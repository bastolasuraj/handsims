<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #fee2e2;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #f87171 0%, #ef4444 100%);
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #dc2626 0%, #b91c1c 100%);
    }
    
    select:focus, input:focus, textarea:focus {
        transform: translateY(-1px);
    }
</style>

<div class="max-w-6xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex items-center">
            <div class="bg-gradient-to-r from-red-500 to-rose-600 p-3 rounded-lg shadow-lg">
                <i class="fas fa-minus-circle text-white text-xl"></i>
            </div>
            <div class="ml-4">
                <h1 class="text-2xl font-bold text-gray-800">Stock Out</h1>
                <p class="text-sm text-gray-600">Remove inventory items and assign to departments</p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Stock Out Form (2 columns on large screens) -->
        <div class="lg:col-span-2 bg-gradient-to-br from-white to-red-50 shadow-xl rounded-xl p-8 border border-red-100">
            <form action="<?php echo APP_URL; ?>/stock/out" method="POST" class="space-y-6">
            <?php require_once __DIR__ . '/../../Helpers/csrf.php';
            echo csrf_field(); ?>
            
            <!-- Two Column Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Selection -->
                <div class="group">
                    <label for="product_search" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-box text-purple-500 mr-1"></i>
                        Product <span class="text-red-400">*</span>
                    </label>
                    <div class="relative" id="product_search_container">
                        <input type="text" 
                               id="product_search"
                               required
                               value="<?php echo isset($selected_product) ? htmlspecialchars($selected_product['part_number'] . ' - ' . $selected_product['product_type']) : ''; ?>"
                               class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 text-sm font-medium hover:border-purple-300"
                               placeholder="Type or select product...">
                        <input type="hidden" name="product_id" id="product_id" required value="<?php echo isset($selected_product) ? $selected_product['id'] : ''; ?>">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400 group-hover:text-purple-500 transition-colors"></i>
                        </div>
                        <div id="product_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                </div>

                <!-- Location Selection -->
                <div class="group">
                    <label for="location_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-warehouse text-blue-500 mr-1"></i>
                        From Location <span class="text-red-400">*</span>
                    </label>
                    <div class="relative" id="location_search_container">
                        <input type="text" 
                               id="location_name"
                               required
                               value="<?php echo isset($selected_location) ? htmlspecialchars($selected_location['name']) : ''; ?>"
                               class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 text-sm font-medium hover:border-blue-300"
                               placeholder="Type or select location...">
                        <input type="hidden" name="location_id" id="location_id" required value="<?php echo isset($selected_location) ? $selected_location['id'] : ''; ?>">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                        </div>
                        <div id="location_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                </div>

                <!-- Department Selection -->
                <div class="group">
                    <label for="department_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-building text-red-500 mr-1"></i>
                        To Department <span class="text-red-400">*</span>
                    </label>
                    <div class="relative" id="department_search_container">
                        <input type="text" 
                               id="department_name"
                               required
                               class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-red-500 focus:ring-4 focus:ring-red-100 transition-all duration-200 text-sm font-medium hover:border-red-300"
                               placeholder="Type or select department...">
                        <input type="hidden" name="department_id" id="department_id" required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400 group-hover:text-red-500 transition-colors"></i>
                        </div>
                        <div id="department_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                </div>

                <!-- Size Selection -->
                <div class="group">
                    <label for="size_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-ruler text-orange-500 mr-1"></i>
                        Size (if applicable)
                    </label>
                    <div class="relative" id="size_search_container">
                        <input type="text" 
                               id="size_name"
                               class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all duration-200 text-sm font-medium hover:border-orange-300"
                               placeholder="Select a product first..."
                               value="<?php echo isset($selected_size) ? htmlspecialchars($selected_size['size']) : ''; ?>">
                        <input type="hidden" name="size_id" id="size_id" value="<?php echo isset($selected_size) ? $selected_size['id'] : ''; ?>">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400 group-hover:text-orange-500 transition-colors"></i>
                        </div>
                    </div>
                </div>

                <!-- Quantity -->
                <div class="group md:col-span-2">
                    <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sort-numeric-down text-red-500 mr-1"></i>
                        Quantity <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="quantity" name="quantity" min="1" required
                               class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-red-500 focus:ring-4 focus:ring-red-100 transition-all duration-200 text-sm font-medium hover:border-red-300"
                               placeholder="Enter quantity to remove">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-calculator text-gray-400 group-hover:text-red-500 transition-colors"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remarks -->
            <div class="group">
                <label for="remarks" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-comment-alt text-indigo-500 mr-1"></i>
                    Remarks / Reason
                </label>
                <div class="relative">
                    <textarea id="remarks" name="remarks" rows="4"
                              class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-sm resize-none hover:border-indigo-300"
                              placeholder="Optional notes or reason for stock removal..."></textarea>
                    <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                </div>
                </div>

                <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t-2 border-gray-100">
                <a href="<?php echo APP_URL; ?>/dashboard" 
                   class="bg-gradient-to-r from-gray-200 to-gray-300 hover:from-gray-300 hover:to-gray-400 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-arrow-left mr-2"></i> Cancel
                </a>
                <button type="submit" 
                        class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-minus-circle mr-2"></i> Remove Stock
                </button>
            </div>
            </form>
        </div>

        <!-- Combined Lookup & Info Panel -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-white to-purple-50 shadow-xl rounded-xl p-6 border border-purple-100 sticky top-6">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-2 rounded-lg shadow-lg">
                            <i class="fas fa-list text-white text-lg"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-800 ml-3" id="lookupTitle">Master Data & Stock Info</h2>
                    </div>
                </div>
                <div class="relative mb-4">
                    <input type="text" 
                           id="lookupSearch" 
                           placeholder="Search..." 
                           class="pl-10 pr-4 py-2 text-sm w-full border-2 border-purple-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-400 transition-all duration-200 hover:border-purple-300"
                           onkeyup="filterLookupData(this.value)">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-purple-400"></i>
                </div>
                
                <!-- Combined Container -->
                <div id="combinedContainer" class="overflow-y-auto custom-scrollbar" style="max-height: 600px;">
                    <div class="text-center py-12 text-gray-400">
                        <i class="fas fa-mouse-pointer text-4xl mb-3"></i>
                        <p class="text-sm">Click on a field to see available options</p>
                        <p class="text-xs mt-2">Stock information will appear as you fill the form</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 text-center border border-blue-200">
        <p class="text-sm text-gray-700">
            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
            Need to add stock instead? 
            <a href="<?php echo APP_URL; ?>/stock/add" class="text-blue-600 hover:text-blue-700 font-semibold underline decoration-2 decoration-blue-300 hover:decoration-blue-500 transition-all">
                Go to Add Stock
            </a>
        </p>
    </div>
</div>

<script>
// Store inventory data
const inventoryData = {
    products: <?php echo json_encode($products); ?>,
    locations: <?php echo json_encode($locations); ?>,
    departments: <?php echo json_encode($departments); ?>,
    sizes: <?php echo json_encode($sizes); ?>, // All sizes - will be filtered based on product
    productSizes: {}, // Cache for product-specific sizes
    stock: [] // Will be populated via AJAX
};

// Function to get product-specific sizes from stock
function getProductSizes() {
    const productId = document.getElementById('product_id').value;
    const locationId = document.getElementById('location_id').value;
    
    if (!productId || !locationId) {
        return [];
    }
    
    // Get sizes from actual stock data
    const stockItems = inventoryData.stock.filter(item => 
        item.product_id == productId && item.location_id == locationId && item.quantity > 0
    );
    
    // Extract unique sizes
    const uniqueSizes = [];
    const sizeIds = new Set();
    
    stockItems.forEach(item => {
        if (!sizeIds.has(item.size_id)) {
            sizeIds.add(item.size_id);
            uniqueSizes.push({
                id: item.size_id,
                size: item.size
            });
        }
    });
    
    return uniqueSizes;
}


function updateActive(items, activeIndex) {
    items.forEach(item => item.classList.remove('bg-gray-100'));
    if (activeIndex > -1) {
        items[activeIndex].classList.add('bg-gray-100');
    }
}

// Function to fetch real stock data
async function fetchStockData() {
    try {
        const response = await fetch('<?php echo APP_URL; ?>/stock/getInventoryJson');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        inventoryData.stock = data;
        updateStockInfo(); // Update info once data is fetched
    } catch (error) {
        console.error("Error fetching stock data:", error);
        document.getElementById('stockInfoPanel').innerHTML = `
            <div class="text-center py-8 text-red-500">
                <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                <p class="text-sm">Failed to load stock data. Please try again.</p>
            </div>
        `;
    }
}

// Update the stock information panel
function updateStockInfo() {
    const productId = document.getElementById('product_id').value;
    const locationId = document.getElementById('location_id').value;
    const sizeId = document.getElementById('size_id').value;
    const productSearch = document.getElementById('product_search').value;
    const locationName = document.getElementById('location_name').value;
    
    const panel = document.getElementById('stockInfoPanel');
    let html = '';
    
    // Filter stock based on current selections
    let filteredStock = inventoryData.stock;
    
    if (productId) {
        filteredStock = filteredStock.filter(s => s.product_id == productId);
    } else if (productSearch) {
        // Partial match for product search
        filteredStock = filteredStock.filter(s => {
            const combinedName = `${s.part_number} - ${s.product_type}`;
            return combinedName.toLowerCase().includes(productSearch.toLowerCase());
        });
    }
    
    if (locationId) {
        filteredStock = filteredStock.filter(s => s.location_id == locationId);
    } else if (locationName) {
        // Partial match for location
        filteredStock = filteredStock.filter(s => 
            s.location_name.toLowerCase().includes(locationName.toLowerCase())
        );
    }
    
    if (sizeId) {
        filteredStock = filteredStock.filter(s => s.size_id == sizeId);
    }
    
    if (filteredStock.length > 0) {
        // Group by location if product is selected but location isn't
        if ((productId || productSearch) && !locationId) {
            html += '<div class="space-y-3">';
            html += '<h4 class="font-semibold text-gray-700 text-sm">Stock by Location:</h4>';
            
            // Group by location
            const byLocation = {};
            filteredStock.forEach(item => {
                if (!byLocation[item.location_name]) {
                    byLocation[item.location_name] = [];
                }
                byLocation[item.location_name].push(item);
            });
            
            Object.keys(byLocation).forEach(loc => {
                const items = byLocation[loc];
                const total = items.reduce((sum, item) => sum + item.quantity, 0);
                
                html += `
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium text-gray-800">
                                <i class="fas fa-warehouse text-blue-500 mr-1"></i>
                                ${loc}
                            </span>
                            <span class="text-sm font-bold text-gray-700">${total} units</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                `;
                
                items.forEach(item => {
                    const stockClass = item.quantity > 10 ? 'text-green-600' : 
                                     item.quantity > 5 ? 'text-yellow-600' : 'text-red-600';
                    html += `
                        <div class="flex justify-between">
                            <span class="text-gray-600">${item.size}:</span>
                            <span class="font-medium ${stockClass}">${item.quantity}</span>
                        </div>
                    `;
                });
                
                html += '</div></div>';
            });
            html += '</div>';
        }
        // Show sizes if product and location are selected
        else if (productId && locationId && !sizeId) {
            html += '<div class="space-y-3">';
            html += '<h4 class="font-semibold text-gray-700 text-sm">Available Sizes:</h4>';
            
            const total = filteredStock.reduce((sum, item) => sum + item.quantity, 0);
            html += `
                <div class="bg-blue-50 rounded-lg p-3 border border-blue-200 mb-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-blue-800">Total Available:</span>
                        <span class="text-lg font-bold text-blue-900">${total} units</span>
                    </div>
                </div>
            `;
            
            filteredStock.forEach(item => {
                const stockClass = item.quantity > 10 ? 'bg-green-100 border-green-300 text-green-800' : 
                                 item.quantity > 5 ? 'bg-yellow-100 border-yellow-300 text-yellow-800' : 
                                 'bg-red-100 border-red-300 text-red-800';
                const iconClass = item.quantity > 10 ? 'text-green-500' : 
                                 item.quantity > 5 ? 'text-yellow-500' : 'text-red-500';
                
                html += `
                    <div class="${stockClass} rounded-lg p-3 border">
                        <div class="flex justify-between items-center">
                            <span class="font-medium">
                                <i class="fas fa-ruler ${iconClass} mr-1"></i>
                                Size ${item.size}
                            </span>
                            <span class="font-bold text-lg">${item.quantity}</span>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }
        // Show specific stock info if all fields are selected
        else if (productId && locationId && sizeId) {
            const item = filteredStock[0];
            if (item) {
                const stockClass = item.quantity > 10 ? 'bg-green-100 border-green-300' : 
                                 item.quantity > 5 ? 'bg-yellow-100 border-yellow-300' : 
                                 'bg-red-100 border-red-300';
                const textClass = item.quantity > 10 ? 'text-green-800' : 
                                item.quantity > 5 ? 'text-yellow-800' : 'text-red-800';
                const statusText = item.quantity > 10 ? 'Good Stock' : 
                                 item.quantity > 5 ? 'Low Stock' : 'Critical';
                const statusIcon = item.quantity > 10 ? 'check-circle' : 
                                  item.quantity > 5 ? 'exclamation-triangle' : 'exclamation-circle';
                
                html += `
                    <div class="${stockClass} rounded-lg p-4 border-2 ${textClass}">
                        <div class="text-center">
                            <i class="fas fa-${statusIcon} text-3xl mb-3"></i>
                            <h4 class="font-bold text-lg mb-1">${item.quantity} Units Available</h4>
                            <p class="text-sm font-medium">${statusText}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-current opacity-50">
                            <div class="text-xs space-y-1">
                                <div class="flex justify-between">
                                    <span>Product:</span>
                                    <span class="font-medium">${item.part_number} - ${item.product_type}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Location:</span>
                                    <span class="font-medium">${item.location_name}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Size:</span>
                                    <span class="font-medium">${item.size}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
        // Show general summary
        else {
            const total = filteredStock.reduce((sum, item) => sum + item.quantity, 0);
            const locations = [...new Set(filteredStock.map(s => s.location_name))].length;
            const products = [...new Set(filteredStock.map(s => `${s.part_number} - ${s.product_type}`))].length;
            
            html += `
                <div class="space-y-3">
                    <h4 class="font-semibold text-gray-700 text-sm">Stock Summary:</h4>
                    <div class="grid grid-cols-1 gap-3">
                        <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-blue-700">Total Units:</span>
                                <span class="font-bold text-blue-900">${total}</span>
                            </div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-purple-700">Products:</span>
                                <span class="font-bold text-purple-900">${products}</span>
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-green-700">Locations:</span>
                                <span class="font-bold text-green-900">${locations}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    } else if (productSearch || locationName) {
        // No results found
        html = `
            <div class="text-center py-6">
                <i class="fas fa-search text-3xl text-gray-300 mb-3"></i>
                <p class="text-sm text-gray-500">No stock found matching your criteria</p>
            </div>
        `;
    } else {
        // Default message
        html = `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-chart-bar text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm">Start filling the form to see available stock information</p>
            </div>
        `;
    }
    
    panel.innerHTML = html;
}

// Display combined lookup and stock info
function displayCombinedData(lookupType, lookupData, lookupTitle) {
    const container = document.getElementById('combinedContainer');
    const titleElement = document.getElementById('lookupTitle');
    
    titleElement.textContent = lookupTitle;
    
    let html = '';
    
    // Show lookup data first
    if (lookupData && lookupData.length > 0) {
        html += '<div class="mb-4"><h3 class="text-sm font-bold text-gray-700 mb-2 flex items-center"><i class="fas fa-list mr-2 text-purple-500"></i>Select ' + lookupType + '</h3>';
        html += '<div class="grid grid-cols-1 gap-2">';
        
        lookupData.forEach(item => {
            const displayName = item.size || item.name || (item.part_number ? `${item.part_number} - ${item.product_type}` : '');
            let icon = 'fa-cube';
            let colorClass = 'from-purple-50 to-indigo-50 border-purple-200 hover:border-purple-400';
            
            if (lookupType === 'products') {
                icon = 'fa-box';
                colorClass = 'from-purple-50 to-indigo-50 border-purple-200 hover:border-purple-400';
            } else if (lookupType === 'locations') {
                icon = 'fa-warehouse';
                colorClass = 'from-blue-50 to-cyan-50 border-blue-200 hover:border-blue-400';
            } else if (lookupType === 'sizes') {
                icon = 'fa-ruler';
                colorClass = 'from-orange-50 to-amber-50 border-orange-200 hover:border-orange-400';
            } else if (lookupType === 'departments') {
                icon = 'fa-building';
                colorClass = 'from-red-50 to-rose-50 border-red-200 hover:border-red-400';
            }
            
            // Get stock info for this item based on lookup type
            let stockInfo = '';
            let inlineQuantity = '';
            if (lookupType === 'locations') {
                const productId = document.getElementById('product_id').value;
                if (productId) {
                    const locationStock = inventoryData.stock.filter(s => 
                        s.product_id == productId && s.location_id == item.id && s.quantity > 0
                    );
                    if (locationStock.length > 0) {
                        // Create evenly spaced, bold size breakdown
                        const sizeItems = locationStock.map(s => 
                            `<span class="inline-block"><span class="font-bold">${s.size}</span>:<span class="font-bold">${s.quantity}</span></span>`
                        ).join(' ');
                        stockInfo = `<div class="text-xs text-gray-700 mt-2 flex flex-wrap gap-3">${sizeItems}</div>`;
                    }
                }
            } else if (lookupType === 'sizes') {
                const productId = document.getElementById('product_id').value;
                const locationId = document.getElementById('location_id').value;
                if (productId && locationId) {
                    const sizeStock = inventoryData.stock.find(s => 
                        s.product_id == productId && s.location_id == locationId && s.size_id == item.id
                    );
                    if (sizeStock && sizeStock.quantity > 0) {
                        // Show quantity inline (on the same line as size name)
                        inlineQuantity = `<span class="text-sm font-bold text-blue-600">${sizeStock.quantity} units</span>`;
                    }
                }
            }
            
            html += `
                <div class="lookup-item bg-gradient-to-r ${colorClass} border-2 rounded-lg p-4 cursor-pointer transition-all duration-200 hover:shadow-md transform hover:-translate-y-0.5"
                     data-type="${lookupType}"
                     data-id="${item.id}"
                     data-name="${displayName.replace(/"/g, '&quot;')}"
                     onclick="selectLookupItem('${lookupType}', ${item.id}, \`${displayName.replace(/`/g, '\\`')}\`)">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="flex-shrink-0">
                                <i class="fas ${icon} text-lg text-gray-700"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-semibold text-gray-800">${displayName}</p>
                                ${stockInfo}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            ${inlineQuantity}
                            <i class="fas fa-chevron-right text-sm text-gray-400"></i>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div></div>';
    }
    
    // Don't show redundant stock info below if it's already in the lookup items
    // Only show additional info for products and departments (which don't have stock info in lookup)
    if (lookupType !== 'locations' && lookupType !== 'sizes') {
        const stockHtml = getStockInfoHtml();
        if (stockHtml) {
            html += '<div class="mt-4 pt-4 border-t-2 border-purple-200">' + stockHtml + '</div>';
        }
    }
    
    container.innerHTML = html || `
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-mouse-pointer text-4xl mb-3"></i>
            <p class="text-sm">Click on a field to see available options</p>
            <p class="text-xs mt-2">Stock information will appear as you fill the form</p>
        </div>
    `;
}

// Get stock info HTML
function getStockInfoHtml() {
    const productId = document.getElementById('product_id').value;
    const locationId = document.getElementById('location_id').value;
    const sizeId = document.getElementById('size_id').value;
    
    if (!productId && !locationId) {
        return null;
    }
    
    let filteredStock = inventoryData.stock;
    
    if (productId) {
        filteredStock = filteredStock.filter(s => s.product_id == productId);
    }
    
    if (locationId) {
        filteredStock = filteredStock.filter(s => s.location_id == locationId);
    }
    
    if (sizeId) {
        filteredStock = filteredStock.filter(s => s.size_id == sizeId);
    }
    
    if (filteredStock.length === 0) {
        return '<div class="text-center py-4 text-gray-500 text-xs">No stock found</div>';
    }
    
    let html = '';
    
    if (productId && locationId && sizeId) {
        const item = filteredStock[0];
        if (item) {
            const stockClass = item.quantity > 10 ? 'bg-green-100 border-green-300' : 
                             item.quantity > 5 ? 'bg-yellow-100 border-yellow-300' : 
                             'bg-red-100 border-red-300';
            const textClass = item.quantity > 10 ? 'text-green-800' : 
                            item.quantity > 5 ? 'text-yellow-800' : 'text-red-800';
            const statusIcon = item.quantity > 10 ? 'check-circle' : 
                              item.quantity > 5 ? 'exclamation-triangle' : 'exclamation-circle';
            
            html = `
                <div class="${stockClass} rounded-lg p-3 border-2 ${textClass}">
                    <div class="text-center">
                        <i class="fas fa-${statusIcon} text-2xl mb-2"></i>
                        <h4 class="font-bold text-base mb-1">${item.quantity} Units</h4>
                        <p class="text-xs">${item.size}</p>
                    </div>
                </div>
            `;
        }
    } else if (productId && locationId) {
        const total = filteredStock.reduce((sum, item) => sum + item.quantity, 0);
        
        // Group by location
        const byLocation = {};
        filteredStock.forEach(item => {
            if (!byLocation[item.location_name]) {
                byLocation[item.location_name] = [];
            }
            byLocation[item.location_name].push(item);
        });
        
        html = `<div class="bg-blue-50 rounded-lg p-3 border border-blue-200 mb-2">
            <div class="flex justify-between items-center">
                <span class="text-xs font-medium text-blue-800">Total:</span>
                <span class="text-sm font-bold text-blue-900">${total} units</span>
            </div>
        </div>`;
        
        Object.keys(byLocation).forEach(locationName => {
            const items = byLocation[locationName];
            const locationTotal = items.reduce((sum, item) => sum + parseInt(item.quantity), 0);
            
            // Determine overall stock status for the location
            const avgQty = locationTotal / items.length;
            const stockClass = avgQty > 10 ? 'bg-green-100 border-green-300 text-green-800' : 
                             avgQty > 5 ? 'bg-yellow-100 border-yellow-300 text-yellow-800' : 
                             'bg-red-100 border-red-300 text-red-800';
            
            // Build size string like "S:10 M:15 L:20"
            const sizeString = items.map(item => `${item.size}:${item.quantity}`).join(' ');
            
            html += `
                <div class="${stockClass} rounded-lg p-2 border mb-2">
                    <div class="font-semibold text-xs mb-1">${locationName}</div>
                    <div class="text-xs">${sizeString}</div>
                </div>
            `;
        });
    } else {
        const total = filteredStock.reduce((sum, item) => sum + item.quantity, 0);
        html = `
            <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-blue-700">Total Units:</span>
                    <span class="font-bold text-blue-900">${total}</span>
                </div>
            </div>
        `;
    }
    
    return html;
}

// Select an item from the lookup panel
function selectLookupItem(type, id, name) {
    if (type === 'products') {
        document.getElementById('product_search').value = name;
        document.getElementById('product_id').value = id;
        // Refresh the combined view to show updated stock info
        displayCombinedData('products', inventoryData.products, 'Select Product');
        document.getElementById('location_name').focus();
    } else if (type === 'locations') {
        document.getElementById('location_name').value = name;
        document.getElementById('location_id').value = id;
        // Refresh the combined view to show updated stock info
        displayCombinedData('locations', inventoryData.locations, 'Select Location');
        document.getElementById('size_name').focus();
    } else if (type === 'sizes') {
        document.getElementById('size_name').value = name;
        document.getElementById('size_id').value = id;
        // Refresh the combined view to show updated stock info
        const productSizes = getProductSizes();
        displayCombinedData('sizes', productSizes, 'Select Size');
        document.getElementById('department_name').focus();
    } else if (type === 'departments') {
        document.getElementById('department_name').value = name;
        document.getElementById('department_id').value = id;
        document.getElementById('quantity').focus();
    }
}

// Filter lookup data based on search
function filterLookupData(searchTerm) {
    const term = searchTerm.toLowerCase();
    const items = document.querySelectorAll('.lookup-item');
    
    items.forEach(item => {
        const name = item.getAttribute('data-name').toLowerCase();
        
        if (name.includes(term)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

// Hide lookup data when clicking outside
let hideTimeout;

function hideCombinedData() {
    const container = document.getElementById('combinedContainer');
    const titleElement = document.getElementById('lookupTitle');
    
    titleElement.textContent = 'Stock Information';
    
    // Only show stock info, hide lookup data
    const stockHtml = getStockInfoHtml();
    if (stockHtml) {
        container.innerHTML = '<div class="space-y-3">' + stockHtml + '</div>';
    } else {
        container.innerHTML = `
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-mouse-pointer text-4xl mb-3"></i>
                <p class="text-sm">Click on a field to see available options</p>
                <p class="text-xs mt-2">Stock information will appear as you fill the form</p>
            </div>
        `;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    fetchStockData(); // Fetch data on load
    
    // Add focus event listeners to show combined lookup and stock data
    document.getElementById('product_search').addEventListener('focus', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide
        displayCombinedData('products', inventoryData.products, 'Select Product');
    });
    
    // Add input event listener for product search to filter and keep lookup visible while typing
    document.getElementById('product_search').addEventListener('input', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide while typing
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            // Show all products if search is empty
            displayCombinedData('products', inventoryData.products, 'Select Product');
        } else {
            // Filter products based on search term
            const filteredProducts = inventoryData.products.filter(product => {
                const productName = `${product.part_number} - ${product.product_type}`.toLowerCase();
                return productName.includes(searchTerm);
            });
            displayCombinedData('products', filteredProducts, 'Select Product');
        }
    });
    
    document.getElementById('location_name').addEventListener('focus', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide
        const productId = document.getElementById('product_id').value;
        
        // Filter locations to only show those with stock for the selected product
        let filteredLocations = inventoryData.locations;
        if (productId) {
            const locationsWithStock = inventoryData.stock
                .filter(s => s.product_id == productId && s.quantity > 0)
                .map(s => s.location_id);
            const uniqueLocationIds = [...new Set(locationsWithStock)];
            filteredLocations = inventoryData.locations.filter(loc => uniqueLocationIds.includes(loc.id));
        }
        
        displayCombinedData('locations', filteredLocations, 'Select Location');
    });
    
    document.getElementById('size_name').addEventListener('focus', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide
        const productSizes = getProductSizes();
        if (productSizes.length > 0) {
            displayCombinedData('sizes', productSizes, 'Available Sizes');
        } else {
            displayCombinedData('sizes', [], 'Available Sizes');
        }
    });
    
    document.getElementById('department_name').addEventListener('focus', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide
        displayCombinedData('departments', inventoryData.departments, 'Select Department');
    });
    
    // Add blur event listeners to hide lookup data
    const formFields = ['product_search', 'location_name', 'size_name', 'department_name'];
    formFields.forEach(fieldId => {
        document.getElementById(fieldId).addEventListener('blur', function() {
            // Don't hide product lookup if there's text in the search field
            if (fieldId === 'product_search' && this.value.trim() !== '') {
                return; // Keep lookup visible for product search
            }
            // Delay to allow click on lookup items
            hideTimeout = setTimeout(hideCombinedData, 200);
        });
    });
});
</script>