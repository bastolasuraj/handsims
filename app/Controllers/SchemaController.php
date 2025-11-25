<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SchemaModel;

class SchemaController extends Controller
{
    public function index()
    {
        $this->requireAuth();

        $schemaModel = new SchemaModel($this->db);
        $tables = $schemaModel->getTables();

        $schema = [];
        foreach ($tables as $table) {
            $schema[$table] = $schemaModel->getTableSchema($table);
        }

        $data = [
            'schema' => $schema
        ];

        $this->view('schema/index', $data);
    }
}
