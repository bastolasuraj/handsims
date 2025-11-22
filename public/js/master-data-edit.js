// Master Data Edit Functionality
(function() {
    'use strict';
    
    // Make editItem globally available immediately
    window.editMasterItem = function(type, itemJson) {
        console.log('editMasterItem called with type:', type);
        console.log('Item JSON:', itemJson);
        
        try {
            const item = JSON.parse(atob(itemJson));
            console.log('Parsed item:', item);
            
            // Update form action
            const formAction = document.getElementById('form-action');
            if (formAction) formAction.value = 'edit';
            
            // Update item ID
            const itemId = document.getElementById('item-id');
            if (itemId) itemId.value = item.id;
            
            // Update form title
            const formTitle = document.getElementById('form-title');
            if (formTitle) {
                const typeName = type.charAt(0).toUpperCase() + type.slice(1);
                formTitle.innerHTML = '<i class="fas fa-save text-blue-500 mr-2"></i>Edit ' + typeName;
            }
            
            // Update button text
            const buttonText = document.getElementById('button-text');
            if (buttonText) {
                const typeName = type.charAt(0).toUpperCase() + type.slice(1);
                buttonText.textContent = 'Update ' + typeName;
            }
            
            // Update button icon
            const submitButton = document.getElementById('form-submit-button');
            if (submitButton) {
                const icon = submitButton.querySelector('i');
                if (icon) icon.className = 'fas fa-save mr-2';
            }
            
            // Populate fields based on type
            switch(type) {
                case 'category':
                    setFieldValue('category-name', item.name);
                    // setFieldValue('category-type', item.type || '');
                    setFieldValue('category-description', item.description || '');
                    break;
                    
                case 'size':
                    setFieldValue('size-name', item.size || item.name || '');
                    break;
                    
                case 'department':
                    setFieldValue('department-name', item.name);
                    setFieldValue('department-description', item.description || '');
                    break;
                    
                case 'location':
                    setFieldValue('location-name', item.name);
                    setFieldValue('location-description', item.description || '');
                    break;
                    
                case 'seller':
                    setFieldValue('seller-name', item.name);
                    setFieldValue('seller-contact_person', item.contact_person || '');
                    setFieldValue('seller-phone', item.phone || '');
                    setFieldValue('seller-email', item.email || '');
                    setFieldValue('seller-address', item.address || '');
                    break;
                    
                case 'type':
                    setFieldValue('type-name', item.name);
                    setFieldValue('type-category_id', item.category_id || '');
                    setFieldValue('type-description', item.description || '');
                    break;
            }
            
            // Add cancel button if not present
            addCancelButton();
            
            // Scroll to form
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
        } catch (error) {
            console.error('Error in editMasterItem:', error);
            alert('Error loading item data. Please try again.');
        }
    };
    
    // Helper function to set field value
    function setFieldValue(fieldId, value) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.value = value;
            console.log('Set', fieldId, 'to', value);
        } else {
            console.warn('Field not found:', fieldId);
        }
    }
    
    // Add cancel edit button
    function addCancelButton() {
        let cancelButton = document.getElementById('cancel-edit-button');
        if (!cancelButton) {
            const form = document.querySelector('form[data-master-data-type]');
            if (form) {
                cancelButton = document.createElement('button');
                cancelButton.type = 'button';
                cancelButton.id = 'cancel-edit-button';
                cancelButton.className = 'w-full mt-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg transition-all shadow-lg';
                cancelButton.innerHTML = '<i class="fas fa-times mr-2"></i> Cancel Edit';
                cancelButton.onclick = function() {
                    resetForm();
                };
                form.appendChild(cancelButton);
            }
        }
    }
    
    // Reset form to add mode
    function resetForm() {
        const form = document.querySelector('form[data-master-data-type]');
        if (form) {
            form.reset();
            
            const formAction = document.getElementById('form-action');
            if (formAction) formAction.value = 'add';
            
            const itemId = document.getElementById('item-id');
            if (itemId) itemId.value = '';
            
            const type = form.getAttribute('data-master-data-type');
            const typeName = type.charAt(0).toUpperCase() + type.slice(1);
            
            const formTitle = document.getElementById('form-title');
            if (formTitle) {
                formTitle.innerHTML = '<i class="fas fa-plus-circle text-blue-500 mr-2"></i>Add New ' + typeName;
            }
            
            const buttonText = document.getElementById('button-text');
            if (buttonText) {
                buttonText.textContent = 'Add ' + typeName;
            }
            
            const submitButton = document.getElementById('form-submit-button');
            if (submitButton) {
                const icon = submitButton.querySelector('i');
                if (icon) icon.className = 'fas fa-plus mr-2';
            }
            
            const cancelButton = document.getElementById('cancel-edit-button');
            if (cancelButton) {
                cancelButton.remove();
            }
        }
    }
    
    // Make resetForm globally available
    window.resetMasterForm = resetForm;
    
    console.log('Master data edit script loaded successfully');
})();