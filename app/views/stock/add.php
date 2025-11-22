<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f3e8ff;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #a78bfa 0%, #c084fc 100%);
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #8b5cf6 0%, #a855f7 100%);
    }
    
    select:focus, input:focus, textarea:focus {
        transform: translateY(-1px);
    }
</style>

<div class="max-w-6xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-plus-circle text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-800">Add Stock</h1>
                    <p class="text-sm text-gray-600">Add new inventory items to warehouse locations</p>
                </div>
            </div>
            <button id="bulkAddBtn" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                <i class="fas fa-upload mr-2"></i> Bulk Add
            </button>
        </div>
    </div>

    <!-- Bulk Add Modal -->
    <div id="bulkAddModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="<?php echo APP_URL; ?>/stock/bulk_add" method="POST" enctype="multipart/form-data">
                    <?php require_once __DIR__ . '/../../Helpers/csrf.php';
                    echo csrf_field(); ?>
                    <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-file-csv text-white text-xl"></i>
                            </div>
                            <h3 class="ml-4 text-lg font-bold text-white" id="modal-title">
                                Bulk Stock Upload
                            </h3>
                        </div>
                    </div>
                    <div class="bg-white px-6 py-6">
                        <p class="text-sm text-gray-600 mb-4">
                            To bulk add stock, create a CSV file with the following columns. The first row must be the header.
                        </p>
                        <div class="bg-gray-100 p-3 rounded-lg mb-4 overflow-x-auto">
                            <code class="text-sm text-gray-800">part_number,location_name,size_id,quantity,remarks,seller_id,price_per_unit</code>
                        </div>
                        <div class="text-xs text-gray-500 space-y-1 mb-4">
                            <p><strong>part_number:</strong> The part number of the product.</p>
                            <p><strong>location_name:</strong> The name of the warehouse or location.</p>
                            <p><strong>size_id:</strong> The ID of the product size. Leave empty if not applicable.</p>
                            <p><strong>quantity:</strong> The number of items to add.</p>
                            <p><strong>remarks:</strong> Optional notes.</p>
                            <p><strong>seller_id:</strong> Optional seller ID.</p>
                            <p><strong>price_per_unit:</strong> Optional price per unit.</p>
                        </div>
                        <a href="<?php echo APP_URL; ?>/public/samples/stock_sample.csv" download class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-download mr-2"></i> Download Sample
                        </a>
                        <div class="mt-4">
                            <label for="bulk_file" class="block text-sm font-semibold text-gray-700 mb-2">Upload CSV File</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <i class="fas fa-upload mx-auto h-12 w-12 text-gray-400"></i>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="bulk_file_input" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                            <span>Upload a file</span>
                                            <input id="bulk_file_input" name="bulk_file" type="file" class="sr-only" accept=".csv">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">CSV up to 10MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                        <button type="button" id="closeModalBtn" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <i class="fas fa-upload mr-2"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Bulk Add Modal -->

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Stock Form (2 columns) -->
        <div class="lg:col-span-2 bg-gradient-to-br from-white to-green-50 shadow-xl rounded-xl p-8 border border-green-100">
        <form action="<?php echo APP_URL; ?>/stock/add" method="POST" class="space-y-6">
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
                               class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 text-sm font-medium hover:border-purple-300"
                               placeholder="Type or select product..."
                               value="<?php echo isset($selected_product) ? htmlspecialchars($selected_product['part_number'] . ' - ' . $selected_product['product_type']) : ''; ?>">
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
                        Location <span class="text-red-400">*</span>
                    </label>
                    <div class="relative" id="location_search_container">
                        <input type="text" 
                               id="location_name"
                               required
                               class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 text-sm font-medium hover:border-blue-300"
                               placeholder="Type or select location..."
                               value="<?php echo isset($selected_location) ? htmlspecialchars($selected_location['name']) : ''; ?>">
                        <input type="hidden" name="location_id" id="location_id" required value="<?php echo isset($selected_location) ? $selected_location['id'] : ''; ?>">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                        </div>
                        <div id="location_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
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
                <div class="group">
                    <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sort-numeric-up text-green-500 mr-1"></i>
                        Quantity <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="quantity" name="quantity" min="1" required
                               class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-200 text-sm font-medium hover:border-green-300"
                               placeholder="Enter quantity">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-calculator text-gray-400 group-hover:text-green-500 transition-colors"></i>
                        </div>
                    </div>
                </div>

                <!-- Seller -->
                <div class="group">
                    <label for="seller_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tie text-fuchsia-500 mr-1"></i>
                        Seller
                    </label>
                    <div class="relative" id="seller_search_container">
                        <input type="text" 
                               id="seller_name"
                               class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-100 transition-all duration-200 text-sm font-medium hover:border-fuchsia-300"
                               placeholder="Type or select seller...">
                        <input type="hidden" name="seller_id" id="seller_id">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400 group-hover:text-fuchsia-500 transition-colors"></i>
                        </div>
                        <div id="seller_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                </div>

                <!-- Price Per Unit -->
                <div class="group">
                    <label for="price_per_unit" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-dollar-sign text-emerald-500 mr-1"></i>
                        Price per unit
                    </label>
<!--                    <p class="text-xs text-gray-500 mb-2">Price is just for information, it is not calculated anywhere</p>-->
                    <div class="relative">
                        <input type="number" step="0.01" min="0" id="price_per_unit" name="price_per_unit"
                               class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-200 text-sm font-medium hover:border-emerald-300"
                               placeholder="Enter price per unit (optional)">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-coins text-gray-400 group-hover:text-emerald-500 transition-colors"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remarks (Full Width) -->
            <div class="group">
                <label for="remarks" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-comment-alt text-indigo-500 mr-1"></i>
                    Remarks / Description
                </label>
                <div class="relative">
                    <textarea id="remarks" name="remarks" rows="4"
                              class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-sm resize-none hover:border-indigo-300"
                              placeholder="Optional notes or description..."></textarea>
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
                        class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> Add Stock
                </button>
            </div>
        </form>
        </div>

        <!-- Lookup Data Panel (1 column) -->
        <div class="lg:col-span-1 bg-gradient-to-br from-white to-purple-50 shadow-xl rounded-xl p-6 border border-purple-100">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-2 rounded-lg shadow-lg">
                        <i class="fas fa-list text-white text-lg"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 ml-3" id="lookupTitle">Master Data Lookup</h2>
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
            
            <!-- Lookup Data Container -->
            <div id="lookupDataContainer" class="overflow-y-auto custom-scrollbar" style="max-height: 500px;">
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-mouse-pointer text-4xl mb-3"></i>
                    <p class="text-sm">Click on a field to see available options</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Add Product Link -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 text-center border border-blue-200">
        <p class="text-sm text-gray-700">
            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
            Product not in the list? 
            <a href="<?php echo APP_URL; ?>/products/add" class="text-blue-600 hover:text-blue-700 font-semibold underline decoration-2 decoration-blue-300 hover:decoration-blue-500 transition-all">
                Add new product first
            </a>
        </p>
    </div>
</div>

<script>
// Modal handling
const bulkAddBtn = document.getElementById('bulkAddBtn');
const closeModalBtn = document.getElementById('closeModalBtn');
const bulkAddModal = document.getElementById('bulkAddModal');

bulkAddBtn.addEventListener('click', () => {
    bulkAddModal.classList.remove('hidden');
});

closeModalBtn.addEventListener('click', () => {
    bulkAddModal.classList.add('hidden');
});

window.addEventListener('click', (event) => {
    if (event.target == bulkAddModal) {
        bulkAddModal.classList.add('hidden');
    }
});

// Store data
const inventoryData = {
    products: <?php echo json_encode($products); ?>,
    locations: <?php echo json_encode($locations); ?>,
    sizes: <?php echo json_encode($sizes); ?>, // All sizes - will be filtered based on product
    sellers: <?php echo json_encode($sellers); ?>,
    productSizes: {} // Cache for product-specific sizes
};

// Function to get product-specific sizes
function getProductSizes() {
    const productId = document.getElementById('product_id').value;
    
    if (!productId) {
        return [];
    }
    
    // Find the selected product
    const selectedProduct = inventoryData.products.find(p => p.id == productId);
    
    if (!selectedProduct || !selectedProduct.available_sizes) {
        return [];
    }
    
    // Parse available sizes (comma-separated IDs)
    const sizeIds = selectedProduct.available_sizes.split(',').map(id => id.trim()).filter(id => id);
    
    // Filter sizes based on product's available sizes
    const productSizes = inventoryData.sizes.filter(size => 
        sizeIds.includes(size.id.toString())
    );
    
    return productSizes;
}

// Display lookup data in the right panel
function displayLookupData(type, data, title) {
    const container = document.getElementById('lookupDataContainer');
    const titleElement = document.getElementById('lookupTitle');
    
    titleElement.textContent = title;
    
    if (!data || data.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p class="text-sm">No ${type} available</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="grid grid-cols-1 gap-2">';
    
    data.forEach(item => {
        const displayName = item.size || item.name || (item.part_number ? `${item.part_number} - ${item.product_type}` : '');
        let icon = 'fa-cube';
        let colorClass = 'from-purple-50 to-indigo-50 border-purple-200 hover:border-purple-400';
        
        if (type === 'products') {
            icon = 'fa-box';
            colorClass = 'from-purple-50 to-indigo-50 border-purple-200 hover:border-purple-400';
        } else if (type === 'locations') {
            icon = 'fa-warehouse';
            colorClass = 'from-blue-50 to-cyan-50 border-blue-200 hover:border-blue-400';
        } else if (type === 'sizes') {
            icon = 'fa-ruler';
            colorClass = 'from-orange-50 to-amber-50 border-orange-200 hover:border-orange-400';
        } else if (type === 'sellers') {
            icon = 'fa-user-tie';
            colorClass = 'from-fuchsia-50 to-pink-50 border-fuchsia-200 hover:border-fuchsia-400';
        }
        
        html += `
            <div class="lookup-item bg-gradient-to-r ${colorClass} border-2 rounded-lg p-3 cursor-pointer transition-all duration-200 hover:shadow-md transform hover:-translate-y-0.5"
                 data-type="${type}"
                 data-id="${item.id}"
                 data-name="${displayName.replace(/"/g, '&quot;')}"
                 onclick="selectLookupItem('${type}', ${item.id}, \`${displayName.replace(/`/g, '\\`')}\`)">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas ${icon} text-lg text-gray-700"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-semibold text-gray-800">${displayName}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

// Select an item from the lookup panel
function selectLookupItem(type, id, name) {
    if (type === 'products') {
        document.getElementById('product_search').value = name;
        document.getElementById('product_id').value = id;
        document.getElementById('location_name').focus();
    } else if (type === 'locations') {
        document.getElementById('location_name').value = name;
        document.getElementById('location_id').value = id;
        document.getElementById('size_name').focus();
    } else if (type === 'sizes') {
        document.getElementById('size_name').value = name;
        document.getElementById('size_id').value = id;
        document.getElementById('quantity').focus();
    } else if (type === 'sellers') {
        document.getElementById('seller_name').value = name;
        document.getElementById('seller_id').value = id;
        document.getElementById('price_per_unit').focus();
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

function hideLookupData() {
    const container = document.getElementById('lookupDataContainer');
    const titleElement = document.getElementById('lookupTitle');
    
    titleElement.textContent = 'Master Data Lookup';
    container.innerHTML = `
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-mouse-pointer text-4xl mb-3"></i>
            <p class="text-sm">Click on a field to see available options</p>
        </div>
    `;
}

document.addEventListener('DOMContentLoaded', function() {
    // Add focus event listeners to show lookup data
    document.getElementById('product_search').addEventListener('focus', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide
        displayLookupData('products', inventoryData.products, 'Available Products');
    });
    
    // Add input event listener for product search to filter and keep lookup visible while typing
    document.getElementById('product_search').addEventListener('input', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide while typing
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            // Show all products if search is empty
            displayLookupData('products', inventoryData.products, 'Available Products');
        } else {
            // Filter products based on search term
            const filteredProducts = inventoryData.products.filter(product => {
                const productName = `${product.part_number} - ${product.product_type}`.toLowerCase();
                return productName.includes(searchTerm);
            });
            displayLookupData('products', filteredProducts, 'Available Products');
        }
    });
    
    document.getElementById('location_name').addEventListener('focus', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide
        displayLookupData('locations', inventoryData.locations, 'Available Locations');
    });
    
    // Add input event listener for location search to filter and keep lookup visible while typing
    document.getElementById('location_name').addEventListener('input', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide while typing
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            // Show all locations if search is empty
            displayLookupData('locations', inventoryData.locations, 'Available Locations');
        } else {
            // Filter locations based on search term
            const filteredLocations = inventoryData.locations.filter(location => {
                return location.name.toLowerCase().includes(searchTerm);
            });
            displayLookupData('locations', filteredLocations, 'Available Locations');
        }
    });
    
    document.getElementById('size_name').addEventListener('focus', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide
        const productSizes = getProductSizes();
        if (productSizes.length > 0) {
            displayLookupData('sizes', productSizes, 'Available Sizes');
        } else {
            displayLookupData('sizes', [], 'Available Sizes');
        }
    });
    
    // Add input event listener for size search to filter and keep lookup visible while typing
    document.getElementById('size_name').addEventListener('input', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide while typing
        const searchTerm = this.value.toLowerCase().trim();
        const productSizes = getProductSizes();
        
        if (searchTerm === '') {
            // Show all sizes if search is empty
            displayLookupData('sizes', productSizes, 'Available Sizes');
        } else {
            // Filter sizes based on search term
            const filteredSizes = productSizes.filter(size => {
                return size.size.toLowerCase().includes(searchTerm);
            });
            displayLookupData('sizes', filteredSizes, 'Available Sizes');
        }
    });
    
    document.getElementById('seller_name').addEventListener('focus', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide
        displayLookupData('sellers', inventoryData.sellers, 'Available Sellers');
    });
    
    // Add input event listener for seller search to filter and keep lookup visible while typing
    document.getElementById('seller_name').addEventListener('input', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide while typing
        const searchTerm = this.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            // Show all sellers if search is empty
            displayLookupData('sellers', inventoryData.sellers, 'Available Sellers');
        } else {
            // Filter sellers based on search term
            const filteredSellers = inventoryData.sellers.filter(seller => {
                return seller.name.toLowerCase().includes(searchTerm);
            });
            displayLookupData('sellers', filteredSellers, 'Available Sellers');
        }
    });
    
    // Add blur event listeners to hide lookup data
    const formFields = ['product_search', 'location_name', 'size_name', 'seller_name'];
    formFields.forEach(fieldId => {
        document.getElementById(fieldId).addEventListener('blur', function() {
            // Don't hide product lookup if there's text in the search field
            if (fieldId === 'product_search' && this.value.trim() !== '') {
                return; // Keep lookup visible for product search
            }
            // Delay to allow click on lookup items
            hideTimeout = setTimeout(hideLookupData, 200);
        });
    });
});
</script>