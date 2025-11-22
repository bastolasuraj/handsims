<?php if ($category === 'error') : ?>
<!-- Error Log Table -->
<div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
    <div class="bg-gradient-to-r from-red-500 to-orange-600 px-6 py-4">
        <h2 class="text-lg font-semibold text-white">
            <i class="fas fa-bug mr-2"></i> PHP Error Log
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($error_logs as $log) : ?>
                    <?php
                    $errorClass = 'bg-gray-100 text-gray-800';
                    if (strpos($log['type'], 'Exception') !== false) {
                        $errorClass = 'bg-red-100 text-red-800';
                    } elseif (strpos($log['type'], 'Warning') !== false) {
                        $errorClass = 'bg-yellow-100 text-yellow-800';
                    }
                    ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($log['timestamp']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $errorClass; ?>">
                            <?php echo htmlspecialchars($log['type']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($log['message']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo htmlspecialchars($log['file']); ?> on line <?php echo htmlspecialchars($log['line']); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else : ?>
<!-- Search and Filter -->
<div id="log-search-filter-area" class="bg-gradient-to-br from-white to-blue-50 shadow-xl rounded-xl p-6 border border-blue-100">
    <form method="GET" action="<?php echo APP_URL; ?>/activity-logs" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2 group">
                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-search text-blue-500 mr-1"></i> Search
                </label>
                <div class="relative">
                    <input type="text" id="log-search" name="search" value="<?php echo htmlspecialchars($search); ?>"
                           placeholder="Search in logs..."
                           class="block w-full px-4 py-3 pl-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 text-sm font-medium hover:border-blue-300">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div class="group">
                <label for="date_from" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar text-green-500 mr-1"></i> From Date
                </label>
                <input type="date" id="log-date_from" name="date_from" value="<?php echo $date_from; ?>"
                       class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-200 text-sm font-medium hover:border-green-300">
            </div>
            <div class="group">
                <label for="date_to" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-check text-purple-500 mr-1"></i> To Date
                </label>
                <input type="date" id="log-date_to" name="date_to" value="<?php echo $date_to; ?>"
                       class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 text-sm font-medium hover:border-purple-300">
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <button type="button" id="log-clear-filters" 
               class="bg-gradient-to-r from-gray-200 to-gray-300 hover:from-gray-300 hover:to-gray-400 text-gray-700 font-semibold py-2.5 px-5 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                <i class="fas fa-times mr-2"></i> Clear Filters
            </button>
            <button type="submit" id="log-search-submit"
                    class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-2.5 px-6 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="fas fa-search mr-2"></i> Search
            </button>
        </div>
    </form>
</div>
<!-- Terminal-Style Log Display -->
<div class="log-terminal rounded-xl p-6 text-green-400 overflow-hidden shadow-2xl border border-gray-700">
    <div class="mb-4 text-green-300 font-bold">
        <div class="flex items-center mb-2">
            <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
            <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
            <div class="w-3 h-3 rounded-full bg-green-500 mr-4"></div>
            <span class="text-xs text-gray-400">H&S INVENTORY MANAGEMENT SYSTEM - ACTIVITY LOG</span>
        </div>
        <div class="text-xs text-gray-500">
            Generated: <?php echo date('Y-m-d H:i:s'); ?><br>
            <?php echo str_repeat("═", 80); ?>
        </div>
    </div>
    
    <div class="overflow-y-auto custom-scrollbar" style="max-height: 400px;">
        <?php foreach ($formatted_logs as $log) : ?>
        <div class="log-line mb-1 px-4 py-1 rounded text-sm transition-all duration-200 cursor-pointer">
            <span class="text-green-400"><?php echo htmlspecialchars($log); ?></span>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($formatted_logs)) : ?>
        <div class="text-gray-500 text-center py-8">
            <i class="fas fa-inbox text-4xl mb-3"></i>
            <p>No logs found matching your criteria.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Detailed Log Table -->
<div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
        <h2 class="text-lg font-semibold text-white">
            <i class="fas fa-table mr-2"></i> Detailed View
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <i class="fas fa-clock text-blue-500 mr-1"></i> Timestamp
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <i class="fas fa-user text-purple-500 mr-1"></i> User
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <i class="fas fa-bolt text-yellow-500 mr-1"></i> Action
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <i class="fas fa-info-circle text-green-500 mr-1"></i> Details
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <i class="fas fa-network-wired text-red-500 mr-1"></i> IP
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($logs as $log) : ?>
                <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-all duration-200">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                            <div>
                                <div class="font-medium"><?php echo date('M d, Y', strtotime($log['created_at'])); ?></div>
                                <div class="text-xs text-gray-500"><?php echo date('H:i:s', strtotime($log['created_at'])); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold text-xs mr-3">
                                <?php echo strtoupper(substr($log['username'] ?? '?', 0, 1)); ?>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($log['username'] ?? 'N/A'); ?></div>
                                <?php if (isset($log['full_name']) && $log['full_name']) : ?>
                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($log['full_name']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $actionClass = 'bg-blue-100 text-blue-800';
                        if (strpos(strtolower($log['action']), 'delete') !== false || strpos(strtolower($log['action']), 'remove') !== false) {
                            $actionClass = 'bg-red-100 text-red-800';
                        } elseif (strpos(strtolower($log['action']), 'add') !== false || strpos(strtolower($log['action']), 'create') !== false) {
                            $actionClass = 'bg-green-100 text-green-800';
                        } elseif (strpos(strtolower($log['action']), 'update') !== false || strpos(strtolower($log['action']), 'edit') !== false) {
                            $actionClass = 'bg-yellow-100 text-yellow-800';
                        }
                        ?>
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $actionClass; ?>">
                            <?php echo htmlspecialchars($log['action']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <?php if ($log['details']) : ?>
                            <div class="max-w-xs truncate" title="<?php echo htmlspecialchars($log['details']); ?>">
                                <i class="fas fa-comment-dots text-gray-400 mr-1"></i>
                                <?php echo htmlspecialchars($log['details']); ?>
                            </div>
                        <?php else : ?>
                            <span class="text-gray-400">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-gray-100 text-gray-800">
                            <i class="fas fa-network-wired text-gray-500 mr-1 text-xs"></i>
                            <?php echo htmlspecialchars($log['ip'] ?? ''); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($logs)) : ?>
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                        <p>No logs found</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-700">
                <span class="font-medium">Page <?php echo $page; ?></span>
                <?php if (count($logs) > 0) : ?>
                <span class="text-gray-500 ml-2">
                    (Showing <?php echo count($logs); ?> entries)
                </span>
                <?php endif; ?>
            </div>
            <div class="flex space-x-2">
                <?php if ($page > 1) : ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>" 
                   class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-white hover:shadow-md transition-all duration-200 text-sm font-medium">
                    <i class="fas fa-chevron-left mr-1"></i> Previous
                </a>
                <?php endif; ?>
                
                <?php if (count($logs) == 50) : ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>" 
                   class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-white hover:shadow-md transition-all duration-200 text-sm font-medium">
                    Next <i class="fas fa-chevron-right ml-1"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>