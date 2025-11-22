// public/js/master-data-ajax.js

// Immediately define global functions (before DOMContentLoaded)
(function() {
    // Global function to handle initial edit on page load
    window.initEditForm = function(type, item) {
        // Store the item and type in a global variable for the DOMContentLoaded listener to pick up
        window._initialEditData = { type: type, item: item };
    };

    // Global editItem function for inline onclick handlers
    window.editItem = function(type, item) {
        // Store the data for DOMContentLoaded to process
        if (document.readyState === 'loading') {
            // If DOM is still loading, store for later
            window._pendingEditData = { type: type, item: item };
        } else {
            // If DOM is ready, trigger the edit directly
            if (window._editItemHandler) {
                window._editItemHandler(type, item);
            } else {
                // If handler not ready yet, store for later
                window._pendingEditData = { type: type, item: item };
            }
        }
    };
})();

document.addEventListener('DOMContentLoaded', function() {
    console.log('Master data AJAX script loaded and DOMContentLoaded fired');
    
    // This script will be included in each master data management page (categories, sizes, departments, locations, sellers)
    // It expects the form and list elements to follow a consistent naming convention.

    // Helper function to get CSRF token
    function getCSRFToken() {
        const csrfInput = document.querySelector('input[name="csrf"]');
        return csrfInput ? csrfInput.value : '';
    }

    const masterDataForm = document.querySelector('form[data-master-data-type]');
    if (!masterDataForm) {
        console.warn('Master data form not found or missing data-master-data-type attribute.');
        return;
    }

    const masterDataType = masterDataForm.getAttribute('data-master-data-type'); // e.g., 'category', 'size', 'department'
    console.log('Master data type detected:', masterDataType);
    const formActionInput = masterDataForm.querySelector('input[name="action"]');
    const itemIdInput = masterDataForm.querySelector('input[name="id"]');
    const formTitleElement = document.getElementById('form-title');
    const buttonTextElement = document.getElementById('button-text');
    const formSubmitButton = document.getElementById('form-submit-button');
    const cancelEditButton = document.getElementById('cancel-edit-button'); // This might be dynamically added
    const itemListContainer = document.getElementById(`${masterDataType}-list`); // e.g., 'category-list'
    const itemCountSpan = document.getElementById(`${masterDataType}-count`); // e.g., 'category-count'
    const noItemsMessage = document.getElementById(`no-${masterDataType}s-message`); // e.g., 'no-categories-message'
    const alertArea = document.getElementById('alert-area');

    // Make editItem available globally through a handler
    window._editItemHandler = function(type, item) {
        if (type === masterDataType) {
            editItem(type, item);
        }
    };

    // Process initial edit data if present (from PHP view)
    if (window._initialEditData) {
        const { type, item } = window._initialEditData;
        if (type === masterDataType) {
            editItem(type, item);
        }
        delete window._initialEditData; // Clean up global variable
    }

    // Process pending edit data (from inline onclick before DOM ready)
    if (window._pendingEditData) {
        const { type, item } = window._pendingEditData;
        if (type === masterDataType) {
            editItem(type, item);
        }
        delete window._pendingEditData; // Clean up global variable
    }

    // Helper to show notifications (assuming InventoryApp.showNotification exists from main.js)
    function showAlert(message, type) {
        if (window.InventoryApp && window.InventoryApp.showNotification) {
            window.InventoryApp.showNotification(message, type);
        } else {
            // Fallback if main.js is not loaded or showNotification is not exposed
            const alertDiv = document.createElement('div');
            alertDiv.className = `bg-${type}-100 border border-${type}-400 text-${type}-700 px-4 py-3 rounded relative mb-4`;
            alertDiv.innerHTML = `<span class="block sm:inline">${message}</span>`;
            alertArea.innerHTML = ''; // Clear previous alerts
            alertArea.appendChild(alertDiv);
            setTimeout(() => alertDiv.remove(), 5000); // Remove after 5 seconds
        }
    }

    function resetForm() {
        masterDataForm.reset();
        formActionInput.value = 'add';
        itemIdInput.value = '';
        formTitleElement.innerHTML = `<i class="fas fa-plus-circle text-blue-500 mr-2"></i>Add New ${capitalizeFirstLetter(masterDataType)}`;
        buttonTextElement.textContent = `Add ${capitalizeFirstLetter(masterDataType)}`;
        formSubmitButton.querySelector('i').className = 'fas fa-plus mr-2';
        
        // Remove dynamically added cancel edit button
        const currentCancelButton = document.getElementById('cancel-edit-button');
        if (currentCancelButton) {
            currentCancelButton.remove();
        }

        // Remove edit_id from URL
        const url = new URL(window.location.href);
        url.searchParams.delete('edit_id');
        window.history.replaceState({}, document.title, url.toString());
    }

    // Function to populate form for editing
    function editItem(type, item) {
        console.log('editItem called with type:', type, 'and item:', item);
        console.log('Current masterDataType:', masterDataType);
        
        if (type !== masterDataType) {
            console.error(`editItem called for wrong type. Expected ${masterDataType}, got ${type}`);
            return;
        }

        console.log('Setting form action to edit');
        formActionInput.value = 'edit';
        console.log('Setting item ID to:', item.id);
        itemIdInput.value = item.id;
        formTitleElement.innerHTML = `<i class="fas fa-save text-blue-500 mr-2"></i>Edit ${capitalizeFirstLetter(masterDataType)}`;
        buttonTextElement.textContent = `Update ${capitalizeFirstLetter(masterDataType)}`;
        formSubmitButton.querySelector('i').className = 'fas fa-save mr-2';

        // Populate specific fields based on type
        console.log('Populating fields for type:', type);
        if (type === 'category') {
            const nameField = document.getElementById('category-name');
            console.log('Category name field:', nameField, 'Setting to:', item.name);
            if (nameField) nameField.value = item.name;
            
            const descField = document.getElementById('category-description');
            if (descField) descField.value = item.description || '';
        } else if (type === 'type') {
            const nameField = document.getElementById('type-name');
            if (nameField) nameField.value = item.name;
            
            const categoryField = document.getElementById('type-category_id');
            if (categoryField) categoryField.value = item.category_id || '';
            
            const descField = document.getElementById('type-description');
            if (descField) descField.value = item.description || '';
        } else if (type === 'size') {
            const sizeField = document.getElementById('size-name');
            console.log('Size field:', sizeField, 'Setting to:', item.size);
            if (sizeField) {
                sizeField.value = item.size || item.name || ''; // Try both 'size' and 'name' properties
                console.log('Size field value after setting:', sizeField.value);
            } else {
                console.error('Size field not found!');
            }
        } else if (type === 'department') {
            const nameField = document.getElementById('department-name');
            console.log('Department name field:', nameField, 'Setting to:', item.name);
            if (nameField) nameField.value = item.name;
            
            const descField = document.getElementById('department-description');
            if (descField) descField.value = item.description || '';
        } else if (type === 'location') {
            const nameField = document.getElementById('location-name');
            console.log('Location name field:', nameField, 'Setting to:', item.name);
            if (nameField) nameField.value = item.name;
            
            const descField = document.getElementById('location-description');
            if (descField) descField.value = item.description || '';
        } else if (type === 'seller') {
            const nameField = document.getElementById('seller-name');
            console.log('Seller name field:', nameField, 'Setting to:', item.name);
            if (nameField) nameField.value = item.name;
            
            const contactField = document.getElementById('seller-contact_person');
            if (contactField) contactField.value = item.contact_person || '';
            
            const phoneField = document.getElementById('seller-phone');
            if (phoneField) phoneField.value = item.phone || '';
            
            const emailField = document.getElementById('seller-email');
            if (emailField) emailField.value = item.email || '';
            
            const addressField = document.getElementById('seller-address');
            if (addressField) addressField.value = item.address || '';
        }
        // Add cancel edit button if not already present
        let currentCancelButton = document.getElementById('cancel-edit-button');
        if (!currentCancelButton) {
            currentCancelButton = document.createElement('button');
            currentCancelButton.type = 'button';
            currentCancelButton.id = 'cancel-edit-button';
            currentCancelButton.className = 'w-full mt-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg transition-all shadow-lg';
            currentCancelButton.innerHTML = '<i class="fas fa-times mr-2"></i> Cancel Edit';
            currentCancelButton.addEventListener('click', resetForm);
            masterDataForm.appendChild(currentCancelButton);
        }

        // Scroll to form
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Handle form submission
    masterDataForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        // Ensure action is correctly set, even if it was changed by editItem
        formData.set('action', formActionInput.value);
        // Ensure CSRF token is included
        if (!formData.has('csrf')) {
            formData.set('csrf', getCSRFToken());
        }

        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Identify as AJAX request
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showAlert(result.message, 'green');
                if (formActionInput.value === 'add') {
                    // Add new item to list
                    if (noItemsMessage) {
                        noItemsMessage.remove();
                        // Re-add the list container if it was removed
                        if (!itemListContainer.querySelector('.bg-gray-50')) {
                            itemListContainer.innerHTML = ''; // Clear the "No items yet" message
                        }
                    }
                    const newItemHtml = generateItemHtml(masterDataType, result.new_item);
                    itemListContainer.insertAdjacentHTML('afterbegin', newItemHtml);
                    itemCountSpan.textContent = parseInt(itemCountSpan.textContent) + 1;
                } else if (formActionInput.value === 'edit') {
                    // Update existing item in list
                    const updatedItem = result.updated_item;
                    const itemElement = document.getElementById(`${masterDataType}-item-${updatedItem.id}`);
                    if (itemElement) {
                        itemElement.outerHTML = generateItemHtml(masterDataType, updatedItem);
                    }
                }
                resetForm();
            } else {
                showAlert(result.message, 'red');
            }
        } catch (error) {
            console.error('AJAX submission failed:', error);
            showAlert('An error occurred during submission. Please try again.', 'red');
        }
    });

    // Handle delete forms (AJAX) - Event delegation for dynamically added forms
    if (itemListContainer) {
        itemListContainer.addEventListener('submit', async function(e) {
        if (e.target.classList.contains('delete-form')) {
            e.preventDefault();
            if (!confirm(`Are you sure you want to delete this ${masterDataType}?`)) {
                return;
            }

            const formData = new FormData(e.target);
            formData.set('action', 'delete'); // Ensure action is correctly set
            // Ensure CSRF token is included
            formData.set('csrf', getCSRFToken());

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showAlert(result.message, 'green');
                    document.getElementById(`${masterDataType}-item-${result.deleted_id}`).remove();
                    itemCountSpan.textContent = parseInt(itemCountSpan.textContent) - 1;
                    if (parseInt(itemCountSpan.textContent) === 0) {
                        itemListContainer.innerHTML = `<p class="text-gray-500 text-center py-8" id="no-${masterDataType}s-message">No ${masterDataType}s yet. Add one to get started!</p>`;
                    }
                } else {
                    showAlert(result.message, 'red');
                }
            } catch (error) {
                console.error('AJAX deletion failed:', error);
                showAlert('An error occurred during deletion. Please try again.', 'red');
            }
        });
    } else {
        console.error('Item list container not found for delete handler:', masterDataType);
    }

    // Event delegation for edit buttons
    if (itemListContainer) {
        itemListContainer.addEventListener('click', function(e) {
            const editButton = e.target.closest('.edit-button');
            if (editButton) {
                console.log('Edit button clicked');
                console.log('Raw data-item:', editButton.dataset.item);
                try {
                    const itemData = JSON.parse(atob(editButton.dataset.item));
                    console.log('Parsed item data:', itemData);
                    console.log('Master data type:', masterDataType);
                    editItem(masterDataType, itemData);
                } catch (error) {
                    console.error('Error parsing item data:', error);
                    console.error('Data attribute value:', editButton.dataset.item);
                    alert('Error parsing item data. Check console for details.');
                }
            }
        });
    } else {
        console.error('Item list container not found for:', masterDataType);
    }



    // Function to generate HTML for a single item in the list
    function generateItemHtml(type, item) {
        // Encode item data as base64 for data attribute
        const itemJsonBase64 = btoa(JSON.stringify(item));
        let html = '';
        if (type === 'category') {
            html = `
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-blue-300 transition-all" id="category-item-${item.id}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800" id="category-name-${item.id}">${item.name}</h3>
                            ${item.description ? `<p class="text-xs text-gray-500 mt-1" id="category-description-${item.id}">${item.description}</p>` : ''}
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button type="button" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded transition-all edit-button" title="Edit" data-item="${itemJsonBase64}">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <form method="POST" onsubmit="return confirm('Delete this category?')" class="inline delete-form">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="${item.id}">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded transition-all" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
        } else if (type === 'type') {
            html = `
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-pink-300 transition-all" id="type-item-${item.id}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800" id="type-name-${item.id}">${item.name}</h3>
                            ${item.category_name ? `<p class="text-xs text-gray-600" id="type-category-${item.id}">Category: ${item.category_name}</p>` : ''}
                            ${item.description ? `<p class="text-xs text-gray-500 mt-1" id="type-description-${item.id}">${item.description}</p>` : ''}
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button type="button" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded transition-all edit-button" title="Edit" data-item="${itemJsonBase64}">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <form method="POST" onsubmit="return confirm('Delete this type?')" class="inline delete-form">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="${item.id}">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded transition-all" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
        } else if (type === 'size') {
            html = `
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-blue-300 transition-all" id="size-item-${item.id}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800" id="size-name-${item.id}">${item.size}</h3>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button type="button" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded transition-all edit-button" title="Edit" data-item="${itemJsonBase64}">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <form method="POST" onsubmit="return confirm('Delete this size?')" class="inline delete-form">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="${item.id}">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded transition-all" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
        } else if (type === 'department') {
            html = `
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-blue-300 transition-all" id="department-item-${item.id}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800" id="department-name-${item.id}">${item.name}</h3>
                            ${item.description ? `<p class="text-xs text-gray-500 mt-1" id="department-description-${item.id}">${item.description}</p>` : ''}
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button type="button" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded transition-all edit-button" title="Edit" data-item="${itemJsonBase64}">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <form method="POST" onsubmit="return confirm('Delete this department?')" class="inline delete-form">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="${item.id}">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded transition-all" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
        } else if (type === 'location') {
            html = `
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-orange-300 transition-all" id="location-item-${item.id}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800" id="location-name-${item.id}">${escapeHtml(item.name)}</h3>
                            ${item.description ? `<p class="text-xs text-gray-500 mt-1" id="location-description-${item.id}">${escapeHtml(item.description)}</p>` : ''}
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button type="button" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded transition-all edit-button" title="Edit" data-item="${itemJsonBase64}">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <form method="POST" class="inline delete-form">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="${item.id}">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded transition-all" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
        } else if (type === 'seller') {
            html = `
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-blue-300 transition-all" id="seller-item-${item.id}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800" id="seller-name-${item.id}">${item.name}</h3>
                            ${item.contact_person ? `<p class="text-xs text-gray-600" id="seller-contact_person-${item.id}">Contact: ${item.contact_person}</p>` : ''}
                            ${item.phone ? `<p class="text-xs text-gray-600" id="seller-phone-${item.id}">Phone: ${item.phone}</p>` : ''}
                            ${item.email ? `<p class="text-xs text-gray-600" id="seller-email-${item.id}">Email: ${item.email}</p>` : ''}
                            ${item.address ? `<p class="text-xs text-gray-500 mt-1" id="seller-address-${item.id}">Address: ${item.address}</p>` : ''}
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button type="button" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded transition-all edit-button" title="Edit" data-item="${itemJsonBase64}">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <form method="POST" onsubmit="return confirm('Delete this seller?')" class="inline delete-form">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="${item.id}">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded transition-all" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
        }
        return html;
    }

    // Utility to capitalize first letter
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Utility to escape HTML characters for safe display
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Initial form setup if editing_item is present (from initial page load)
    // The PHP view will call window.editItem if $is_edit is true.
});
