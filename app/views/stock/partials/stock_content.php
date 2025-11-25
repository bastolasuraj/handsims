<!-- Search and Filter -->
<div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b">
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <input type="text" id="searchInput" placeholder="Search by part number, type, or category..." 
                   class="w-full px-4 py-3 pl-12 rounded-lg border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200">
            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>
        <select id="statusFilter" class="px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200">
            <option value="">All Status</option>
            <option value="good">Good Stock</option>
            <option value="low">Low Stock</option>
            <option value="critical">Critical</option>
            <option value="out">Out of Stock</option>
        </select>
    </div>
</div>



<!-- Stock Table -->
<div class="overflow-x-auto overflow-x-hidden">
    <table class="min-w-full" id="stockTable">
        <thead>
            <tr class="bg-gradient-to-r from-gray-100 to-gray-200">
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-300 transition-colors" onclick="sortTable(0)">
                    <div class="flex items-center">
                        <i class="fas fa-barcode mr-2 text-purple-500"></i>
                        Part Number
                        <i class="fas fa-sort ml-2 text-gray-400"></i>
                    </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-300 transition-colors" onclick="sortTable(1)">
                    <div class="flex items-center">
                        <i class="fas fa-tag mr-2 text-blue-500"></i>
                        Part Type
                        <i class="fas fa-sort ml-2 text-gray-400"></i>
                    </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-folder mr-2 text-green-500"></i>
                        Category
                    </div>
                </th>
                <?php if (!$selected_location) : ?>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-warehouse mr-2 text-orange-500"></i>
                        Location
                    </div>
                </th>
                <?php endif; ?>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-ruler mr-2 text-indigo-500"></i>
                        Size
                    </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider cursor-pointer hover:bg-gray-300 transition-colors" onclick="sortTable(<?php echo $selected_location ? 4 : 5; ?>)">
                    <div class="flex items-center">
                        <i class="fas fa-sort-numeric-up mr-2 text-cyan-500"></i>
                        Quantity
                        <i class="fas fa-sort ml-2 text-gray-400"></i>
                    </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-flag mr-2 text-yellow-500"></i>
                        Min Qty
                    </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-traffic-light mr-2 text-red-500"></i>
                        Status
                    </div>
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-tools mr-2 text-gray-500"></i>
                        Actions
                    </div>
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($stock as $index => $item) : ?>
            <tr class="table-hover-row hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 fade-in-row" style="animation-delay: <?php echo $index * 0.05; ?>s">
                <td class="px-6 py-4 whitespace-nowrap">
                    <?php if (!empty($item['deleted_at'])) : ?>
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            <span class="text-sm font-bold text-red-600">Product Deleted</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($item['part_number']); ?></div>
                    <?php else : ?>
                        <a href="<?php echo APP_URL; ?>/products/detail?id=<?php echo $item['product_id']; ?>" 
                           class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center">
                            <i class="fas fa-link mr-2 text-xs"></i>
                            <?php echo htmlspecialchars($item['part_number']); ?>
                        </a>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <?php if (!empty($item['deleted_at'])) : ?>
                        <span class="text-sm text-gray-400 italic">Deleted Product</span>
                    <?php else : ?>
                        <span class="text-sm text-gray-700 font-medium"><?php echo htmlspecialchars($item['product_type'] ?? 'N/A'); ?></span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gradient-to-r from-blue-100 to-indigo-100 text-indigo-800">
                        <?php echo htmlspecialchars($item['category_name'] ?? 'N/A'); ?>
                    </span>
                </td>
                <?php if (!$selected_location) : ?>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-orange-400 to-red-500 p-1.5 rounded mr-2">
                            <i class="fas fa-map-pin text-white text-xs"></i>
                        </div>
                        <span class="text-sm text-gray-700 font-medium"><?php echo htmlspecialchars($item['location_name']); ?></span>
                    </div>
                </td>
                <?php endif; ?>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800">
                        <?php echo htmlspecialchars($item['size'] ?? 'One Size'); ?>
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-lg font-bold <?php echo $item['quantity'] > $item['min_quantity'] * 2 ? 'text-green-600' : ($item['quantity'] > $item['min_quantity'] ? 'text-yellow-600' : 'text-red-600'); ?>">
                        <?php echo $item['quantity']; ?>
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-600"><?php echo $item['low_stock_threshold']; ?></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <?php if ($item['quantity'] == 0) : ?>
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gradient-to-r from-gray-200 to-gray-300 text-gray-700">
                        <i class="fas fa-times-circle mr-1"></i> Out of Stock
                    </span>
                    <?php elseif ($item['quantity'] <= $item['min_quantity']) : ?>
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gradient-to-r from-red-400 to-red-600 text-white animate-pulse">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Low Stock
                    </span>
                    <?php elseif ($item['quantity'] <= $item['min_quantity'] * 2) : ?>
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 text-white">
                        <i class="fas fa-exclamation mr-1"></i> Warning
                    </span>
                    <?php else : ?>
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gradient-to-r from-green-400 to-emerald-500 text-white">
                        <i class="fas fa-check-circle mr-1"></i> In Stock
                    </span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <?php if (!empty($item['deleted_at'])) : ?>
                        <span class="text-xs text-gray-400 italic">No actions available</span>
                    <?php else : ?>
                        <div class="flex space-x-2">
                            <a href="<?php echo APP_URL; ?>/stock/add?product_id=<?php echo $item['product_id']; ?>&location_id=<?php echo $item['location_id']; ?><?php echo $item['size_id'] ? '&size_id=' . $item['size_id'] : ''; ?>" 
                               class="bg-gradient-to-r from-green-400 to-emerald-500 text-white p-2 rounded-lg hover:shadow-lg transform hover:scale-110 transition-all duration-200" 
                               title="Add Stock">
                                <i class="fas fa-plus text-xs"></i>
                            </a>
                            <a href="<?php echo APP_URL; ?>/stock/out?product_id=<?php echo $item['product_id']; ?>&location_id=<?php echo $item['location_id']; ?><?php echo $item['size_id'] ? '&size_id=' . $item['size_id'] : ''; ?>" 
                               class="bg-gradient-to-r from-red-400 to-rose-500 text-white p-2 rounded-lg hover:shadow-lg transform hover:scale-110 transition-all duration-200" 
                               title="Remove Stock">
                                <i class="fas fa-minus text-xs"></i>
                            </a>
                            <a href="<?php echo APP_URL; ?>/stock/transfer?product_id=<?php echo $item['product_id']; ?>&from_location_id=<?php echo $item['location_id']; ?><?php echo $item['size_id'] ? '&size_id=' . $item['size_id'] : ''; ?>" 
                               class="bg-gradient-to-r from-blue-400 to-cyan-500 text-white p-2 rounded-lg hover:shadow-lg transform hover:scale-110 transition-all duration-200" 
                               title="Transfer Stock">
                                <i class="fas fa-exchange-alt text-xs"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($stock)) : ?>
            <tr>
                <td colspan="<?php echo $selected_location ? 8 : 9; ?>" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="bg-gradient-to-r from-gray-200 to-gray-300 p-6 rounded-full mb-4">
                            <i class="fas fa-box-open text-4xl text-gray-500"></i>
                        </div>
                        <p class="text-lg font-medium text-gray-600">No inventory records found</p>
                        <p class="text-sm text-gray-500 mt-2">Try adjusting your filters or add new stock</p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>