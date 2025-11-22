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
    
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    textarea:focus {
        transform: translateY(-1px);
    }
    
    .size-checkbox:checked {
        background-image: linear-gradient(45deg, #fb923c, #f97316);
        border-color: #f97316;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .product-item {
        animation: fadeIn 0.3s ease-out;
    }
    
    /* Gradient text effect for headers */
    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Shimmer effect for loading */
    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }
    
    .shimmer {
        animation: shimmer 2s infinite;
        background: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
        background-size: 1000px 100%;
    }
</style>

<div class="max-w-6xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-plus-circle mr-2"></i> Add New Product
            </h1>
            <a href="<?php echo APP_URL; ?>/products" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Column 1: Add Product Form -->
        <div class="lg:col-span-2 bg-gradient-to-br from-white to-blue-50 shadow-xl rounded-xl p-8 border border-blue-100">
            <div class="flex items-center mb-6">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-cube text-white text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800 ml-4">Product Information</h2>
            </div>
            
            <?php if (isset($error)) : ?>
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                <?php if (isset($existing_product)) : ?>
                <div class="mt-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="update_existing" value="1" form="productForm" class="mr-2">
                        <span class="text-sm">Update the existing product with this part number</span>
                    </label>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (isset($prefill)) : ?>
            <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">Product with part number "<?php echo htmlspecialchars($prefill['part_number']); ?>" found. Form has been prefilled.</span>
            </div>
            <?php endif; ?>

            <!-- Product Mode Indicator -->
            <div id="productModeIndicator" class="hidden mb-6 p-4 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200">
                <div class="flex items-center">
                    <div class="bg-blue-500 p-2 rounded-full mr-3">
                        <i class="fas fa-info-circle text-white text-sm"></i>
                    </div>
                    <span id="modeMessage" class="font-medium text-blue-800"></span>
                    <button type="button" onclick="resetForm()" class="ml-auto bg-white px-3 py-1 rounded-lg text-sm text-blue-600 hover:bg-blue-50 transition-colors shadow-sm border border-blue-200">
                        <i class="fas fa-times mr-1"></i> Clear Form
                    </button>
                </div>
            </div>

            <form method="POST" action="<?php echo APP_URL; ?>/products/add" class="space-y-4" id="productForm">
                <?php require_once __DIR__ . '/../../Helpers/csrf.php';
                echo csrf_field(); ?>
                <input type="hidden" id="isExistingProduct" name="isExistingProduct" value="0">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Part Number (Used as Barcode) -->
                    <div class="group">
                        <label for="part_number" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-barcode text-purple-500 mr-1"></i>
                            Part Number (Barcode) <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="part_number" 
                                   id="part_number" 
                                   required
                                   value="<?php echo htmlspecialchars($_POST['part_number'] ?? $prefill['part_number'] ?? ''); ?>"
                                   class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 text-sm font-medium editable-field hover:border-purple-300"
                                   placeholder="e.g., PN-001 or scan barcode"
                                   onblur="checkExistingProduct(this.value)"
                                   onkeyup="searchProductByPartNumber(this.value)">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-qrcode text-gray-400 group-hover:text-purple-500 transition-colors"></i>
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-1 text-blue-400"></i>
                            Scan or type the barcode
                        </p>
                    </div>
 <!-- Category Dropdown -->
                    <div class="group">
                        <label for="category_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-folder text-green-500 mr-1"></i>
                            Category
                        </label>
                        <div class="relative" id="category_search_container">
                            <input type="text" 
                                   id="category_name"
                                   class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-200 text-sm font-medium hover:border-green-300"
                                   placeholder="Type or select a category..."
                                   value="<?php echo htmlspecialchars($_POST['category_name'] ?? $prefill['category_name'] ?? ''); ?>">
                            <input type="hidden" name="category_id" id="category_id" value="<?php echo $_POST['category_id'] ?? $prefill['category_id'] ?? ''; ?>">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400 group-hover:text-green-500 transition-colors"></i>
                            </div>
                            <div id="category_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                        </div>
                    </div>
                    
                    <!-- Product Type Dropdown (filtered by category) -->
                    <div class="group">
                        <label for="product_type_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag text-blue-500 mr-1"></i>
                            Product Type <span class="text-red-400">*</span>
                        </label>
                        <div class="relative" id="type_search_container">
                            <input type="text"
                                   id="product_type_name"
                                   required
                                   class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 text-sm font-medium hover:border-blue-300"
                                   placeholder="Type or select product type..."
                                   value="<?php echo htmlspecialchars($_POST['type_name'] ?? $prefill['type_name'] ?? ''); ?>">
                            <input type="hidden" name="type_id" id="type_id" required value="<?php echo htmlspecialchars($_POST['type_id'] ?? $prefill['type_id'] ?? ''); ?>">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                            </div>
                            <div id="type_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden max-h-60 overflow-y-auto"></div>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-1 text-blue-400"></i>
                            Select a category first to filter types.
                        </p>
                    </div>

                   

                    <!-- Low Stock Threshold -->
                    <div class="group">
                        <label for="low_stock_threshold" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-battery-quarter text-red-500 mr-1"></i>
                            Low Stock Threshold
                        </label>
                        <div class="relative">
                            <input type="number"
                                   min="0"
                                   name="low_stock_threshold"
                                   id="low_stock_threshold"
                                   value="<?php echo htmlspecialchars($_POST['low_stock_threshold'] ?? $prefill['low_stock_threshold'] ?? '5'); ?>"
                                   class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-red-500 focus:ring-4 focus:ring-red-100 transition-all duration-200 text-sm font-medium editable-field hover:border-red-300"
                                   placeholder="e.g., 5">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-exclamation-triangle text-gray-400"></i>
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500">
                            When total stock falls at or below this number, the product is flagged as low stock.
                        </p>
                    </div>

                    <!-- Available Sizes -->
                    <div class="group">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-ruler text-orange-500 mr-1"></i>
                            Available Sizes
                        </label>
                        <div class="bg-gradient-to-r from-orange-50 to-yellow-50 p-4 rounded-lg border border-orange-200">
                            <div class="grid grid-cols-4 gap-3">
                                <?php
                                $selectedSizes = $_POST['sizes'] ?? [];
                                if (isset($prefill['available_sizes'])) {
                                    $selectedSizes = explode(',', $prefill['available_sizes']);
                                }
                                foreach ($sizes ?? [] as $size) :
                                    $isOneSize = ($size['size'] === 'One Size');
                                    ?>
                                <label class="flex items-center cursor-pointer hover:bg-white rounded-lg p-2 transition-all duration-200 size-label <?php echo $isOneSize ? 'one-size-label' : 'regular-size-label'; ?>">
                                    <input type="checkbox" 
                                           name="sizes[]" 
                                           value="<?php echo $size['id']; ?>"
                                           <?php echo in_array($size['id'], $selectedSizes) ? 'checked' : ''; ?>
                                           class="mr-2 size-checkbox w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2"
                                           data-size-id="<?php echo $size['id']; ?>"
                                           data-is-one-size="<?php echo $isOneSize ? 'true' : 'false'; ?>">
                                    <span class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($size['size']); ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            <p class="mt-3 text-xs text-gray-600 flex items-center">
                                <i class="fas fa-info-circle mr-1 text-blue-400"></i>
                                Note: "One Size" cannot be combined with other sizes
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Description (Full Width) -->
                <div class="group mt-6">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-align-left text-indigo-500 mr-1"></i>
                        Description
                    </label>
                    <div class="relative">
                        <textarea name="description" 
                                  id="description" 
                                  rows="4"
                                  class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-sm resize-none hover:border-indigo-300"
                                  placeholder="Add a detailed product description..."><?php echo htmlspecialchars($_POST['description'] ?? $prefill['description'] ?? ''); ?></textarea>
                        <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                            <i class="fas fa-pencil-alt"></i>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t-2 border-gray-100">
                    <div id="formStatus" class="text-sm text-gray-600 font-medium"></div>
                    <div class="flex space-x-3">
                        <a href="<?php echo APP_URL; ?>/products" 
                           class="bg-gradient-to-r from-gray-200 to-gray-300 hover:from-gray-300 hover:to-gray-400 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="fas fa-arrow-left mr-2"></i> Cancel
                        </a>
                        <button type="submit" 
                                id="submitBtn"
                                class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> <span id="submitBtnText">Add Product</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Column 2: Lookup Data Panel -->
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
                    <p class="text-xs mt-2">Category or Type fields will show master data</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Width Section: Inventory Details -->
    <div id="inventoryDetailsSection" class="bg-white shadow rounded-lg p-6 hidden">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-warehouse mr-2"></i> Inventory Details
            </h2>
            <button onclick="closeInventoryDetails()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="inventoryDetailsContent">
            <!-- Inventory details will be loaded here via AJAX -->
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-spinner fa-spin text-2xl"></i>
                <p class="mt-2">Loading inventory details...</p>
            </div>
        </div>
    </div>
</div>

<script>
// Store data from PHP
const productData = {
    categories: <?php echo json_encode($categories ?? []); ?>,
    types: <?php echo json_encode($types ?? []); ?>
};

console.log('Categories loaded:', productData.categories);
console.log('Types loaded:', productData.types);

// Filter products in the grid
function filterProducts(searchTerm) {
    const term = searchTerm.toLowerCase();
    const products = document.querySelectorAll('.product-item');
    
    products.forEach(product => {
        const name = product.getAttribute('data-product-name') || '';
        const category = product.getAttribute('data-category') || '';
        
        if (name.includes(term) || category.includes(term)) {
            product.style.display = '';
        } else {
            product.style.display = 'none';
        }
    });
}

// Filter types based on selected category (for lookup panel display)
function filterTypesByCategory(categoryId) {
    // Filter types and update lookup panel
    const filteredTypes = categoryId ? 
        productData.types.filter(type => type.category_id == categoryId) : 
        productData.types;
    
    // Update lookup panel with filtered types
    const categoryName = productData.categories.find(c => c.id == categoryId)?.name || 'Selected Category';
    displayLookupData('types', filteredTypes, `Types in ${categoryName}`);
}

// Load product data when clicked
function loadProductData(partNumber) {
    // Make AJAX call to get product data
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `<?php echo APP_URL; ?>/ajax.php?action=getProduct&part_number=${encodeURIComponent(partNumber)}`, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const data = JSON.parse(xhr.responseText);
                console.log('Product data received:', data);
                if (data.success && data.product) {
                    // Fill the form with product data
                    fillFormWithProduct(data.product);
                    // Load inventory details
                    loadInventoryDetails(data.product.id);
                } else {
                    console.error('Product not found');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
            }
        }
    };
    
    xhr.onerror = function() {
        console.error('Request failed');
    };
    
    xhr.send();
}

// Fill form with product data
function fillFormWithProduct(product, isExisting = true) {
    // Fill form fields
    document.getElementById('part_number').value = product.part_number || '';
    document.getElementById('product_type_name').value = product.type_name || ''; // Use type_name
    document.getElementById('type_id').value = product.type_id || ''; // Use type_id
    document.getElementById('category_name').value = product.category_name || '';
    document.getElementById('category_id').value = product.category_id || '';
    document.getElementById('description').value = product.description || '';
    
    // Clear all size checkboxes first
    document.querySelectorAll('.size-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Check appropriate sizes
    if (product.available_sizes) {
        const sizes = product.available_sizes.split(',');
        sizes.forEach(size => {
            const checkbox = document.querySelector(`.size-checkbox[value="${size.trim()}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    }
    
    if (isExisting) {
        // Set form to existing product mode
        setFormMode('existing', product);
    }
    
    // Scroll to form
    document.querySelector('.bg-white').scrollIntoView({ behavior: 'smooth' });
}

// Set form mode (new or existing product)
function setFormMode(mode, product = null) {
    const indicator = document.getElementById('productModeIndicator');
    const modeMessage = document.getElementById('modeMessage');
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const formStatus = document.getElementById('formStatus');
    const isExistingField = document.getElementById('isExistingProduct');
    
    if (mode === 'existing' && product) {
        // Show indicator
        indicator.classList.remove('hidden');
        indicator.classList.add('bg-blue-50', 'border', 'border-blue-200', 'text-blue-700');
        modeMessage.textContent = `Editing existing product: ${product.type_name}`; // Use type_name
        
        // Update submit button
        submitBtnText.textContent = 'Update Product';
        submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
        
        // Set hidden field
        isExistingField.value = '1';
        
        // Disable fields except description and sizes
        disableFormFields(true);
        
        // Clear form status (no message needed)
        formStatus.textContent = '';
    } else {
        // Reset to new product mode
        indicator.classList.add('hidden');
        indicator.classList.remove('bg-blue-50', 'border', 'border-blue-200', 'text-blue-700');
        
        // Reset submit button
        submitBtnText.textContent = 'Add Product';
        submitBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
        submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        
        // Reset hidden field
        isExistingField.value = '0';
        
        // Enable all fields
        disableFormFields(false);
        
        // Clear form status
        formStatus.textContent = '';
    }
}

// Disable/Enable form fields
function disableFormFields(disable) {
    // Only disable product_type_name and category_name, NOT part_number
    const fieldsToDisable = ['product_type_name', 'category_name'];
    
    fieldsToDisable.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            if (disable) {
                field.setAttribute('readonly', 'readonly');
                field.classList.add('bg-gray-100', 'cursor-not-allowed');
                field.classList.remove('bg-white');
            } else {
                field.removeAttribute('readonly');
                field.classList.remove('bg-gray-100', 'cursor-not-allowed');
                field.classList.add('bg-white');
            }
        }
    });
    
    // Part number, description and sizes remain editable
    const partNumber = document.getElementById('part_number');
    const description = document.getElementById('description');
    const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
    
    if (partNumber) {
        partNumber.removeAttribute('readonly');
        partNumber.classList.remove('bg-gray-100', 'cursor-not-allowed');
    }
    
    if (description) {
        description.removeAttribute('readonly');
        description.classList.remove('bg-gray-100', 'cursor-not-allowed');
    }
    
    sizeCheckboxes.forEach(checkbox => {
        checkbox.removeAttribute('disabled');
    });
}

// Reset form to new product mode
function resetForm() {
    // Clear all form fields
    document.getElementById('productForm').reset();
    
    // Reset to new product mode
    setFormMode('new');
    
    // Close inventory details
    closeInventoryDetails();
    
    // Focus on part number field
    document.getElementById('part_number').focus();
}

// Search product by part number (triggered on keyup)
function searchProductByPartNumber(partNumber) {
    if (partNumber.length > 2) {
        // Load inventory details if product exists
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `<?php echo APP_URL; ?>/ajax.php?action=getProduct&part_number=${encodeURIComponent(partNumber)}`, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.success && data.product) {
                        loadInventoryDetails(data.product.id);
                    } else {
                        closeInventoryDetails();
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    closeInventoryDetails();
                }
            }
        };
        
        xhr.send();
    } else {
        closeInventoryDetails();
    }
}

// Load inventory details
function loadInventoryDetails(productId) {
    const section = document.getElementById('inventoryDetailsSection');
    const content = document.getElementById('inventoryDetailsContent');
    
    section.classList.remove('hidden');
    content.innerHTML = `
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-spinner fa-spin text-2xl"></i>
            <p class="mt-2">Loading inventory details...</p>
        </div>
    `;
    
    // Fetch inventory details
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `<?php echo APP_URL; ?>/ajax.php?action=getInventory&product_id=${productId}`, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const data = JSON.parse(xhr.responseText);
                console.log('Inventory data received:', data);
                if (data.success) {
                    displayInventoryDetails(data.inventory, data.product);
                } else {
                    content.innerHTML = `
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-exclamation-circle text-2xl"></i>
                            <p class="mt-2">No inventory data available</p>
                        </div>
                    `;
                }
            } catch (e) {
                console.error('Error parsing inventory response:', e);
                content.innerHTML = `
                    <div class="text-center py-8 text-red-500">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                        <p class="mt-2">Error loading inventory details</p>
                    </div>
                `;
            }
        }
    };
    
    xhr.onerror = function() {
        content.innerHTML = `
            <div class="text-center py-8 text-red-500">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
                <p class="mt-2">Error loading inventory details</p>
            </div>
        `;
    };
    
    xhr.send();
}

// Display inventory details
function displayInventoryDetails(inventory, product) {
    const content = document.getElementById('inventoryDetailsContent');
    
    if (!inventory || inventory.length === 0) {
        content.innerHTML = `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">No Inventory</h3>
                        <p class="mt-1 text-sm text-yellow-700">
                            This product has not been added to inventory yet.
                        </p>
                    </div>
                </div>
            </div>
        `;
        return;
    }
    
    // Calculate total stock
    const totalStock = inventory.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0);
    
    let html = `
        <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-box text-blue-500 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-900">Product</p>
                        <p class="text-lg font-bold text-blue-700">${product.product_type}</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-cubes text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-900">Total Stock</p>
                        <p class="text-lg font-bold text-green-700">${totalStock} units</p>
                    </div>
                </div>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-warehouse text-purple-500 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-purple-900">Locations</p>
                        <p class="text-lg font-bold text-purple-700">${new Set(inventory.map(i => i.location_name)).size}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
    `;
    
    inventory.forEach(item => {
        const statusClass = item.quantity > 10 ? 'bg-green-100 text-green-800' : 
                          item.quantity > 5 ? 'bg-yellow-100 text-yellow-800' : 
                          'bg-red-100 text-red-800';
        const statusText = item.quantity > 10 ? 'In Stock' : 
                          item.quantity > 5 ? 'Low Stock' : 
                          'Critical';
        
        html += `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                    ${item.location_name}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${item.size || 'N/A'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                    ${item.quantity}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                        ${statusText}
                    </span>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    content.innerHTML = html;
}

// Close inventory details
function closeInventoryDetails() {
    document.getElementById('inventoryDetailsSection').classList.add('hidden');
}

// Check if product exists when part number is entered (onblur event)
function checkExistingProduct(partNumber) {
    if (partNumber && partNumber.trim() !== '') {
        // Check if product exists and load its data
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `<?php echo APP_URL; ?>/ajax.php?action=getProduct&part_number=${encodeURIComponent(partNumber)}`, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (data.success && data.product) {
                        // Product exists, fill the form and set to existing mode
                        console.log('Existing product found:', data.product);
                        fillFormWithProduct(data.product, true);
                        loadInventoryDetails(data.product.id);
                    } else {
                        // New product, ensure form is in new mode
                        setFormMode('new');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
            }
        };
        
        xhr.send();
    } else {
        // Empty part number, reset to new mode
        setFormMode('new');
    }
}

function updateSizeCheckboxStates() {
    const checkboxes = document.querySelectorAll('.size-checkbox');
    const oneSizeCheckbox = document.querySelector('.size-checkbox[data-is-one-size="true"]');
    const regularSizes = document.querySelectorAll('.size-checkbox[data-is-one-size="false"]');
    const anyRegularChecked = Array.from(regularSizes).some(cb => cb.checked);

    // Reset all to enabled first
    checkboxes.forEach(cb => {
        cb.disabled = false;
        cb.parentElement.style.opacity = '1';
        cb.parentElement.style.cursor = 'pointer';
    });

    if (oneSizeCheckbox && oneSizeCheckbox.checked) {
        regularSizes.forEach(cb => {
            if (cb.checked) {
                cb.checked = false; // Uncheck if it was checked
            }
            cb.disabled = true;
            cb.parentElement.style.opacity = '0.5';
            cb.parentElement.style.cursor = 'not-allowed';
        });
    } else if (anyRegularChecked) {
        if (oneSizeCheckbox) {
            if (oneSizeCheckbox.checked) {
                oneSizeCheckbox.checked = false; // Uncheck if it was checked
            }
            oneSizeCheckbox.disabled = true;
            oneSizeCheckbox.parentElement.style.opacity = '0.5';
            oneSizeCheckbox.parentElement.style.cursor = 'not-allowed';
        }
    }
}

// Handle size checkbox mutual exclusivity
function handleSizeCheckboxes() {
    const checkboxes = document.querySelectorAll('.size-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSizeCheckboxStates);
    });
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
        const displayName = type === 'categories' ? item.name : 
                           (item.name + (item.category_name ? ` <span class="text-xs text-gray-500">(${item.category_name})</span>` : ''));
        const icon = type === 'categories' ? 'fa-folder' : 'fa-tag';
        const colorClass = type === 'categories' ? 'from-green-50 to-emerald-50 border-green-200 hover:border-green-400' : 
                                                    'from-blue-50 to-indigo-50 border-blue-200 hover:border-blue-400';
        
        html += `
            <div class="lookup-item bg-gradient-to-r ${colorClass} border-2 rounded-lg p-3 cursor-pointer transition-all duration-200 hover:shadow-md transform hover:-translate-y-0.5"
                 data-type="${type}"
                 data-id="${item.id}"
                 data-name="${item.name}"
                 onclick="selectLookupItem('${type}', ${item.id}, '${item.name.replace(/'/g, "\\'")}')">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas ${icon} text-lg ${type === 'categories' ? 'text-green-600' : 'text-blue-600'}"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-semibold text-gray-800">${displayName}</p>
                        ${item.description ? `<p class="text-xs text-gray-600 mt-0.5">${item.description}</p>` : ''}
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
    if (type === 'categories') {
        document.getElementById('category_name').value = name;
        document.getElementById('category_id').value = id;
        
        // Filter types based on selected category
        filterTypesByCategory(id);
        
        // Show types in lookup panel
        const filteredTypes = productData.types.filter(t => t.category_id == id);
        displayLookupData('types', filteredTypes, `Types in ${name}`);
        
        // Focus on type field
        document.getElementById('product_type_name').focus();
        
    } else if (type === 'types') {
        document.getElementById('product_type_name').value = name;
        document.getElementById('type_id').value = id;
        
        // Focus on next field (low stock threshold or description)
        document.getElementById('low_stock_threshold').focus();
    }
}

// Filter lookup data based on search
function filterLookupData(searchTerm) {
    const term = searchTerm.toLowerCase();
    const items = document.querySelectorAll('.lookup-item');
    
    items.forEach(item => {
        const name = item.getAttribute('data-name').toLowerCase();
        const text = item.textContent.toLowerCase();
        
        if (name.includes(term) || text.includes(term)) {
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
            <p class="text-xs mt-2">Category or Type fields will show master data</p>
        </div>
    `;
}

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add focus event listeners to show lookup data
    document.getElementById('category_name').addEventListener('focus', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide
        displayLookupData('categories', productData.categories, 'Available Categories');
    });
    
    document.getElementById('product_type_name').addEventListener('focus', function() {
        clearTimeout(hideTimeout); // Cancel any pending hide
        const categoryId = document.getElementById('category_id').value;
        if (categoryId) {
            const filteredTypes = productData.types.filter(t => t.category_id == categoryId);
            const categoryName = productData.categories.find(c => c.id == categoryId)?.name || 'Selected Category';
            displayLookupData('types', filteredTypes, `Types in ${categoryName}`);
        } else {
            displayLookupData('types', productData.types, 'All Product Types');
        }
    });

    // Check if there's a prefilled product (from URL parameter)
    const partNumberField = document.getElementById('part_number');
    if (partNumberField && partNumberField.value) {
        checkExistingProduct(partNumberField.value);
    }
    
    // Add event listener for Enter key on part number field (for barcode scanning)
    if (partNumberField) {
        partNumberField.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.blur(); // Trigger onblur event which calls checkExistingProduct
            }
        });
    }
    
    // Initialize size checkbox handlers
    handleSizeCheckboxes();
    updateSizeCheckboxStates(); // Initial check
    
    // If there's a pre-selected category, filter types
    const categoryId = document.getElementById('category_id').value;
    if (categoryId) {
        filterTypesByCategory(categoryId);
    }
    
    // Add blur event listeners to hide lookup data
    const formFields = ['category_name', 'product_type_name'];
    formFields.forEach(fieldId => {
        document.getElementById(fieldId).addEventListener('blur', function() {
            // Delay to allow click on lookup items
            hideTimeout = setTimeout(hideLookupData, 200);
        });
    });
});

</script>