<?php

namespace Tests;

use App\Models\ProductModel;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\InMemoryPDO;

class ProductModelCrudTest extends TestCase
{
    private function seedDb(): InMemoryPDO
    {
        return new InMemoryPDO([
            'categories' => [
                ['id' => 1, 'name' => 'Safety', 'description' => 'PPE'],
            ],
            'types' => [
                ['id' => 1, 'name' => 'Helmet', 'category_id' => 1, 'description' => ''],
            ],
            'product_sizes' => [
                ['id' => 1, 'size' => 'S'],
                ['id' => 2, 'size' => 'M'],
            ],
            'products' => [
                [
                    'id' => 1,
                    'part_number' => 'P-100',
                    'product_type' => 'Helmet',
                    'category_id' => 1,
                    'type_id' => 1,
                    'description' => 'Head protection',
                    'barcode' => null,
                    'qr_code' => null,
                    'low_stock_threshold' => 5,
                    'available_sizes' => '1,2',
                    'deleted_at' => null,
                ],
            ],
            'inventory' => [
                ['id' => 1, 'product_id' => 1, 'location_id' => 1, 'size_id' => 1, 'quantity' => 7, 'min_quantity' => 1],
                ['id' => 2, 'product_id' => 1, 'location_id' => 2, 'size_id' => 2, 'quantity' => 3, 'min_quantity' => 0],
            ],
            'locations' => [
                ['id' => 1, 'name' => 'Main', 'description' => ''],
                ['id' => 2, 'name' => 'Overflow', 'description' => ''],
            ],
        ]);
    }

    public function testCreateUpdateAndSoftDeleteProduct(): void
    {
        $pdo = $this->seedDb();
        $model = new ProductModel($pdo);

        $model->create([
            'part_number' => 'P-200',
            'type_id' => 1,
            'category_id' => 1,
            'description' => 'New item',
            'low_stock_threshold' => 2,
            'qr_code' => 'qr',
            'available_sizes' => '1,2',
            'product_type' => 'Should be stripped',
        ]);
        $newId = $pdo->lastInsertId();
        $this->assertNotEmpty($model->getById($newId));

        $model->update($newId, [
            'part_number' => 'P-200-EDIT',
            'type_id' => 1,
            'category_id' => 1,
            'description' => 'Updated',
            'low_stock_threshold' => 4,
            'qr_code' => 'qr2',
            'available_sizes' => '1',
        ]);
        $updated = $model->getById($newId);
        $this->assertSame('P-200-EDIT', $updated['part_number']);
        $this->assertSame('1', $updated['available_sizes']);

        $model->delete($newId);
        $results = $model->searchByPartNumber('P-200');
        $this->assertSame([], $results, 'Soft-deleted products should be excluded from search');
    }

    public function testAvailableSizesAndInventoryDerivedData(): void
    {
        $pdo = $this->seedDb();
        $model = new ProductModel($pdo);

        $sizes = $model->getAvailableSizes(1);
        $this->assertCount(2, $sizes);
        $this->assertSame('S', $sizes[0]['size']);

        $details = $model->getProductDetails(1);
        $this->assertSame(10, $details['total_stock'], 'Total stock should sum inventory rows');

        $inventory = $model->getProductInventory(1);
        $this->assertCount(2, $inventory);
        $this->assertSame('Main', $inventory[0]['location_name']);
    }

    public function testSearchMatchesDescriptionAndPartNumber(): void
    {
        $pdo = $this->seedDb();
        $model = new ProductModel($pdo);

        $matchesPart = $model->searchByPartNumber('P-100');
        $this->assertNotEmpty($matchesPart);

        $matchesDescription = $model->searchByPartNumber('Head');
        $this->assertNotEmpty($matchesDescription);
    }
}
