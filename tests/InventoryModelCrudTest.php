<?php

namespace Tests;

use App\Models\InventoryModel;
use App\Models\TransactionModel;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\InMemoryPDO;

class InventoryModelCrudTest extends TestCase
{
    private function seedDb(): InMemoryPDO
    {
        return new InMemoryPDO([
            'categories' => [
                ['id' => 1, 'name' => 'Safety', 'description' => ''],
            ],
            'types' => [
                ['id' => 1, 'name' => 'Helmet', 'category_id' => 1, 'description' => ''],
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
            'product_sizes' => [
                ['id' => 1, 'size' => 'S'],
                ['id' => 2, 'size' => 'M'],
            ],
            'locations' => [
                ['id' => 1, 'name' => 'Main', 'description' => ''],
                ['id' => 2, 'name' => 'Overflow', 'description' => ''],
            ],
            'inventory' => [
                ['id' => 1, 'product_id' => 1, 'location_id' => 1, 'size_id' => 1, 'quantity' => 3, 'min_quantity' => 1, 'remarks' => ''],
                ['id' => 2, 'product_id' => 1, 'location_id' => 2, 'size_id' => 2, 'quantity' => 1, 'min_quantity' => 0, 'remarks' => ''],
            ],
            'stock_transactions' => [],
        ]);
    }

    public function testUpdateStockAddsOrAccumulatesQuantities(): void
    {
        $pdo = $this->seedDb();
        $model = new InventoryModel($pdo);

        $model->updateStock(1, 1, 1, 2, 'add');
        $existing = $pdo->tables['inventory'][0]['quantity'];
        $this->assertSame(5, $existing, 'Existing row should increase quantity');

        $model->updateStock(1, 1, 2, 4, 'add');
        $this->assertCount(3, $pdo->tables['inventory'], 'New combination should create a row');
    }

    public function testSubtractStockValidatesAvailabilityAndUpdates(): void
    {
        $pdo = $this->seedDb();
        $model = new InventoryModel($pdo);

        $insufficient = $model->subtractStock(1, 1, 1, 5);
        $this->assertFalse($insufficient['ok']);
        $this->assertSame(3, $insufficient['available']);

        $ok = $model->subtractStock(1, 1, 1, 2);
        $this->assertTrue($ok['ok']);
        $this->assertSame(1, $pdo->tables['inventory'][0]['quantity'], 'Quantity should decrease after subtraction');
    }

    public function testLowStockDetectionAndTotals(): void
    {
        $pdo = $this->seedDb();
        $model = new InventoryModel($pdo);

        $detailed = $model->getLowStockItemsDetailed();
        $this->assertNotEmpty($detailed, 'Low stock items should surface when below threshold');

        $total = $model->getTotalStockQuantity();
        $this->assertSame(4, $total);
    }

    public function testGetStockByLocationIncludesJoinedFields(): void
    {
        $pdo = $this->seedDb();
        $model = new InventoryModel($pdo);

        $all = $model->getStockByLocation();
        $this->assertCount(2, $all);
        $this->assertSame('Main', $all[0]['location_name']);

        $filtered = $model->getStockByLocation(2);
        $this->assertCount(1, $filtered);
        $this->assertSame(2, $filtered[0]['location_id']);
    }

    public function testTransactionsRecordInsertAlongsideInventory(): void
    {
        $pdo = $this->seedDb();
        $inventory = new InventoryModel($pdo);
        $transactions = new TransactionModel($pdo);

        $inventory->updateStock(1, 1, 1, 1, 'add');
        $transactions->addTransaction([
            'transaction_type' => 'IN',
            'product_id' => 1,
            'location_id' => 1,
            'department_id' => 0,
            'size_id' => 1,
            'quantity' => 1,
            'user_id' => 99,
            'remarks' => 'restock',
            'seller_id' => null,
            'price_per_unit' => null,
        ]);

        $this->assertCount(1, $pdo->tables['stock_transactions']);
        $this->assertSame(5, $inventory->getTotalStockQuantity(), 'Stock total reflects transaction update');
    }
}
