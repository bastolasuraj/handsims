<style>

        transition: all 0.3s ease;
    }
    
    .table-hover-row:hover {
        transform: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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
    
    .fade-in-row {
        animation: fadeIn 0.5s ease-out;
    }
</style>

<!-- Bulk Add Modal -->
<div id="bulkAddModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <di<style>
    .table-hover-row {
        transition: all 0.3s ease;
    
 clas="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="<?php echo APP_URL; ?>/stock/bulk_add" method="POST" enctype="multipart/form-data">
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


    <!-- Page Header -->
    <div id="stock-main-content" class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <?php if (isset($filter) && $filter === 'low_stock') : ?>
                    <div class="bg-gradient-to-r from-yellow-500 to-orange-600 p-3 rounded-lg shadow-lg">
                        <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-gray-800">Low Stock Items</h1>
                        <p class="text-sm text-gray-600">Items that need to be restocked</p>
                    </div>
                <?php else: ?>
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-3 rounded-lg shadow-lg">
                        <i class="fas fa-warehouse text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-2xl font-bold text-gray-800">Current Stock Levels</h1>
                        <p class="text-sm text-gray-600">View inventory across all locations</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex items-center space-x-4">
                <button id="bulkAddBtn" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-upload mr-2"></i> Bulk Add
                </button>
                <button onclick="window.print()" 
                        class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-semibold py-2.5 px-5 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-print mr-2"></i> Print Report
                </button>
            </div>
        </div>
    </div>
    
<!-- Summary Statistics -->
<div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <?php
        $totalItems = count($stock);
        $totalQuantity = array_sum(array_column($stock, 'quantity'));
        $lowStockCount = count(
            array_filter(
                $stock, function ($item) {
                    return $item['quantity'] <= $item['low_stock_threshold'];
                }
            )
        );
        $uniqueProducts = count(array_unique(array_column($stock, 'product_id')));
        ?>
        
        <div class="bg-white rounded-lg p-4 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Total Items</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo number_format($totalItems); ?></p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-boxes text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Total Quantity</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo number_format($totalQuantity); ?></p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-cubes text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Unique Products</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo number_format($uniqueProducts); ?></p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-tags text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg p-4 shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Low Stock</p>
                    <p class="text-2xl font-bold text-red-600"><?php echo number_format($lowStockCount); ?></p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg animate-pulse">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>


    <!-- Location Tabs -->
    <div class="bg-white shadow-xl rounded-xl overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50">
            <nav class="flex space-x-2 p-2" aria-label="Tabs">
                <a href="<?php echo APP_URL; ?>/stock/in-stock" 
                   class="<?php echo !$selected_location ? 'bg-white shadow-md text-gray-800' : 'text-gray-600 hover:bg-gray-100'; ?> px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-globe-americas mr-2"></i> All Locations
                </a>
                <?php foreach ($locations as $location) : ?>
                <a href="<?php echo APP_URL; ?>/stock/in-stock?location=<?php echo $location['id']; ?>" 
                   class="<?php echo $selected_location == $location['id'] ? 'bg-white shadow-md text-gray-800' : 'text-gray-600 hover:bg-gray-100'; ?> px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-map-marker-alt mr-1"></i> <?php echo htmlspecialchars($location['name']); ?>
                </a>
                <?php endforeach; ?>
            </nav>
        </div>

    <div id="stock-content-area" class="space-y-6">
        <?php require 'partials/stock_content.php'; ?>
    </div>

<script>
// Modal handling (retained)
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

document.addEventListener('DOMContentLoaded', function() {
    const stockContentArea = document.getElementById('stock-content-area');
    const locationTabs = document.querySelector('.bg-gradient-to-r.from-indigo-50.to-purple-50 nav');
    let currentUrl = new URL(window.location.href);

    function showLoadingIndicator() {
        stockContentArea.innerHTML = `
            <div class="flex justify-center items-center h-64">
                <div class="animate-spin rounded-full h-32 w-32 border-t-2 border-b-2 border-indigo-500"></div>
            </div>
      //  `;
    }

    
        // Debounce timer for search/filter to avoid frequent AJAX calls
    let searchDebounceTimer;
async function fetchStockContent(url) {
        showLoadingIndicator();
        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Identify as AJAX request
                }
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const html = await response.text();
            stockContentArea.innerHTML = html;
            // Re-attach event listeners to new content (e.g., search/filter, sortable table headers)
            attachEventListenersToContent();
            // Update URL in browser history
            window.history.pushState({}, '', url);
            currentUrl = new URL(url); // Update currentUrl for subsequent requests
        } catch (error) {
            console.error('Error fetching stock content:', error);
            stockContentArea.innerHTML = `<div class="text-red-500 text-center py-8">Error loading stock data. Please try again.</div>`;
        }
    }

    function attachEventListenersToContent() {
        const searchInput = stockContentArea.querySelector('#searchInput');
        if (searchInput) {
            // Debounced client-side filter on input
            const debouncedHandler = function(e) {
                e.preventDefault();
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    filterTableClientSide();
                }, 300);
            };
            searchInput.addEventListener('input', debouncedHandler);
            // Prevent Enter from submitting/reloading
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    return false;
                }
            });
        }

        const statusFilter = stockContentArea.querySelector('#statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', function(e){
                e.preventDefault();
                filterTableClientSide();
            });
        }

        // Re-attach event listeners for sortable table headers
        stockContentArea.querySelectorAll('#stockTable th[onclick]').forEach(header => {
            const onclickAttr = header.getAttribute('onclick');
            if (onclickAttr) {
                const columnIndexMatch = onclickAttr.match(/\((\d+)\)/);
                if (columnIndexMatch) {
                    const columnIndex = columnIndexMatch[1];
                    header.removeAttribute('onclick'); // Remove inline handler
                    header.addEventListener('click', () => sortTable(columnIndex));
                }
            }
        });
    }

    function handleTabClick(event) {
        event.preventDefault();
        const targetUrl = event.currentTarget.href;
        fetchStockContent(targetUrl);

        // Update active tab styling
        locationTabs.querySelectorAll('a').forEach(tab => {
            tab.classList.remove('bg-white', 'shadow-md', 'text-gray-800');
            tab.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        event.currentTarget.classList.remove('text-gray-600', 'hover:bg-gray-100');
        event.currentTarget.classList.add('bg-white', 'shadow-md', 'text-gray-800');
    }

    // Client-side filtering without reloads
    function filterTableClientSide() {
        const searchInput = stockContentArea.querySelector('#searchInput');
        const statusFilter = stockContentArea.querySelector('#statusFilter');
        const tableBody = stockContentArea.querySelector('#stockTable tbody');
        if (!tableBody) return;

        const term = (searchInput?.value || '').toLowerCase().trim();
        const statusVal = (statusFilter?.value || '').toLowerCase();

        let visible = 0;
        tableBody.querySelectorAll('tr').forEach(tr => {
            // skip empty-state rows by checking colspan
            const isEmptyRow = tr.querySelector('td[colspan]');
            if (isEmptyRow) {
                tr.style.display = 'none';
                return;
            }
            const tds = tr.querySelectorAll('td');
            const partNumber = (tds[0]?.textContent || '').toLowerCase();
            const partType   = (tds[1]?.textContent || '').toLowerCase();
            const category   = (tds[2]?.textContent || '').toLowerCase();
            const statusText = (tds[tds.length - 2]?.textContent || '').toLowerCase();

            const matchesTerm = !term || partNumber.includes(term) || partType.includes(term) || category.includes(term);

            let matchesStatus = !statusVal;
            if (statusVal) {
                if (statusVal === 'good')      matchesStatus = statusText.includes('in stock');
                if (statusVal === 'low')       matchesStatus = statusText.includes('low stock');
                if (statusVal === 'critical')  matchesStatus = statusText.includes('warning');
                if (statusVal === 'out')       matchesStatus = statusText.includes('out of stock');
            }

            if (matchesTerm && matchesStatus) {
                tr.style.display = '';
                visible++;
            } else {
                tr.style.display = 'none';
            }
        });

        // Manage a dynamic "no results" row
        const existingNoResults = tableBody.querySelector('tr[data-no-results]');
        if (existingNoResults) existingNoResults.remove();
        if (visible === 0) {
            const colCount = stockContentArea.querySelectorAll('#stockTable thead th').length;
            const noRow = document.createElement('tr');
            noRow.setAttribute('data-no-results', '');
            noRow.innerHTML = `
                <td colspan="${colCount}" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="bg-gradient-to-r from-gray-200 to-gray-300 p-6 rounded-full mb-4">
                            <i class="fas fa-search text-4xl text-gray-500"></i>
                        </div>
                        <p class="text-lg font-medium text-gray-600">No matching results found</p>
                        <p class="text-sm text-gray-500 mt-2">Try adjusting your search or filters</p>
                    </div>
                </td>`;
            tableBody.appendChild(noRow);
        }
    }

    function sortTable(columnIndex) {
        // This function will need to be re-implemented to work with AJAX
        // For now, it will trigger a full AJAX reload with sorting parameters
        const searchParams = new URLSearchParams(currentUrl.search);
        searchParams.set('sort_column', columnIndex); // Assuming backend handles this
        // Toggle sort direction if same column clicked again
        const currentSortDir = searchParams.get('sort_direction');
        if (searchParams.get('sort_column') === columnIndex && currentSortDir === 'asc') {
            searchParams.set('sort_direction', 'desc');
        } else {
            searchParams.set('sort_direction', 'asc');
        }

        const newUrl = `${currentUrl.origin}${currentUrl.pathname}?${searchParams.toString()}`;
        fetchStockContent(newUrl);
    }

    // Initial event listeners for tabs
    locationTabs.querySelectorAll('a').forEach(tab => {
        tab.addEventListener('click', handleTabClick);
    });

    // Initial event listeners for search/filter and sortable headers
    attachEventListenersToContent();

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        fetchStockContent(window.location.href);
    });
});
</script>

<style>
@media print {
    /* Hide navigation and action buttons when printing */
    nav, .bg-gradient-to-r, button, a[href*="add"], a[href*="out"] {
        display: none !important;
    }
    
    table {
        font-size: 12px;
    }
    
    .table-hover-row:hover {
        transform: none !important;
        box-shadow: none !important;
    }
}</style>