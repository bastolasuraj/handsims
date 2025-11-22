<style>
    .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #e0f2fe; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #38bdf8 0%, #0ea5e9 100%); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #0284c7 0%, #0369a1 100%); }
    select:focus, input:focus, textarea:focus { transform: translateY(-1px); }
</style>

<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex items-center">
            <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-3 rounded-lg shadow-lg">
                <i class="fas fa-exchange-alt text-white text-xl"></i>
            </div>
            <div class="ml-4">
                <h1 class="text-2xl font-bold text-gray-800">Transfer Stock</h1>
                <p class="text-sm text-gray-600">Move stock between locations without changing total quantity</p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Transfer Form (2 columns on large screens) -->
        <div class="lg:col-span-2 bg-gradient-to-br from-white to-cyan-50 shadow-xl rounded-xl p-8 border border-cyan-100">
            <form action="<?php echo APP_URL; ?>/stock/transfer" method="POST" class="space-y-6">
            <?php require_once __DIR__ . '/../../Helpers/csrf.php';
            echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- From Location -->
                <div class="group">
                    <label for="from_location_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-warehouse text-blue-500 mr-1"></i>
                        From Location <span class="text-red-400">*</span>
                    </label>
                    <div class="relative" id="from_location_search_container">
                        <input type="text" 
                               id="from_location_name"
                               required
                               class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 text-sm font-medium hover:border-blue-300"
                               placeholder="Type or select from location..."
                               value="<?php echo isset($selected_from_location) ? htmlspecialchars($selected_from_location['name']) : ''; ?>">
                        <input type="hidden" name="from_location_id" id="from_location_id" required value="<?php echo isset($selected_from_location) ? $selected_from_location['id'] : ''; ?>">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                        </div>
                        <div id="from_location_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                </div>

                <!-- To Location -->
                <div class="group">
                    <label for="to_location_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-warehouse text-emerald-500 mr-1"></i>
                        To Location <span class="text-red-400">*</span>
                    </label>
                    <div class="relative" id="to_location_search_container">
                        <input type="text" 
                               id="to_location_name"
                               required
                               class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 text-sm font-medium hover:border-emerald-300"
                               placeholder="Type or select to location...">
                        <input type="hidden" name="to_location_id" id="to_location_id" required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400 group-hover:text-emerald-500 transition-colors"></i>
                        </div>
                        <div id="to_location_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                </div>
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
                               disabled
                               class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 text-sm font-medium hover:border-purple-300"
                               placeholder="Select a location first..."
                               value="<?php echo isset($selected_product) ? htmlspecialchars($selected_product['part_number'] . ' - ' . $selected_product['product_type']) : ''; ?>">
                        <input type="hidden" name="product_id" id="product_id" required value="<?php echo isset($selected_product) ? $selected_product['id'] : ''; ?>">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400 group-hover:text-purple-500 transition-colors"></i>
                        </div>
                        <div id="product_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                </div>

                <!-- Size -->
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
                        <i class="fas fa-exchange-alt text-cyan-500 mr-1"></i>
                        Quantity to Transfer <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="quantity" name="quantity" min="1" required
                               class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100 transition-all duration-200 text-sm font-medium hover:border-cyan-300"
                               placeholder="Enter quantity to transfer">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-calculator text-gray-400 group-hover:text-cyan-500 transition-colors"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="group">
                <label for="remarks" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-comment-alt text-indigo-500 mr-1"></i>
                    Remarks / Reason
                </label>
                <div class="relative">
                    <textarea id="remarks" name="remarks" rows="3"
                              class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-sm resize-none hover:border-indigo-300"
                              placeholder="Optional notes or reason for transfer..."></textarea>
                    <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t-2 border-gray-100">
                <a href="<?php echo APP_URL; ?>/dashboard" 
                   class="bg-gradient-to-r from-gray-200 to-gray-300 hover:from-gray-300 hover:to-gray-400 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-arrow-left mr-2"></i> Cancel
                </a>
                <button type="submit" 
                        class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-exchange-alt mr-2"></i> Transfer Stock
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
                <div id="combinedContainer" class="overflow-y-auto custom-scrollbar" style="max-height: 700px;">
                    <div class="text-center py-12 text-gray-400">
                        <i class="fas fa-mouse-pointer text-4xl mb-3"></i>
                        <p class="text-sm">Click on a field to see available options</p>
                        <p class="text-xs mt-2">Stock information will appear as you fill the form</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Store inventory data
const inventoryData = {
    products: <?php echo json_encode($products); ?>,
    locations: <?php echo json_encode($locations); ?>,
    sizes: <?php echo json_encode($sizes); ?>, // All sizes - will be filtered based on product
    productSizes: {}, // Cache for product-specific sizes
    stock: [] // Will be populated via AJAX
};

// Function to get product-specific sizes from stock
function getProductSizes() {
    const productId = document.getElementById('product_id').value;
    const fromLocationId = document.getElementById('from_location_id').value;
    
    if (!productId || !fromLocationId) {
        return [];
    }
    
    // Get sizes from actual stock data
    const stockItems = inventoryData.stock.filter(item => 
        item.product_id == productId && item.location_id == fromLocationId && item.quantity > 0
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

async function fetchStockData() {
    try {
        const response = await fetch('<?php echo APP_URL; ?>/stock/getInventoryJson');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        inventoryData.stock = data;
        updateStockInfo();
        updateStockInfoTo();
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

function updateStockInfo() {
    const productId = document.getElementById('product_id').value;
    const fromLocationId = document.getElementById('from_location_id').value;
    const sizeId = document.getElementById('size_id').value;
    const productSearch = document.getElementById('product_search').value;
    const fromLocationName = document.getElementById('from_location_name').value;
    
    const panel = document.getElementById('stockInfoPanel');
    let html = '';
    
    let filteredStock = inventoryData.stock;
    
    if (productId) {
        filteredStock = filteredStock.filter(s => s.product_id == productId);
    } else if (productSearch) {
        filteredStock = filteredStock.filter(s => {
            const combinedName = `${s.part_number} - ${s.product_type}`;
            return combinedName.toLowerCase().includes(productSearch.toLowerCase());
        });
    }
    
    if (fromLocationId) {
        filteredStock = filteredStock.filter(s => s.location_id == fromLocationId);
    } else if (fromLocationName) {
        filteredStock = filteredStock.filter(s => 
            s.location_name.toLowerCase().includes(fromLocationName.toLowerCase())
        );
    }
    
    if (sizeId) {
        filteredStock = filteredStock.filter(s => s.size_id == sizeId);
    }
    
    if (filteredStock.length > 0) {
        if ((productId || productSearch) && !fromLocationId) {
            html += '<div class="space-y-3">';
            html += '<h4 class="font-semibold text-gray-700 text-sm">Stock by Location:</h4>';
            
            const byLocation = {};
            filteredStock.forEach(item => {
                if (!byLocation[item.location_name]) {
                    byLocation[item.location_name] = [];
                }
                byLocation[item.location_name].push(item);
            });
            
            Object.keys(byLocation).forEach(loc => {
                const items = byLocation[loc];
                const total = items.reduce((sum, item) => sum + parseInt(item.quantity), 0);
                
                html += `<div class="bg-gray-50 rounded-lg p-3 border border-gray-200">...</div>`;
            });
            html += '</div>';
        }
        else if (productId && fromLocationId && !sizeId) {
            html += '<div class="space-y-3">';
            html += '<h4 class="font-semibold text-gray-700 text-sm">Available Sizes:</h4>';
            
            const total = filteredStock.reduce((sum, item) => sum + parseInt(item.quantity), 0);
            html += `<div class="bg-blue-50 rounded-lg p-3 border border-blue-200 mb-3">...</div>`;
            
            filteredStock.forEach(item => {
                html += `<div class="...">...</div>`;
            });
            html += '</div>';
        }
        else if (productId && fromLocationId && sizeId) {
            const item = filteredStock[0];
            if (item) {
                html += `<div class="...">...</div>`;
            }
        }
        else {
            const total = filteredStock.reduce((sum, item) => sum + parseInt(item.quantity), 0);
            html += `<div class="space-y-3">...</div>`;
        }
    } else if (productSearch || fromLocationName) {
        html = `<div class="text-center py-6">...</div>`;
    } else {
        html = `<div class="text-center py-8 text-gray-500">...</div>`;
    }
    
    panel.innerHTML = html;
}

function updateStockInfoTo() {
    const productId = document.getElementById('product_id').value;
    const toLocationId = document.getElementById('to_location_id').value;
    const sizeId = document.getElementById('size_id').value;
    const productSearch = document.getElementById('product_search').value;
    const toLocationName = document.getElementById('to_location_name').value;
    
    const panel = document.getElementById('stockInfoPanelTo');
    let html = '';
    
    let filteredStock = inventoryData.stock;
    
    if (productId) {
        filteredStock = filteredStock.filter(s => s.product_id == productId);
    } else if (productSearch) {
        filteredStock = filteredStock.filter(s => {
            const combinedName = `${s.part_number} - ${s.product_type}`;
            return combinedName.toLowerCase().includes(productSearch.toLowerCase());
        });
    }
    
    if (toLocationId) {
        filteredStock = filteredStock.filter(s => s.location_id == toLocationId);
    } else if (toLocationName) {
        filteredStock = filteredStock.filter(s => 
            s.location_name.toLowerCase().includes(toLocationName.toLowerCase())
        );
    }
    
    if (sizeId) {
        filteredStock = filteredStock.filter(s => s.size_id == sizeId);
    }
    
    if (filteredStock.length > 0) {
        html = '...'; // Simplified for brevity
    } else {
        html = '...'; // Simplified for brevity
    }
    
    panel.innerHTML = html;
}

// Display combined lookup data
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
            } else if (lookupType === 'from_locations') {
                icon = 'fa-warehouse';
                colorClass = 'from-blue-50 to-cyan-50 border-blue-200 hover:border-blue-400';
            } else if (lookupType === 'to_locations') {
                icon = 'fa-warehouse';
                colorClass = 'from-emerald-50 to-green-50 border-emerald-200 hover:border-emerald-400';
            } else if (lookupType === 'sizes') {
                icon = 'fa-ruler';
                colorClass = 'from-orange-50 to-amber-50 border-orange-200 hover:border-orange-400';
            }
            
            // Get stock info for this item based on lookup type
            let stockInfo = '';
            if (lookupType === 'from_locations' || lookupType === 'to_locations') {
                const productId = document.getElementById('product_id').value;
                if (productId) {
                    const locationStock = inventoryData.stock.filter(s => 
                        s.product_id == productId && s.location_id == item.id && s.quantity > 0
                    );
                    if (locationStock.length > 0) {
                        const sizeString = locationStock.map(s => `${s.size}:${s.quantity}`).join(' ');
                        stockInfo = `<div class="text-xs text-gray-600 mt-1">${sizeString}</div>`;
                    }
                }
            } else if (lookupType === 'sizes') {
                const productId = document.getElementById('product_id').value;
                const fromLocationId = document.getElementById('from_location_id').value;
                if (productId && fromLocationId) {
                    const sizeStock = inventoryData.stock.find(s => 
                        s.product_id == productId && s.location_id == fromLocationId && s.size_id == item.id
                    );
                    if (sizeStock && sizeStock.quantity > 0) {
                        stockInfo = `<div class="text-xs text-gray-600 mt-1">${sizeStock.quantity} units available</div>`;
                    }
                }
            }
            
            html += `
                <div class="lookup-item bg-gradient-to-r ${colorClass} border-2 rounded-lg p-4 cursor-pointer transition-all duration-200 hover:shadow-md transform hover:-translate-y-0.5"
                     data-type="${lookupType}"
                     data-id="${item.id}"
                     data-name="${displayName.replace(/"/g, '&quot;')}"
                     onclick="selectLookupItem('${lookupType}', ${item.id}, \`${displayName.replace(/`/g, '\\`')}\`)">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas ${icon} text-lg text-gray-700"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-semibold text-gray-800">${displayName}</p>
                            ${stockInfo}
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-chevron-right text-sm text-gray-400"></i>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div></div>';
    }
    
    // Don't show redundant stock info below if it's already in the lookup items
    // Only show additional info for products (which don't have stock info in lookup)
    if (lookupType !== 'from_locations' && lookupType !== 'to_locations' && lookupType !== 'sizes') {
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

// Enable/disable product field based on location selection
function updateProductDropdown(locationId) {
    const productSearchInput = document.getElementById('product_search');
    
    if (!locationId) {
        productSearchInput.disabled = true;
        productSearchInput.placeholder = 'Select a location first...';
    } else {
        productSearchInput.disabled = false;
        productSearchInput.placeholder = 'Type or select product...';
    }
}

// Select an item from the lookup panel
function selectLookupItem(type, id, name) {
    if (type === 'from_locations') {
        document.getElementById('from_location_name').value = name;
        document.getElementById('from_location_id').value = id;
        updateProductDropdown(id);
        displayCombinedData('from_locations', inventoryData.locations, 'From Locations');
        document.getElementById('to_location_name').focus();
    } else if (type === 'to_locations') {
        document.getElementById('to_location_name').value = name;
        document.getElementById('to_location_id').value = id;
        const fromLocationId = document.getElementById('from_location_id').value;
        const filteredLocations = fromLocationId ? 
            inventoryData.locations.filter(loc => loc.id != fromLocationId) : 
            inventoryData.locations;
        displayCombinedData('to_locations', filteredLocations, 'To Locations');
        document.getElementById('product_search').focus();
    } else if (type === 'products') {
        document.getElementById('product_search').value = name;
        document.getElementById('product_id').value = id;
        const fromLocationId = document.getElementById('from_location_id').value;
        if (fromLocationId) {
            const productsInLocation = inventoryData.stock.filter(item => item.location_id == fromLocationId && item.quantity > 0);
            const uniqueProducts = [];
            const productIds = new Set();
            productsInLocation.forEach(item => {
                if (!productIds.has(item.product_id)) {
                    productIds.add(item.product_id);
                    uniqueProducts.push({
                        id: item.product_id,
                        part_number: item.part_number,
                        product_type: item.product_type
                    });
                }
            });
            displayCombinedData('products', uniqueProducts, 'Available Products');
        }
        document.getElementById('size_name').focus();
    } else if (type === 'sizes') {
        document.getElementById('size_name').value = name;
        document.getElementById('size_id').value = id;
        const productSizes = getProductSizes();
        displayCombinedData('sizes', productSizes, 'Available Sizes');
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

// Get stock info HTML for display
function getStockInfoHtml() {
    const productId = document.getElementById('product_id').value;
    const fromLocationId = document.getElementById('from_location_id').value;
    const sizeId = document.getElementById('size_id').value;
    
    if (!productId && !fromLocationId) {
        return null;
    }
    
    let filteredStock = inventoryData.stock;
    
    if (productId) {
        filteredStock = filteredStock.filter(s => s.product_id == productId);
    }
    
    if (fromLocationId) {
        filteredStock = filteredStock.filter(s => s.location_id == fromLocationId);
    }
    
    if (sizeId) {
        filteredStock = filteredStock.filter(s => s.size_id == sizeId);
    }
    
    if (filteredStock.length === 0) {
        return '<div class="text-center py-4 text-gray-500 text-sm">No stock found</div>';
    }
    
    let html = '<h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center"><i class="fas fa-info-circle mr-2 text-blue-500"></i>Available Stock</h3>';
    
    if (productId && fromLocationId && sizeId) {
        const item = filteredStock[0];
        if (item) {
            const stockClass = item.quantity > 10 ? 'bg-green-100 border-green-300' : 
                             item.quantity > 5 ? 'bg-yellow-100 border-yellow-300' : 
                             'bg-red-100 border-red-300';
            const textClass = item.quantity > 10 ? 'text-green-800' : 
                            item.quantity > 5 ? 'text-yellow-800' : 'text-red-800';
            const statusIcon = item.quantity > 10 ? 'check-circle' : 
                              item.quantity > 5 ? 'exclamation-triangle' : 'exclamation-circle';
            
            html += `
                <div class="${stockClass} rounded-lg p-4 border-2 ${textClass}">
                    <div class="text-center">
                        <i class="fas fa-${statusIcon} text-3xl mb-2"></i>
                        <h4 class="font-bold text-lg mb-1">${item.quantity} Units</h4>
                        <p class="text-sm">${item.size}</p>
                    </div>
                </div>
            `;
        }
    } else if (productId && fromLocationId) {
        const total = filteredStock.reduce((sum, item) => sum + parseInt(item.quantity), 0);
        
        // Group by location
        const byLocation = {};
        filteredStock.forEach(item => {
            if (!byLocation[item.location_name]) {
                byLocation[item.location_name] = [];
            }
            byLocation[item.location_name].push(item);
        });
        
        html += `<div class="bg-blue-50 rounded-lg p-3 border border-blue-200 mb-3">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-blue-800">Total Available:</span>
                <span class="text-lg font-bold text-blue-900">${total} units</span>
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
                <div class="${stockClass} rounded-lg p-3 border mb-2">
                    <div class="font-semibold text-sm mb-1">${locationName}</div>
                    <div class="text-xs">${sizeString}</div>
                </div>
            `;
        });
    } else {
        const total = filteredStock.reduce((sum, item) => sum + parseInt(item.quantity), 0);
        html += `
            <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-blue-700">Total Units:</span>
                    <span class="font-bold text-lg text-blue-900">${total}</span>
                </div>
            </div>
        `;
    }
    
    return html;
}

document.addEventListener('DOMContentLoaded', function() {
    fetchStockData();
    
    // Add focus event listeners to show lookup data
    document.getElementById('from_location_name').addEventListener('focus', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide
        displayCombinedData('from_locations', inventoryData.locations, 'From Locations');
    });
    
    document.getElementById('to_location_name').addEventListener('focus', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide
        const fromLocationId = document.getElementById('from_location_id').value;
        const filteredLocations = fromLocationId ? 
            inventoryData.locations.filter(loc => loc.id != fromLocationId) : 
            inventoryData.locations;
        displayCombinedData('to_locations', filteredLocations, 'To Locations');
    });
    
    document.getElementById('product_search').addEventListener('focus', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide
        const fromLocationId = document.getElementById('from_location_id').value;
        if (fromLocationId) {
            const productsInLocation = inventoryData.stock.filter(item => item.location_id == fromLocationId && item.quantity > 0);
            const uniqueProducts = [];
            const productIds = new Set();
            productsInLocation.forEach(item => {
                if (!productIds.has(item.product_id)) {
                    productIds.add(item.product_id);
                    uniqueProducts.push({
                        id: item.product_id,
                        part_number: item.part_number,
                        product_type: item.product_type
                    });
                }
            });
            displayCombinedData('products', uniqueProducts, 'Available Products');
        }
    });
    
    // Add input event listener for product search to filter and keep lookup visible while typing
    document.getElementById('product_search').addEventListener('input', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide while typing
        const searchTerm = this.value.toLowerCase().trim();
        const fromLocationId = document.getElementById('from_location_id').value;
        
        if (fromLocationId) {
            const productsInLocation = inventoryData.stock.filter(item => item.location_id == fromLocationId && item.quantity > 0);
            const uniqueProducts = [];
            const productIds = new Set();
            productsInLocation.forEach(item => {
                if (!productIds.has(item.product_id)) {
                    productIds.add(item.product_id);
                    uniqueProducts.push({
                        id: item.product_id,
                        part_number: item.part_number,
                        product_type: item.product_type
                    });
                }
            });
            
            if (searchTerm === '') {
                // Show all products if search is empty
                displayCombinedData('products', uniqueProducts, 'Available Products');
            } else {
                // Filter products based on search term
                const filteredProducts = uniqueProducts.filter(product => {
                    const productName = `${product.part_number} - ${product.product_type}`.toLowerCase();
                    return productName.includes(searchTerm);
                });
                displayCombinedData('products', filteredProducts, 'Available Products');
            }
        }
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
    
    // Add blur event listeners to hide lookup data
    const formFields = ['from_location_name', 'to_location_name', 'product_search', 'size_name'];
    formFields.forEach(fieldId => {
        document.getElementById(fieldId).addEventListener('blur', function() {
            // Delay to allow click on lookup items
            hideTimeout = setTimeout(hideCombinedData, 200);
        });
    });
});
</script>