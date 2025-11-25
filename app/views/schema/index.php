<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Database Schema</h1>
    <?php foreach ($data['schema'] as $table => $columns): ?>
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($table); ?></h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border-b">Column</th>
                            <th class="py-2 px-4 border-b">Data Type</th>
                            <th class="py-2 px-4 border-b">Nullable</th>
                            <th class="py-2 px-4 border-b">Key</th>
                            <th class="py-2 px-4 border-b">Default</th>
                            <th class="py-2 px-4 border-b">Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($columns as $column): ?>
                            <tr>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($column['COLUMN_NAME']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($column['DATA_TYPE']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($column['IS_NULLABLE']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($column['COLUMN_KEY']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($column['COLUMN_DEFAULT'] ?? ''); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($column['EXTRA'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</div>
