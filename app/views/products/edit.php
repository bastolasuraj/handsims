<style>
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .slide-down {
        animation: slideDown 0.5s ease-out;
    }
    
    .form-input:focus {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.2);
    }
    
    .checkbox-custom {
        transition: all 0.3s ease;
    }
    
    .checkbox-custom:checked {
        transform: scale(1.1);
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    .pulse-animation {
        animation: pulse 2s infinite;
    }
</style>

<div class="max-w-5xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl shadow-2xl p-8 text-white slide-down">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-white/20 backdrop-blur-lg p-3 rounded-lg mr-4">
                    <i class="fas fa-edit text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Edit Product</h1>
                    <p class="text-white/80 mt-1">Update product information and details</p>
                </div>
            </div>
            <a href="<?php echo APP_URL; ?>/products" 
               class="bg-white/20 backdrop-blur-lg hover:bg-white/30 p-3 rounded-lg transition-all duration-300 transform hover:scale-110">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
    </div>

    <?php if (isset($error)) : ?>
    <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md slide-down" style="animation-delay: 0.1s">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
            <span class="text-red-700 font-medium"><?php echo htmlspecialchars($error); ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Edit Product Form -->
    <div class="bg-gradient-to-br from-white to-purple-50 shadow-2xl rounded-xl p-8 border border-purple-100 slide-down" style="animation-delay: 0.2s">
        <form method="POST" action="<?php echo APP_URL; ?>/products/edit?id=<?php echo $product['id']; ?>" class="space-y-8">
            <?php require_once __DIR__ . '/../../Helpers/csrf.php';
            echo csrf_field(); ?>
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            
            <!-- Basic Information Section -->
            <div>
                <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-2 rounded-lg mr-3 shadow-lg">
                        <i class="fas fa-info-circle text-white text-sm"></i>
                    </div>
                    Basic Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Part Number -->
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
                                   value="<?php echo htmlspecialchars($_POST['part_number'] ?? $product['part_number']); ?>"
                                   class="form-input block w-full px-4 py-3 pl-12 rounded-lg border-2 border-gray-200 shadow-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 text-sm font-medium hover:border-purple-300"
                                   placeholder="e.g., PN-001">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fas fa-barcode text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-1 text-blue-400"></i>
                            This will be used as the barcode identifier
                        </p>
                    </div>

                    <!-- Product Type Dropdown -->
                    <div class="group">
                        <label for="type_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag text-blue-500 mr-1"></i>
                            Product Type <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <select name="type_id" 
                                    id="type_id" 
                                    required
                                    class="form-input block w-full px-4 py-3 pl-12 rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 text-sm font-medium hover:border-blue-300">
                                <?php foreach ($types ?? [] as $type) : ?>
                                    <option value="<?php echo htmlspecialchars($type['id']); ?>" 
                                            <?php echo (($_POST['type_id'] ?? $product['type_id']) == $type['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type['name']); ?>
                                        <?php echo !empty($type['category_name']) ? ' (' . htmlspecialchars($type['category_name']) . ')' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fas fa-tag text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="group">
                        <label for="category_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-folder text-green-500 mr-1"></i>
                            Category
                        </label>
                        <?php
                        $currentCategoryName = '';
                        foreach ($categories ?? [] as $cat) {
                            if ($cat['id'] == ($_POST['category_id'] ?? $product['category_id'])) {
                                $currentCategoryName = $cat['name'];
                                break;
                            }
                        }
                        ?>
                        <div class="relative">
                            <input type="text" 
                                   name="category_name" 
                                   id="category_name" 
                                   list="categories_list"
                                   value="<?php echo htmlspecialchars($currentCategoryName); ?>"
                                   class="form-input block w-full px-4 py-3 pl-12 rounded-lg border-2 border-gray-200 shadow-sm focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-200 text-sm font-medium hover:border-green-300"
                                   placeholder="Type or select a category"
                                   onchange="updateCategoryId(this.value)">
                            <datalist id="categories_list">
                                <?php foreach ($categories ?? [] as $category) : ?>
                                <option value="<?php echo htmlspecialchars($category['name']); ?>" data-id="<?php echo $category['id']; ?>">
                                <?php endforeach; ?>
                            </datalist>
                            <input type="hidden" name="category_id" id="category_id" value="<?php echo $_POST['category_id'] ?? $product['category_id'] ?? ''; ?>">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fas fa-folder text-gray-400 group-focus-within:text-green-500 transition-colors"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Placeholder for alignment -->
                    <div></div>
                </div>
            </div>

            <!-- Sizes Section -->
            <div>
                <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <div class="bg-gradient-to-r from-orange-500 to-red-600 p-2 rounded-lg mr-3 shadow-lg">
                        <i class="fas fa-ruler text-white text-sm"></i>
                    </div>
                    Available Sizes
                </h2>
                
                <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-lg p-6 border border-orange-200">
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
                        <?php
                        $selectedSizes = $_POST['sizes'] ?? [];
                        if (!empty($product['available_sizes'])) {
                            $selectedSizes = explode(',', $product['available_sizes']);
                        }
                        foreach ($sizes ?? [] as $size) :
                            $isOneSize = ($size['size'] === 'One Size');
                            ?>
                        <label class="flex items-center justify-center bg-white rounded-lg p-3 border-2 border-gray-200 cursor-pointer hover:border-orange-400 hover:bg-orange-50 transition-all duration-200 has-[:checked]:border-orange-500 has-[:checked]:bg-gradient-to-r has-[:checked]:from-orange-100 has-[:checked]:to-red-100 size-label <?php echo $isOneSize ? 'one-size-label' : 'regular-size-label'; ?>">
                            <input type="checkbox" 
                                   name="sizes[]"
                                   value="<?php echo $size['id']; ?>"
                                   <?php echo in_array($size['id'], $selectedSizes) ? 'checked' : ''; ?>
                                   class="checkbox-custom mr-2 text-orange-600 focus:ring-orange-500 size-checkbox"
                                   data-size-id="<?php echo $size['id']; ?>"
                                   data-is-one-size="<?php echo $isOneSize ? 'true' : 'false'; ?>">
                            <span class="text-sm font-medium"><?php echo htmlspecialchars($size['size']); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="mt-4 text-xs text-gray-600 flex items-center">
                        <i class="fas fa-info-circle mr-1 text-blue-400"></i>
                        Note: "One Size" cannot be combined with other sizes
                    </p>
                </div>
            </div>

            <!-- Description Section -->
            <div>
                <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-2 rounded-lg mr-3 shadow-lg">
                        <i class="fas fa-align-left text-white text-sm"></i>
                    </div>
                    Product Description
                </h2>
                
                <div class="group">
                    <div class="relative">
                        <textarea name="description" 
                                  id="description" 
                                  rows="4"
                                  class="form-input block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-sm resize-none hover:border-indigo-300"
                                  placeholder="Enter a detailed product description..."><?php echo htmlspecialchars($_POST['description'] ?? $product['description']); ?></textarea>
                        <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                            <i class="fas fa-pencil-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Information Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Product Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-3 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Created</span>
                            <i class="fas fa-calendar-plus text-blue-400 text-xs"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 mt-1">
                            <?php echo date('M d, Y', strtotime($product['created_at'])); ?>
                        </p>
                        <p class="text-xs text-gray-500">
                            <?php echo date('h:i A', strtotime($product['created_at'])); ?>
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-lg p-3 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Last Updated</span>
                            <i class="fas fa-calendar-check text-green-400 text-xs"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 mt-1">
                            <?php echo date('M d, Y', strtotime($product['updated_at'])); ?>
                        </p>
                        <p class="text-xs text-gray-500">
                            <?php echo date('h:i A', strtotime($product['updated_at'])); ?>
                        </p>
                    </div>
                    
                    <?php if (isset($product['total_stock'])) : ?>
                    <div class="bg-gradient-to-r from-green-100 to-emerald-100 rounded-lg p-3 border border-green-300">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-green-700">Current Stock</span>
                            <i class="fas fa-boxes text-green-600 text-xs"></i>
                        </div>
                        <p class="text-2xl font-bold text-green-800 mt-1">
                            <?php echo $product['total_stock'] ?? 0; ?>
                        </p>
                        <p class="text-xs text-green-600">units in inventory</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between items-center pt-6 border-t-2 border-gray-200">
                <div>
                    <?php if (($_SESSION['role'] ?? '') === 'admin') : ?>
                    <button type="button"
                            onclick="confirmDelete()"
                            class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-trash mr-2"></i> Delete Product
                    </button>
                    <?php endif; ?>
                </div>
                <div class="flex space-x-3">
                    <a href="<?php echo APP_URL; ?>/products" 
                       class="bg-gradient-to-r from-gray-200 to-gray-300 hover:from-gray-300 hover:to-gray-400 text-gray-700 font-bold py-3 px-6 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                    <button type="submit" 
                            class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-3 px-8 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 pulse-animation">
                        <i class="fas fa-save mr-2"></i> Update Product
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200 slide-down" style="animation-delay: 0.3s">
        <h3 class="text-sm font-bold text-gray-700 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="<?php echo APP_URL; ?>/products/detail?id=<?php echo $product['id']; ?>" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-eye text-blue-500 mr-2"></i>
                <span class="text-sm font-medium">View Product</span>
            </a>
            <a href="<?php echo APP_URL; ?>/stock/add?product_id=<?php echo $product['id']; ?>" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-plus-circle text-green-500 mr-2"></i>
                <span class="text-sm font-medium">Add Stock</span>
            </a>
            <a href="<?php echo APP_URL; ?>/barcode.php?product_id=<?php echo $product['id']; ?>" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-barcode text-purple-500 mr-2"></i>
                <span class="text-sm font-medium">Generate Barcode</span>
            </a>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-red-500 to-rose-600 p-6">
            <div class="flex items-center">
                <div class="bg-white/20 backdrop-blur-lg p-3 rounded-lg mr-4">
                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">Confirm Deletion</h3>
                    <p class="text-white/80 text-sm mt-1">This action cannot be undone</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <p class="text-gray-700 mb-4">
                Are you sure you want to delete the product <strong><?php echo htmlspecialchars($product['part_number']); ?></strong>?
            </p>
            <p class="text-sm text-gray-500 mb-6">
                <i class="fas fa-info-circle mr-1"></i>
                This will permanently remove the product and all associated inventory records.
            </p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()" 
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                    Cancel
                </button>
                <a href="<?php echo APP_URL; ?>/products/delete?id=<?php echo $product['id']; ?>" 
                   class="px-6 py-2 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-medium rounded-lg transition-all duration-200">
                    <i class="fas fa-trash mr-2"></i> Delete Product
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Update category ID based on selected category name
function updateCategoryId(categoryName) {
    const categories = document.querySelectorAll('#categories_list option');
    let categoryId = '';
    
    categories.forEach(option => {
        if (option.value === categoryName) {
            categoryId = option.getAttribute('data-id');
        }
    });
    
    document.getElementById('category_id').value = categoryId;
}

// Delete confirmation modal
function confirmDelete() {
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize size checkbox handlers
    handleSizeCheckboxes();
    updateSizeCheckboxStates(); // Initial check
});

// Add visual feedback when form is being submitted
document.querySelector('form').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
    submitBtn.disabled = true;
});
</script>