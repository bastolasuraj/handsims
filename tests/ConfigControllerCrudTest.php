<?php

namespace Tests;

use App\Controllers\ConfigController;
use App\Models\TypeModel;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\InMemoryPDO;

class ConfigControllerCrudTest extends TestCase
{
    private function makeController(InMemoryPDO $pdo): ConfigController
    {
        $controller = $this->getMockBuilder(ConfigController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $ref = new \ReflectionClass($controller);
        $prop = $ref->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($controller, $pdo);

        return $controller;
    }

    private function invokePrivate(object $object, string $method, array $args = [])
    {
        $ref = new \ReflectionClass($object);
        $m = $ref->getMethod($method);
        $m->setAccessible(true);
        return $m->invokeArgs($object, $args);
    }

    private function seedDb(): InMemoryPDO
    {
        return new InMemoryPDO([
            'categories' => [
                ['id' => 1, 'name' => 'Safety', 'description' => 'PPE'],
            ],
            'product_sizes' => [
                ['id' => 1, 'size' => 'S'],
                ['id' => 2, 'size' => 'L'],
            ],
            'locations' => [
                ['id' => 1, 'name' => 'Main', 'description' => 'Primary'],
                ['id' => 2, 'name' => 'Overflow', 'description' => 'Backup'],
            ],
            'departments' => [
                ['id' => 1, 'name' => 'Ops', 'description' => 'Operations'],
            ],
            'sellers' => [
                ['id' => 1, 'name' => 'Vendor A', 'contact_person' => 'Alice', 'phone' => '1', 'email' => 'a@example.com', 'address' => 'HQ'],
                ['id' => 2, 'name' => 'Vendor B', 'contact_person' => 'Bob', 'phone' => '2', 'email' => 'b@example.com', 'address' => 'Branch'],
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
            'inventory' => [
                ['id' => 1, 'product_id' => 1, 'location_id' => 1, 'size_id' => 1, 'quantity' => 3, 'min_quantity' => 1],
            ],
            'stock_transactions' => [
                ['id' => 1, 'size_id' => 1, 'location_id' => 1, 'department_id' => 1, 'seller_id' => 1, 'product_id' => 1],
            ],
        ]);
    }

    public function testCategoryCrudAndValidationGuardsAgainstInvalidData(): void
    {
        $pdo = $this->seedDb();
        $controller = $this->makeController($pdo);

        $errors = $this->invokePrivate($controller, 'validateCategoryData', [['name' => '']]);
        $this->assertNotEmpty($errors, 'Empty names should be rejected');

        $this->invokePrivate($controller, 'addCategory', [['name' => 'Chemicals', 'description' => 'Hazmat']]);
        $newId = $pdo->lastInsertId();
        $created = $this->invokePrivate($controller, 'getCategoryById', [$newId]);
        $this->assertSame('Chemicals', $created['name']);

        $this->invokePrivate($controller, 'updateCategory', [$newId, ['name' => 'Chem', 'description' => 'Lab']]);
        $updated = $this->invokePrivate($controller, 'getCategoryById', [$newId]);
        $this->assertSame('Chem', $updated['name']);

        $cannotDeleteInUse = $this->invokePrivate($controller, 'deleteCategory', [1]);
        $this->assertFalse($cannotDeleteInUse, 'Category referenced by products should not delete');

        $this->invokePrivate($controller, 'deleteCategory', [$newId]);
        $shouldBeGone = $this->invokePrivate($controller, 'getCategoryById', [$newId]);
        $this->assertFalse($shouldBeGone);
    }

    public function testSizeAndLocationDeletionRespectUsage(): void
    {
        $pdo = $this->seedDb();
        $controller = $this->makeController($pdo);

        $this->assertFalse($this->invokePrivate($controller, 'deleteSize', [1]), 'Size in inventory should block deletion');

        $this->assertFalse($this->invokePrivate($controller, 'deleteLocation', [1]), 'Location in inventory should block deletion');
        $this->assertTrue($this->invokePrivate($controller, 'deleteLocation', [2]), 'Unused location should delete');
    }

    public function testSellerDeletionRequiresNoTransactionReferences(): void
    {
        $pdo = $this->seedDb();
        $controller = $this->makeController($pdo);

        $this->assertFalse($this->invokePrivate($controller, 'deleteSeller', [1]), 'Seller referenced by transactions should not delete');
        $this->assertTrue($this->invokePrivate($controller, 'deleteSeller', [2]), 'Unreferenced seller should delete cleanly');
    }

    public function testDepartmentDeletionBlockedWhenTransactionsExist(): void
    {
        $pdo = $this->seedDb();
        $controller = $this->makeController($pdo);

        $this->assertFalse($this->invokePrivate($controller, 'deleteDepartment', [1]));
        $this->invokePrivate($controller, 'addDepartment', [['name' => 'NewDept', 'description' => '']]);
        $newId = $pdo->lastInsertId();
        $this->assertTrue($this->invokePrivate($controller, 'deleteDepartment', [$newId]));
    }

    public function testTypeValidationAndGuardedDeletion(): void
    {
        $pdo = $this->seedDb();
        $controller = $this->makeController($pdo);
        $typeModel = new TypeModel($pdo);

        $errors = $this->invokePrivate($controller, 'validateTypeData', [['name' => '', 'category_id' => 0]]);
        $this->assertNotEmpty($errors, 'Type requires name and category');

        $typeModel->create(['name' => 'Gloves', 'category_id' => 1, 'description' => 'Hand']);
        $createdId = $pdo->lastInsertId();
        $typeModel->update($createdId, ['name' => 'Gloves Updated', 'category_id' => 1, 'description' => 'Changed']);
        $updated = $typeModel->getById($createdId);
        $this->assertSame('Gloves Updated', $updated['name']);

        $this->assertSame(
            "This type is in use by products and cannot be deleted.",
            $typeModel->delete(1),
            'Delete should be rejected when products reference the type'
        );

        // Remove product reference and retry.
        $pdo->tables['products'] = [];
        $this->assertTrue($typeModel->delete(1));
    }
}
