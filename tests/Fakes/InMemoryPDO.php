<?php

namespace Tests\Fakes;

use RuntimeException;

/**
 * Lightweight in-memory stand-in for PDO that understands the small
 * subset of SQL used by our models/controllers. It lets us exercise
 * CRUD logic without a real MySQL connection.
 */
class InMemoryPDO
{
    public array $tables = [];
    private array $autoIds = [];
    private bool $inTransaction = false;
    private int $lastInsertId = 0;

    public function __construct(array $seedTables = [])
    {
        $defaults = [
            'categories' => [],
            'product_sizes' => [],
            'locations' => [],
            'departments' => [],
            'sellers' => [],
            'types' => [],
            'products' => [],
            'inventory' => [],
            'stock_transactions' => [],
        ];

        $this->tables = array_merge($defaults, $seedTables);
        foreach ($this->tables as $table => $rows) {
            $this->autoIds[$table] = $rows ? (int)max(array_column($rows, 'id')) : 0;
        }
    }

    public function prepare(string $sql): InMemoryStatement
    {
        return new InMemoryStatement($this, $sql);
    }

    public function query(string $sql)
    {
        // Only handful of tests rely on simple counts; map query->prepare()->execute().
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public function lastInsertId(): int
    {
        return $this->lastInsertId;
    }

    public function setLastInsertId(int $id): void
    {
        $this->lastInsertId = $id;
    }

    public function nextId(string $table): int
    {
        $this->autoIds[$table] = ($this->autoIds[$table] ?? 0) + 1;
        $this->lastInsertId = $this->autoIds[$table];
        return $this->autoIds[$table];
    }

    public function beginTransaction(): void
    {
        $this->inTransaction = true;
    }

    public function commit(): void
    {
        $this->inTransaction = false;
    }

    public function rollBack(): void
    {
        $this->inTransaction = false;
    }

    public function inTransaction(): bool
    {
        return $this->inTransaction;
    }
}

class InMemoryStatement
{
    private array $result = [];
    private bool $executed = false;

    public function __construct(private InMemoryPDO $pdo, private string $sql)
    {
    }

    public function execute(array $params = []): bool
    {
        $sql = $this->normalise($this->sql);
        $tables =& $this->pdo->tables;

        // Categories CRUD
        if (str_starts_with($sql, 'select * from categories where id')) {
            $id = (int)($params['id'] ?? 0);
            $row = $this->findById($tables['categories'], $id);
            $this->result = $row ? [$row] : [];
        } elseif (str_starts_with($sql, 'select * from categories order by name')) {
            $this->result = array_values($tables['categories']);
        } elseif (str_starts_with($sql, 'insert into categories')) {
            $id = $this->pdo->nextId('categories');
            $tables['categories'][] = [
                'id' => $id,
                'name' => $params['name'] ?? '',
                'description' => $params['description'] ?? null,
            ];
        } elseif (str_starts_with($sql, 'update categories set')) {
            $id = (int)$params['id'];
            $this->updateRow($tables['categories'], $id, [
                'name' => $params['name'] ?? '',
                'description' => $params['description'] ?? null,
            ]);
        } elseif (str_starts_with($sql, 'select count(*) as count from products where category_id')) {
            $count = $this->countWhere($tables['products'], fn($row) => (int)$row['category_id'] === (int)$params['id']);
            $this->result = [['count' => $count]];
        } elseif (str_starts_with($sql, 'delete from categories')) {
            $this->deleteById($tables['categories'], (int)$params['id']);

        // Sizes CRUD
        } elseif (str_starts_with($sql, 'select * from product_sizes where id')) {
            $row = $this->findById($tables['product_sizes'], (int)$params['id']);
            $this->result = $row ? [$row] : [];
        } elseif (str_starts_with($sql, 'select * from product_sizes order by id')) {
            $this->result = array_values($tables['product_sizes']);
        } elseif (str_starts_with($sql, 'insert into product_sizes')) {
            $id = $this->pdo->nextId('product_sizes');
            $tables['product_sizes'][] = ['id' => $id, 'size' => $params['size'] ?? ''];
        } elseif (str_starts_with($sql, 'update product_sizes set')) {
            $this->updateRow($tables['product_sizes'], (int)$params['id'], ['size' => $params['size'] ?? '']);
        } elseif (str_starts_with($sql, 'select count(*) as count from inventory where size_id')) {
            $count = $this->countWhere($tables['inventory'], fn($row) => (int)$row['size_id'] === (int)$params['id']);
            $this->result = [['count' => $count]];
        } elseif (str_starts_with($sql, 'select count(*) as count from stock_transactions where size_id')) {
            $count = $this->countWhere($tables['stock_transactions'], fn($row) => (int)($row['size_id'] ?? 0) === (int)$params['id']);
            $this->result = [['count' => $count]];
        } elseif (str_starts_with($sql, 'delete from product_sizes')) {
            $this->deleteById($tables['product_sizes'], (int)$params['id']);

        // Locations CRUD
        } elseif (str_starts_with($sql, 'select * from locations where id')) {
            $row = $this->findById($tables['locations'], (int)$params['id']);
            $this->result = $row ? [$row] : [];
        } elseif (str_starts_with($sql, 'select * from locations order by name')) {
            $this->result = array_values($tables['locations']);
        } elseif (str_starts_with($sql, 'insert into locations')) {
            $id = $this->pdo->nextId('locations');
            $tables['locations'][] = [
                'id' => $id,
                'name' => $params['name'] ?? '',
                'description' => $params['description'] ?? null,
            ];
        } elseif (str_starts_with($sql, 'update locations set')) {
            $this->updateRow($tables['locations'], (int)$params['id'], [
                'name' => $params['name'] ?? '',
                'description' => $params['description'] ?? null,
            ]);
        } elseif (str_starts_with($sql, 'select count(*) as count from inventory where location_id')) {
            $count = $this->countWhere($tables['inventory'], fn($row) => (int)$row['location_id'] === (int)$params['id']);
            $this->result = [['count' => $count]];
        } elseif (str_starts_with($sql, 'select count(*) as count from stock_transactions where location_id')) {
            $count = $this->countWhere($tables['stock_transactions'], fn($row) => (int)($row['location_id'] ?? 0) === (int)$params['id']);
            $this->result = [['count' => $count]];
        } elseif (str_starts_with($sql, 'delete from locations')) {
            $this->deleteById($tables['locations'], (int)$params['id']);

        // Departments CRUD
        } elseif (str_starts_with($sql, 'select * from departments where id')) {
            $row = $this->findById($tables['departments'], (int)$params['id']);
            $this->result = $row ? [$row] : [];
        } elseif (str_starts_with($sql, 'select * from departments order by name')) {
            $this->result = array_values($tables['departments']);
        } elseif (str_starts_with($sql, 'insert into departments')) {
            $id = $this->pdo->nextId('departments');
            $tables['departments'][] = [
                'id' => $id,
                'name' => $params['name'] ?? '',
                'description' => $params['description'] ?? null,
            ];
        } elseif (str_starts_with($sql, 'update departments set')) {
            $this->updateRow($tables['departments'], (int)$params['id'], [
                'name' => $params['name'] ?? '',
                'description' => $params['description'] ?? null,
            ]);
        } elseif (str_starts_with($sql, 'select count(*) as count from stock_transactions where department_id')) {
            $count = $this->countWhere($tables['stock_transactions'], fn($row) => (int)($row['department_id'] ?? 0) === (int)$params['id']);
            $this->result = [['count' => $count]];
        } elseif (str_starts_with($sql, 'delete from departments')) {
            $this->deleteById($tables['departments'], (int)$params['id']);

        // Sellers CRUD
        } elseif (str_starts_with($sql, 'select * from sellers where id')) {
            $row = $this->findById($tables['sellers'], (int)$params['id']);
            $this->result = $row ? [$row] : [];
        } elseif (str_starts_with($sql, 'select * from sellers order by name')) {
            $this->result = array_values($tables['sellers']);
        } elseif (str_starts_with($sql, 'insert into sellers')) {
            $id = $this->pdo->nextId('sellers');
            $tables['sellers'][] = [
                'id' => $id,
                'name' => $params['name'] ?? '',
                'contact_person' => $params['contact_person'] ?? null,
                'phone' => $params['phone'] ?? null,
                'email' => $params['email'] ?? null,
                'address' => $params['address'] ?? null,
            ];
        } elseif (str_starts_with($sql, 'update sellers set')) {
            $this->updateRow($tables['sellers'], (int)$params['id'], [
                'name' => $params['name'] ?? '',
                'contact_person' => $params['contact_person'] ?? null,
                'phone' => $params['phone'] ?? null,
                'email' => $params['email'] ?? null,
                'address' => $params['address'] ?? null,
            ]);
        } elseif (str_starts_with($sql, 'select count(*) as count from stock_transactions where seller_id')) {
            $count = $this->countWhere($tables['stock_transactions'], fn($row) => (int)($row['seller_id'] ?? 0) === (int)$params['id']);
            $this->result = [['count' => $count]];
        } elseif (str_starts_with($sql, 'delete from sellers')) {
            $this->deleteById($tables['sellers'], (int)$params['id']);

        // Types
        } elseif (str_starts_with($sql, 'insert into types')) {
            $id = $this->pdo->nextId('types');
            $tables['types'][] = [
                'id' => $id,
                'name' => $params['name'] ?? '',
                'category_id' => (int)$params['category_id'],
                'description' => $params['description'] ?? null,
            ];
        } elseif (str_starts_with($sql, 'update types set')) {
            $this->updateRow($tables['types'], (int)$params['id'], [
                'name' => $params['name'] ?? '',
                'category_id' => (int)$params['category_id'],
                'description' => $params['description'] ?? null,
            ]);
        } elseif (str_starts_with($sql, 'select * from types where name')) {
            $name = $params['name'] ?? '';
            $row = $this->firstWhere($tables['types'], fn($r) => $r['name'] === $name);
            $this->result = $row ? [$row] : [];
        } elseif (str_starts_with($sql, 'select count(*) as count from types')) {
            $this->result = [['count' => count($tables['types'])]];
        } elseif (str_starts_with($sql, 'select t.*, c.name as category_name from types t')) {
            $rows = [];
            foreach ($tables['types'] as $type) {
                $cat = $this->findById($tables['categories'], (int)$type['category_id']);
                $rows[] = $type + ['category_name' => $cat['name'] ?? null];
            }
            if (str_contains($sql, 'where t.id')) {
                $rows = array_values(array_filter($rows, fn($r) => (int)$r['id'] === (int)$params['id']));
            }
            $this->result = $rows;
        } elseif (str_starts_with($sql, 'select * from types where category_id')) {
            $catId = (int)$params['category_id'];
            $this->result = array_values(array_filter($tables['types'], fn($r) => (int)$r['category_id'] === $catId));
        } elseif (str_starts_with($sql, 'select count(*) as count from products where type_id')) {
            $count = $this->countWhere($tables['products'], fn($row) => (int)$row['type_id'] === (int)$params['id']);
            $this->result = [['count' => $count]];
        } elseif (str_starts_with($sql, 'delete from types')) {
            $this->deleteById($tables['types'], (int)$params['id']);

        // Products
        } elseif (str_starts_with($sql, 'select p.id, p.part_number')) {
            // getAll / search results
            $rows = [];
            foreach ($tables['products'] as $product) {
                if (!empty($product['deleted_at'])) {
                    continue;
                }
                $category = $this->findById($tables['categories'], (int)$product['category_id']);
                $type = $this->findById($tables['types'], (int)$product['type_id']);
                $rows[] = $product + [
                    'category_name' => $category['name'] ?? null,
                    'type_name' => $type['name'] ?? null,
                ];
            }

            if (str_contains($sql, 'p.part_number like')) {
                $term = trim($params['term'] ?? '', '%');
                $rows = array_values(array_filter($rows, function ($row) use ($term) {
                    return str_contains($row['part_number'], $term) || str_contains($row['description'] ?? '', $term);
                }));
            }
            $this->result = $rows;
        } elseif (str_starts_with($sql, 'select p.*, c.name as category_name')) {
            $id = (int)$params['id'];
            $product = $this->findById($tables['products'], $id);
            if ($product) {
                $cat = $this->findById($tables['categories'], (int)$product['category_id']);
                $type = $this->findById($tables['types'], (int)$product['type_id']);
                $totalStock = $this->sumWhere($tables['inventory'], fn($r) => (int)$r['product_id'] === $id, 'quantity');
                $this->result = [$product + [
                    'category_name' => $cat['name'] ?? null,
                    'type_name' => $type['name'] ?? null,
                    'total_stock' => $totalStock,
                ]];
            }
        } elseif (str_starts_with($sql, 'select i.quantity, i.min_quantity')) {
            $productId = (int)$params['product_id'];
            $rows = [];
            foreach ($tables['inventory'] as $inventory) {
                if ((int)$inventory['product_id'] !== $productId) {
                    continue;
                }
                $location = $this->findById($tables['locations'], (int)$inventory['location_id']);
                $size = $this->findById($tables['product_sizes'], (int)$inventory['size_id']);
                $rows[] = [
                    'quantity' => $inventory['quantity'],
                    'min_quantity' => $inventory['min_quantity'] ?? 0,
                    'last_updated' => $inventory['last_updated'] ?? null,
                    'location_name' => $location['name'] ?? null,
                    'size' => $size['size'] ?? null,
                ];
            }
            $this->result = $rows;
        } elseif (str_starts_with($sql, 'select available_sizes from products')) {
            $product = $this->findById($tables['products'], (int)$params['id']);
            $this->result = $product ? [['available_sizes' => $product['available_sizes'] ?? '']] : [];
        } elseif (str_starts_with($sql, 'select id, size from product_sizes where id in')) {
            // $params is numeric array of size IDs
            $ids = array_map('intval', $params);
            $rows = array_values(array_filter($tables['product_sizes'], fn($row) => in_array((int)$row['id'], $ids, true)));
            $this->result = array_map(fn($r) => ['id' => $r['id'], 'size' => $r['size']], $rows);
        } elseif (str_starts_with($sql, 'select ps.id, ps.size from inventory')) {
            $productId = (int)$params['product_id'];
            $locationId = (int)$params['location_id'];
            $rows = [];
            foreach ($tables['inventory'] as $inv) {
                if ((int)$inv['product_id'] !== $productId || (int)$inv['location_id'] !== $locationId || (int)$inv['quantity'] <= 0) {
                    continue;
                }
                $size = $this->findById($tables['product_sizes'], (int)$inv['size_id']);
                if ($size) {
                    $rows[] = ['id' => $size['id'], 'size' => $size['size']];
                }
            }
            $this->result = $rows;
        } elseif (str_starts_with($sql, 'insert into products')) {
            $id = $this->pdo->nextId('products');
            $tables['products'][] = [
                'id' => $id,
                'part_number' => $params['part_number'] ?? '',
                'type_id' => (int)$params['type_id'],
                'category_id' => (int)$params['category_id'],
                'description' => $params['description'] ?? null,
                'low_stock_threshold' => (int)($params['low_stock_threshold'] ?? 0),
                'qr_code' => $params['qr_code'] ?? null,
                'available_sizes' => $params['available_sizes'] ?? '',
                'product_type' => $params['product_type'] ?? '',
                'deleted_at' => null,
            ];
        } elseif (str_starts_with($sql, 'update products set deleted_at')) {
            $id = (int)$params['id'];
            $this->updateRow($tables['products'], $id, ['deleted_at' => 'now']);
        } elseif (str_starts_with($sql, 'update products set')) {
            $id = (int)$params['id'];
            $this->updateRow($tables['products'], $id, [
                'part_number' => $params['part_number'] ?? '',
                'type_id' => (int)($params['type_id'] ?? 0),
                'category_id' => (int)($params['category_id'] ?? 0),
                'description' => $params['description'] ?? null,
                'low_stock_threshold' => (int)($params['low_stock_threshold'] ?? 0),
                'qr_code' => $params['qr_code'] ?? null,
                'available_sizes' => $params['available_sizes'] ?? '',
            ]);
        } elseif (str_starts_with($sql, 'select * from products where id')) {
            $row = $this->findById($tables['products'], (int)$params['id']);
            $this->result = $row ? [$row] : [];
        } elseif (str_starts_with($sql, 'select p.part_number')) {
            // Transaction history / stock by location share joins. Keep minimal shape.
            $this->result = array_values($tables['stock_transactions']);
        } elseif (str_starts_with($sql, 'select st.transaction_date')) {
            $productId = (int)$params['product_id'];
            $rows = array_values(array_filter($tables['stock_transactions'], fn($r) => (int)$r['product_id'] === $productId));
            $this->result = $rows;
        } elseif (str_starts_with($sql, 'insert into stock_transactions')) {
            $id = $this->pdo->nextId('stock_transactions');
            $tables['stock_transactions'][] = ['id' => $id] + $params;

        // Inventory
        } elseif (str_starts_with($sql, 'insert into inventory')) {
            $productId = (int)$params['product_id'];
            $locationId = (int)$params['location_id'];
            $sizeId = $params['size_id'] === '' ? null : (int)$params['size_id'];
            $quantity = (int)$params['quantity'];
            $existingKey = $this->findInventoryIndex($tables['inventory'], $productId, $locationId, $sizeId);
            if ($existingKey !== null) {
                $tables['inventory'][$existingKey]['quantity'] += $quantity;
            } else {
                $tables['inventory'][] = [
                    'id' => $this->pdo->nextId('inventory'),
                    'product_id' => $productId,
                    'location_id' => $locationId,
                    'size_id' => $sizeId,
                    'quantity' => $quantity,
                    'min_quantity' => $params['min_quantity'] ?? 0,
                    'remarks' => $params['remarks'] ?? null,
                ];
            }
        } elseif (str_starts_with($sql, 'select id, quantity from inventory')) {
            $productId = (int)$params['p'];
            $locationId = (int)$params['l'];
            $sizeId = $params['s'] ?? null;
            $sizeId = $sizeId === null ? null : (int)$sizeId;
            $idx = $this->findInventoryIndex($tables['inventory'], $productId, $locationId, $sizeId);
            if ($idx === null) {
                $this->result = [];
            } else {
                $row = $tables['inventory'][$idx];
                $this->result = [['id' => $row['id'], 'quantity' => $row['quantity']]];
            }
        } elseif (str_starts_with($sql, 'update inventory set quantity = quantity -')) {
            foreach ($tables['inventory'] as &$inv) {
                if ((int)$inv['id'] === (int)$params['id']) {
                    $inv['quantity'] -= (int)$params['q'];
                    break;
                }
            }
        } elseif (str_contains($sql, 'where i.quantity <= p.low_stock_threshold')) {
            $rows = [];
            foreach ($tables['inventory'] as $inv) {
                $product = $this->findById($tables['products'], (int)$inv['product_id']);
                $location = $this->findById($tables['locations'], (int)$inv['location_id']);
                $size = $this->findById($tables['product_sizes'], (int)$inv['size_id']);
                $type = $this->findById($tables['types'], (int)($product['type_id'] ?? 0));
                if (($product['low_stock_threshold'] ?? 0) >= $inv['quantity']) {
                    $rows[] = [
                        'product_id' => $inv['product_id'],
                        'location_id' => $inv['location_id'],
                        'size_id' => $inv['size_id'],
                        'part_number' => $product['part_number'] ?? null,
                        'product_type' => $type['name'] ?? null,
                        'location_name' => $location['name'] ?? null,
                        'size' => $size['size'] ?? null,
                        'quantity' => $inv['quantity'],
                        'min_quantity' => $inv['min_quantity'] ?? 0,
                        'remarks' => $inv['remarks'] ?? null,
                        'last_updated' => $inv['last_updated'] ?? null,
                        'low_stock_threshold' => $product['low_stock_threshold'] ?? 0,
                    ];
                }
            }
            if (array_key_exists('location_id', $params)) {
                $locationId = (int)$params['location_id'];
                $rows = array_values(array_filter($rows, fn($r) => (int)$r['location_id'] === $locationId));
            }
            $this->result = $rows;
        } elseif (str_starts_with($sql, 'select i.product_id, i.location_id')) {
            $locationFilter = array_key_exists('location_id', $params) || str_contains($sql, 'where i.location_id');
            $rows = [];
            foreach ($tables['inventory'] as $inv) {
                if ($locationFilter && (int)$inv['location_id'] !== (int)($params['location_id'] ?? 0)) {
                    continue;
                }
                $product = $this->findById($tables['products'], (int)$inv['product_id']);
                $location = $this->findById($tables['locations'], (int)$inv['location_id']);
                $size = $this->findById($tables['product_sizes'], (int)$inv['size_id']);
                $type = $this->findById($tables['types'], (int)($product['type_id'] ?? 0));
                $category = $this->findById($tables['categories'], (int)($product['category_id'] ?? 0));
                $rows[] = [
                    'product_id' => $inv['product_id'],
                    'location_id' => $inv['location_id'],
                    'size_id' => $inv['size_id'],
                    'part_number' => $product['part_number'] ?? '',
                    'product_type' => $type['name'] ?? null,
                    'description' => $product['description'] ?? null,
                    'low_stock_threshold' => $product['low_stock_threshold'] ?? 0,
                    'deleted_at' => $product['deleted_at'] ?? null,
                    'location_name' => $location['name'] ?? null,
                    'size' => $size['size'] ?? null,
                    'category_name' => $category['name'] ?? null,
                    'quantity' => $inv['quantity'],
                    'min_quantity' => $inv['min_quantity'] ?? 0,
                    'last_updated' => $inv['last_updated'] ?? null,
                ];
            }
            $this->result = $rows;
        } elseif (str_starts_with($sql, 'select sum(quantity) as total_quantity from inventory')) {
            $sum = $this->sumWhere($tables['inventory'], fn() => true, 'quantity');
            $this->result = [['total_quantity' => $sum]];
        } elseif (str_starts_with($sql, 'select i.quantity, i.min_quantity, i.last_updated')) {
            $rows = [];
            foreach ($tables['inventory'] as $inv) {
                if ($inv['quantity'] <= $inv['min_quantity']) {
                    $rows[] = [
                        'product_id' => $inv['product_id'],
                        'part_number' => $this->findById($tables['products'], (int)$inv['product_id'])['part_number'] ?? null,
                        'product_type' => $this->findById($tables['types'], (int)$this->findById($tables['products'], (int)$inv['product_id'])['type_id'] ?? 0)['name'] ?? null,
                        'location_name' => $this->findById($tables['locations'], (int)$inv['location_id'])['name'] ?? null,
                        'size' => $this->findById($tables['product_sizes'], (int)$inv['size_id'])['size'] ?? null,
                        'quantity' => $inv['quantity'],
                        'min_quantity' => $inv['min_quantity'] ?? 0,
                        'remarks' => $inv['remarks'] ?? null,
                        'last_updated' => $inv['last_updated'] ?? null,
                    ];
                }
            }
            $this->result = $rows;
        } elseif (str_starts_with($sql, 'select count(*) as count from categories')) {
            $this->result = [['count' => count($tables['categories'])]];
        } elseif (str_starts_with($sql, 'select count(*) as count from product_sizes')) {
            $this->result = [['count' => count($tables['product_sizes'])]];
        } elseif (str_starts_with($sql, 'select count(*) as count from locations')) {
            $this->result = [['count' => count($tables['locations'])]];
        } elseif (str_starts_with($sql, 'select count(*) as count from departments')) {
            $this->result = [['count' => count($tables['departments'])]];
        } elseif (str_starts_with($sql, 'select count(*) as count from sellers')) {
            $this->result = [['count' => count($tables['sellers'])]];
        } elseif (str_starts_with($sql, 'select count(*) as count from inventory')) {
            $this->result = [['count' => count($tables['inventory'])]];
        } elseif (str_starts_with($sql, 'select available_sizes from products where id')) {
            // Already covered above; kept for clarity.
        } elseif (str_starts_with($sql, 'select count(*) as count from stock_transactions where date(transaction_date)')) {
            // Use entire dataset; tests only need count >0 vs 0.
            $today = (int)date('Ymd');
            $count = $this->countWhere($tables['stock_transactions'], fn() => true);
            $this->result = [['count' => $count, 'today' => $today]];
        } else {
            throw new RuntimeException("Unhandled SQL in fake PDO: {$this->sql}");
        }

        $this->executed = true;
        return true;
    }

    public function fetch()
    {
        return $this->result[0] ?? false;
    }

    public function fetchAll(): array
    {
        return $this->result;
    }

    private function normalise(string $sql): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', $sql)));
    }

    private function findById(array $rows, int $id): ?array
    {
        foreach ($rows as $row) {
            if ((int)$row['id'] === $id) {
                return $row;
            }
        }
        return null;
    }

    private function deleteById(array &$rows, int $id): void
    {
        $rows = array_values(array_filter($rows, fn($row) => (int)$row['id'] !== $id));
    }

    private function updateRow(array &$rows, int $id, array $updates): void
    {
        foreach ($rows as &$row) {
            if ((int)$row['id'] === $id) {
                $row = array_merge($row, $updates);
                return;
            }
        }
    }

    private function firstWhere(array $rows, callable $filter): ?array
    {
        foreach ($rows as $row) {
            if ($filter($row)) {
                return $row;
            }
        }
        return null;
    }

    private function countWhere(array $rows, callable $filter): int
    {
        $count = 0;
        foreach ($rows as $row) {
            if ($filter($row)) {
                $count++;
            }
        }
        return $count;
    }

    private function sumWhere(array $rows, callable $filter, string $field): int
    {
        $sum = 0;
        foreach ($rows as $row) {
            if ($filter($row)) {
                $sum += (int)($row[$field] ?? 0);
            }
        }
        return $sum;
    }

    private function findInventoryIndex(array $rows, int $productId, int $locationId, ?int $sizeId): ?int
    {
        foreach ($rows as $index => $row) {
            $rowSize = $row['size_id'] ?? null;
            if ((int)$row['product_id'] === $productId &&
                (int)$row['location_id'] === $locationId &&
                ((int)$rowSize === (int)$sizeId)) {
                return $index;
            }
        }
        return null;
    }
}
