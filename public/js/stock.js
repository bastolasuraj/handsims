// Stock Management JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initStockManagement();
});

function initStockManagement() {
    // Initialize stock checking for add/out forms
    const productSelect = document.getElementById('product_id');
    const locationSelect = document.getElementById('location_id');
    const sizeSelect = document.getElementById('size_id');
    
    if (productSelect && locationSelect) {
        productSelect.addEventListener('change', checkAvailableStock);
        locationSelect.addEventListener('change', checkAvailableStock);
        if (sizeSelect) {
            sizeSelect.addEventListener('change', checkAvailableStock);
        }
    }
    
    // Initialize barcode scanner support
    initBarcodeScanner();
    
    // Initialize quick stock actions
    initQuickActions();
    
    // Initialize stock level charts if on dashboard
    if (document.getElementById('stockChart')) {
        initStockCharts();
    }
}

// Check available stock via AJAX
function checkAvailableStock() {
    const productId = document.getElementById('product_id').value;
    const locationId = document.getElementById('location_id').value;
    const sizeId = document.getElementById('size_id')?.value || '';
    const stockDisplay = document.getElementById('availableStock');
    
    if (!productId || !locationId) {
        if (stockDisplay) {
            stockDisplay.innerHTML = '<i class="fas fa-info-circle text-gray-400"></i> Select product and location to check stock';
        }
        return;
    }
    
    // Show loading
    if (stockDisplay) {
        stockDisplay.innerHTML = '<i class="fas fa-spinner fa-spin text-blue-500"></i> Checking stock...';
    }
    
    // Make AJAX request
    fetch(`stock/check-stock?product_id=${productId}&location_id=${locationId}&size_id=${sizeId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (stockDisplay) {
            const available = data.available || 0;
            let statusClass = 'text-green-600';
            let statusIcon = 'check-circle';
            
            if (available === 0) {
                statusClass = 'text-red-600';
                statusIcon = 'exclamation-circle';
            } else if (available < 10) {
                statusClass = 'text-yellow-600';
                statusIcon = 'exclamation-triangle';
            }
            
            stockDisplay.innerHTML = `
                <i class="fas fa-${statusIcon} ${statusClass}"></i>
                <span class="${statusClass} font-semibold">
                    Available Stock: ${available} units
                </span>
            `;
            
            // Update max quantity for stock out form
            const quantityInput = document.getElementById('quantity');
            if (quantityInput && document.querySelector('form[action*="stock/out"]')) {
                quantityInput.max = available;
                if (available === 0) {
                    quantityInput.disabled = true;
                    showNotification('No stock available at this location', 'warning');
                } else {
                    quantityInput.disabled = false;
                }
            }
        }
    })
    .catch(error => {
        console.error('Error checking stock:', error);
        if (stockDisplay) {
            stockDisplay.innerHTML = '<i class="fas fa-exclamation-circle text-red-500"></i> Error checking stock';
        }
    });
}

// Barcode scanner support
function initBarcodeScanner() {
    const barcodeInput = document.getElementById('barcode_scan');
    if (!barcodeInput) return;
    
    let barcodeBuffer = '';
    let barcodeTimer;
    
    document.addEventListener('keypress', function(e) {
        // Check if we're not in an input field
        if (document.activeElement.tagName === 'INPUT' || 
            document.activeElement.tagName === 'TEXTAREA') {
            return;
        }
        
        // Clear buffer after 100ms of inactivity
        clearTimeout(barcodeTimer);
        barcodeTimer = setTimeout(() => {
            barcodeBuffer = '';
        }, 100);
        
        // Add character to buffer
        if (e.key !== 'Enter') {
            barcodeBuffer += e.key;
        } else if (barcodeBuffer.length > 0) {
            // Process barcode
            processBarcode(barcodeBuffer);
            barcodeBuffer = '';
        }
    });
}

function processBarcode(barcode) {
    // Search for product by barcode
    fetch(`products/search-by-barcode?term=${barcode}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(products => {
        if (products.length > 0) {
            // Select the first matching product
            const productSelect = document.getElementById('product_id');
            if (productSelect) {
                productSelect.value = products[0].id;
                productSelect.dispatchEvent(new Event('change'));
                showNotification(`Product found: ${products[0].part_number}`, 'success');
            }
        } else {
            showNotification('Product not found for barcode: ' + barcode, 'error');
        }
    })
    .catch(error => {
        console.error('Error processing barcode:', error);
        showNotification('Error processing barcode', 'error');
    });
}

// Quick stock actions
function initQuickActions() {
    // Quick add stock buttons
    const quickAddButtons = document.querySelectorAll('.quick-add-stock');
    quickAddButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const locationId = this.dataset.locationId;
            showQuickAddModal(productId, locationId);
        });
    });
    
    // Quick remove stock buttons
    const quickRemoveButtons = document.querySelectorAll('.quick-remove-stock');
    quickRemoveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const locationId = this.dataset.locationId;
            showQuickRemoveModal(productId, locationId);
        });
    });
}

function showQuickAddModal(productId, locationId) {
    // Create modal HTML
    const modalHtml = `
        <div id="quickAddModal" class="modal show">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="text-lg font-semibold">Quick Add Stock</h3>
                    <button onclick="closeModal('quickAddModal')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="quickAddForm">
                        <input type="hidden" name="product_id" value="${productId}">
                        <input type="hidden" name="location_id" value="${locationId}">
                        <div class="form-group">
                            <label class="form-label">Quantity to Add</label>
                            <input type="number" name="quantity" min="1" required 
                                   class="form-control w-full px-3 py-2 border rounded-md">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Remarks (Optional)</label>
                            <textarea name="remarks" rows="2" 
                                      class="form-control w-full px-3 py-2 border rounded-md"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button onclick="closeModal('quickAddModal')" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button onclick="submitQuickAdd()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Add Stock
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function submitQuickAdd() {
    const form = document.getElementById('quickAddForm');
    const formData = new FormData(form);
    
    // Here you would submit via AJAX
    // For now, redirect to the add stock page with pre-filled values
    const productId = formData.get('product_id');
    const locationId = formData.get('location_id');
    window.location.href = `stock/add?product_id=${productId}&location_id=${locationId}`;
}

// Stock level charts
function initStockCharts() {
    // Get stock data
    fetch('dashboard/get-stats', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Create simple bar chart using CSS
        createStockChart(data);
    })
    .catch(error => {
        console.error('Error loading chart data:', error);
    });
}

function createStockChart(data) {
    const chartContainer = document.getElementById('stockChart');
    if (!chartContainer) return;
    
    // Simple CSS-based chart
    const chartHtml = `
        <div class="flex items-end justify-around h-48 border-b border-l border-gray-300">
            <div class="flex flex-col items-center">
                <div class="bg-blue-500 w-12 transition-all duration-500" 
                     style="height: ${Math.min(data.total_products * 2, 150)}px"></div>
                <span class="text-xs mt-2">Products</span>
                <span class="text-sm font-semibold">${data.total_products}</span>
            </div>
            <div class="flex flex-col items-center">
                <div class="bg-green-500 w-12 transition-all duration-500" 
                     style="height: ${Math.min(data.total_inventory / 10, 150)}px"></div>
                <span class="text-xs mt-2">Total Stock</span>
                <span class="text-sm font-semibold">${data.total_inventory}</span>
            </div>
            <div class="flex flex-col items-center">
                <div class="bg-yellow-500 w-12 transition-all duration-500" 
                     style="height: ${Math.min(data.low_stock_items * 5, 150)}px"></div>
                <span class="text-xs mt-2">Low Stock</span>
                <span class="text-sm font-semibold">${data.low_stock_items}</span>
            </div>
            <div class="flex flex-col items-center">
                <div class="bg-purple-500 w-12 transition-all duration-500" 
                     style="height: ${Math.min(data.todays_transactions * 10, 150)}px"></div>
                <span class="text-xs mt-2">Today's Txn</span>
                <span class="text-sm font-semibold">${data.todays_transactions}</span>
            </div>
        </div>
    `;
    
    chartContainer.innerHTML = chartHtml;
    
    // Animate bars
    setTimeout(() => {
        chartContainer.querySelectorAll('.bg-blue-500, .bg-green-500, .bg-yellow-500, .bg-purple-500')
            .forEach(bar => {
                bar.style.height = bar.style.height;
            });
    }, 100);
}

// Auto-refresh stock levels
function startAutoRefresh(interval = 60000) {
    setInterval(() => {
        if (document.getElementById('stockTable')) {
            location.reload();
        }
    }, interval);
}

// Export functions
window.StockManager = {
    checkAvailableStock,
    processBarcode,
    showQuickAddModal,
    startAutoRefresh
};