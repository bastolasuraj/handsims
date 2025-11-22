<style>
    .accordion-button {
        transition: all 0.3s ease;
    }
    .accordion-button:hover {
        background: linear-gradient(to right, #f0f9ff, #e0f2fe);
    }
    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }
    .accordion-content.active {
        max-height: 2000px;
    }
    .table-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
</style>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl shadow-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2 flex items-center">
                    <i class="fas fa-database mr-3"></i>
                    Database Viewer
                </h1>
                <p class="text-white/90">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Local Users Only - View all database tables and contents
                </p>
            </div>
            <div class="text-right">
                <div class="text-sm text-white/80">Total Tables</div>
                <div class="text-4xl font-bold"><?php echo count($tables); ?></div>
            </div>
        </div>
    </div>

    <!-- Warning Notice -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Read-Only View:</strong> This is a read-only database viewer. 
                    No modifications can be made through this interface. 
                    Showing first 100 rows per table.
                </p>
            </div>
        </div>
    </div>

    <!-- Database Statistics -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
            Database Statistics
        </h2>
        <div class="table-stats">
            <?php 
            $totalRows = 0;
            foreach ($tables as $table) {
                $totalRows += $table['count'];
            }
            ?>
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-4 rounded-lg border border-blue-200">
                <div class="text-sm text-gray-600">Total Tables</div>
                <div class="text-2xl font-bold text-blue-600"><?php echo count($tables); ?></div>
            </div>
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
                <div class="text-sm text-gray-600">Total Rows</div>
                <div class="text-2xl font-bold text-green-600"><?php echo number_format($totalRows); ?></div>
            </div>
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-lg border border-purple-200">
                <div class="text-sm text-gray-600">Database</div>
                <div class="text-2xl font-bold text-purple-600"><?php echo DB_NAME; ?></div>
            </div>
        </div>
    </div>

    <!-- Tables Accordion -->
    <div class="space-y-4">
        <?php foreach ($tables as $index => $table): ?>
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Accordion Header -->
            <button onclick="toggleAccordion(<?php echo $index; ?>)" 
                    class="accordion-button w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-3 rounded-lg">
                        <i class="fas fa-table text-white"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($table['name']); ?></h3>
                        <p class="text-sm text-gray-500">
                            <?php echo count($table['structure']); ?> columns • 
                            <?php echo number_format($table['count']); ?> rows
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                        <?php echo number_format($table['count']); ?> rows
                    </span>
                    <i class="fas fa-chevron-down text-gray-400 transform transition-transform duration-300" id="icon-<?php echo $index; ?>"></i>
                </div>
            </button>

            <!-- Accordion Content -->
            <div id="accordion-<?php echo $index; ?>" class="accordion-content">
                <div class="p-6 border-t border-gray-200">
                    <!-- Table Structure -->
                    <div class="mb-6">
                        <h4 class="text-md font-bold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-columns mr-2 text-purple-500"></i>
                            Table Structure
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Field</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Type</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Null</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Key</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Default</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-700">Extra</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($table['structure'] as $column): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 font-mono text-blue-600"><?php echo htmlspecialchars($column['Field']); ?></td>
                                        <td class="px-4 py-2 font-mono text-gray-600"><?php echo htmlspecialchars($column['Type']); ?></td>
                                        <td class="px-4 py-2"><?php echo $column['Null'] === 'YES' ? '✓' : '✗'; ?></td>
                                        <td class="px-4 py-2">
                                            <?php if ($column['Key'] === 'PRI'): ?>
                                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">PRI</span>
                                            <?php elseif ($column['Key'] === 'MUL'): ?>
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">MUL</span>
                                            <?php elseif ($column['Key'] === 'UNI'): ?>
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">UNI</span>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2 text-gray-600"><?php echo $column['Default'] ?? '-'; ?></td>
                                        <td class="px-4 py-2 text-gray-500"><?php echo $column['Extra'] ?: '-'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Table Data -->
                    <?php if (!empty($table['data'])): ?>
                    <div>
                        <h4 class="text-md font-bold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-list mr-2 text-green-500"></i>
                            Table Data (First 100 rows)
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gradient-to-r from-gray-100 to-gray-200">
                                    <tr>
                                        <?php foreach (array_keys($table['data'][0]) as $column): ?>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-700 whitespace-nowrap">
                                            <?php echo htmlspecialchars($column); ?>
                                        </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($table['data'] as $row): ?>
                                    <tr class="hover:bg-blue-50">
                                        <?php foreach ($row as $value): ?>
                                        <td class="px-4 py-2 text-gray-700 max-w-xs truncate" title="<?php echo htmlspecialchars($value ?? ''); ?>">
                                            <?php 
                                            if ($value === null) {
                                                echo '<span class="text-gray-400 italic">NULL</span>';
                                            } elseif ($value === '') {
                                                echo '<span class="text-gray-400 italic">empty</span>';
                                            } else {
                                                echo htmlspecialchars(strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value);
                                            }
                                            ?>
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if ($table['count'] > 100): ?>
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-600">
                                Showing 100 of <?php echo number_format($table['count']); ?> rows
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500">No data in this table</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function toggleAccordion(index) {
    const content = document.getElementById('accordion-' + index);
    const icon = document.getElementById('icon-' + index);
    
    // Close all other accordions
    document.querySelectorAll('.accordion-content').forEach((el, i) => {
        if (i !== index) {
            el.classList.remove('active');
            document.getElementById('icon-' + i).style.transform = 'rotate(0deg)';
        }
    });
    
    // Toggle current accordion
    content.classList.toggle('active');
    
    if (content.classList.contains('active')) {
        icon.style.transform = 'rotate(180deg)';
    } else {
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>
