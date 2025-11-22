<style>
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .slide-in-up {
        animation: slideInUp 0.5s ease-out;
    }
    
    .product-row {
        transition: all 0.3s ease;
    }
    
    .product-row:hover {
        transform: translateX(8px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl shadow-2xl p-6 text-white slide-in-up">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <div class="bg-white/20 backdrop-blur-lg p-3 rounded-lg mr-4">
                    <i class="fas fa-box text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Products Catalog</h1>
                    <p class="text-white/80 text-sm mt-1">Manage product catalog and inventory items</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="<?php echo APP_URL; ?>/products/add" 
                   class="bg-white/20 backdrop-blur-lg hover:bg-white/30 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 border border-white/30 flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i> Add Product
                </a>
                <button id="bulkAddBtn" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-upload mr-2"></i> Bulk Add
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Add Modal -->
    <div id="bulkAddModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="<?php echo APP_URL; ?>/products/bulk_add" method="POST" enctype="multipart/form-data">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-white bg-opacity-20">
                                <i class="fas fa-file-csv text-white text-xl"></i>
                            </div>
                            <h3 class="ml-4 text-lg font-bold text-white" id="modal-title">
                                Bulk Product Upload
                            </h3>
                        </div>
                    </div>
                    <div class="bg-white px-6 py-6">
                        <p class="text-sm text-gray-600 mb-4">
                            To bulk add products, create a CSV file with the following columns. The first row must be the header.
                        </p>
                        <div class="bg-gray-100 p-3 rounded-lg mb-4 overflow-x-auto">
                            <code class="text-sm text-gray-800">part_number,type_id,category_name,description,low_stock_threshold,available_sizes</code>
                        </div>
                        <div class="text-xs text-gray-500 space-y-1 mb-4">
                            <p><strong>part_number:</strong> Unique identifier for the product (e.g., SKU, barcode).</p>
                            <p><strong>type_id:</strong> The ID of the product type from the 'Types' master data (e.g., 1 for "Safety Goggles").</p>
                            <p><strong>category_name:</strong> The name of the category. If it doesn't exist, it will be created.</p>
                            <p><strong>description:</strong> A brief description of the product.</p>
                            <p><strong>low_stock_threshold:</strong> A number to trigger low stock alerts (e.g., 5).</p>
                            <p><strong>available_sizes:</strong> Comma-separated list of size IDs (e.g., "1,2,3"). Leave empty if not applicable.</p>
                        </div>
                        <a href="<?php echo APP_URL; ?>/public/samples/products_sample.csv" download class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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

    <!-- Products Table -->
    <div class="bg-white shadow-2xl rounded-xl overflow-hidden slide-in-up" style="animation-delay: 0.2s">
        <!-- Search and Filters -->
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-purple-200">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <input type="text" id="searchInput" placeholder="Search products by name, type, or category..." 
                           class="w-full px-4 py-3 pl-12 rounded-lg border-2 border-purple-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 bg-white">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-400"></i>
                </div>
                <div class="flex gap-2">
                    <button onclick="toggleView('grid')" id="gridViewBtn" 
                            class="px-4 py-3 bg-white border-2 border-purple-200 rounded-lg hover:bg-purple-50 transition-all duration-200">
                        <i class="fas fa-th text-purple-600"></i>
                    </button>
                    <button onclick="toggleView('list')" id="listViewBtn" 
                            class="px-4 py-3 bg-purple-100 border-2 border-purple-300 rounded-lg hover:bg-purple-200 transition-all duration-200">
                        <i class="fas fa-list text-purple-700"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Table View -->
        <div id="tableView" class="overflow-x-auto">
            <table class="min-w-full" id="productsTable">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-100 to-gray-200">
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-barcode mr-2 text-purple-500"></i>
                                Part Number
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-tag mr-2 text-blue-500"></i>
                                Type
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-folder mr-2 text-green-500"></i>
                                Category
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-boxes mr-2 text-orange-500"></i>
                                Total Stock
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-tools mr-2 text-gray-500"></i>
                                Actions
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($products as $index => $product) : ?>
                    <tr class="product-row hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50" style="animation: slideInUp 0.5s ease-out <?php echo $index * 0.05; ?>s both">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-gradient-to-r from-purple-400 to-pink-400 p-2 rounded-lg mr-3">
                                    <i class="fas fa-cube text-white text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900">
                                        <?php echo htmlspecialchars($product['part_number']); ?>
                                    </div>
                                    <?php if ($product['barcode']) : ?>
                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-barcode mr-1"></i><?php echo htmlspecialchars($product['barcode']); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800">
                                <?php echo htmlspecialchars($product['type_name'] ?? 'N/A'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gradient-to-r from-green-100 to-emerald-100 text-green-800">
                                <i class="fas fa-tag mr-1 text-xs"></i>
                                <?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <?php
                                $stmt = $this->db->prepare("SELECT SUM(quantity) as total FROM inventory WHERE product_id = ?");
                                $stmt->execute([$product['id']]);
                                $stock = $stmt->fetch();
                                $totalStock = $stock['total'] ?? 0;
                                $stockClass = $totalStock > 50 ? 'text-green-600' : ($totalStock > 10 ? 'text-yellow-600' : 'text-red-600');
                                ?>
                                <span class="text-lg font-bold <?php echo $stockClass; ?>"><?php echo $totalStock; ?></span>
                                <span class="ml-2 text-xs text-gray-500">units</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="<?php echo APP_URL; ?>/products/detail?id=<?php echo $product['id']; ?>" 
                                   class="bg-gradient-to-r from-blue-400 to-indigo-500 text-white p-2 rounded-lg hover:shadow-lg transform hover:scale-110 transition-all duration-200" 
                                   title="View Details">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="<?php echo APP_URL; ?>/products/edit?id=<?php echo $product['id']; ?>" 
                                   class="bg-gradient-to-r from-green-400 to-emerald-500 text-white p-2 rounded-lg hover:shadow-lg transform hover:scale-110 transition-all duration-200" 
                                   title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <?php if (isset($_SESSION['auth_type']) && $_SESSION['auth_type'] === 'local') : ?>
                                <a href="<?php echo APP_URL; ?>/products/delete?id=<?php echo $product['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this product?');"
                                   class="bg-gradient-to-r from-red-400 to-rose-500 text-white p-2 rounded-lg hover:shadow-lg transform hover:scale-110 transition-all duration-200" 
                                   title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($products)) : ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gradient-to-r from-gray-200 to-gray-300 p-6 rounded-full mb-4">
                                    <i class="fas fa-box-open text-4xl text-gray-500"></i>
                                </div>
                                <p class="text-lg font-medium text-gray-600">No products found</p>
                                <p class="text-sm text-gray-500 mt-2">Start by adding your first product</p>
                                <a href="<?php echo APP_URL; ?>/products/add" 
                                   class="mt-4 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-bold py-2 px-6 rounded-lg hover:shadow-lg transition-all duration-200">
                                    <i class="fas fa-plus mr-2"></i> Add Product
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Grid View (Hidden by default) -->
        <div id="gridView" class="hidden p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($products as $product) : ?>
                <div class="bg-gradient-to-br from-white to-purple-50 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-4">
                        <h3 class="text-white font-bold text-lg"><?php echo htmlspecialchars($product['part_number']); ?></h3>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Type:</span>
                            <span class="text-sm font-medium"><?php echo htmlspecialchars($product['type_name'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">Category:</span>
                            <span class="px-2 py-1 text-xs rounded-full bg-gradient-to-r from-green-100 to-emerald-100 text-green-800">
                                <?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?>
                            </span>
                        </div>
                        <?php
                        $stmt = $this->db->prepare("SELECT SUM(quantity) as total FROM inventory WHERE product_id = ?");
                        $stmt->execute([$product['id']]);
                        $stock = $stmt->fetch();
                        $totalStock = $stock['total'] ?? 0;
                        ?>
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Stock:</span>
                                <span class="text-lg font-bold <?php echo $totalStock > 50 ? 'text-green-600' : ($totalStock > 10 ? 'text-yellow-600' : 'text-red-600'); ?>">
                                    <?php echo $totalStock; ?> units
                                </span>
                            </div>
                        </div>
                        <div class="flex space-x-2 pt-3">
                            <a href="<?php echo APP_URL; ?>/products/detail?id=<?php echo $product['id']; ?>" 
                               class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-center py-2 rounded-lg hover:shadow-lg transition-all duration-200">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                            <a href="<?php echo APP_URL; ?>/products/edit?id=<?php echo $product['id']; ?>" 
                               class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-center py-2 rounded-lg hover:shadow-lg transition-all duration-200">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
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

// Enhanced table search
document.getElementById('searchInput').addEventListener('keyup', function() {
    var input = this.value.toLowerCase();
    var currentView = document.getElementById('tableView').classList.contains('hidden') ? 'grid' : 'table';
    
    if (currentView === 'table') {
        var rows = document.querySelectorAll('#productsTable tbody tr');
        rows.forEach(function(row) {
            if (row.cells.length === 1) return; // Skip empty state row
            var text = row.textContent.toLowerCase();
            row.style.display = text.includes(input) ? '' : 'none';
        });
    } else {
        var cards = document.querySelectorAll('#gridView > div > div');
        cards.forEach(function(card) {
            var text = card.textContent.toLowerCase();
            card.style.display = text.includes(input) ? '' : 'none';
        });
    }
});

// Toggle between grid and list view
function toggleView(view) {
    var tableView = document.getElementById('tableView');
    var gridView = document.getElementById('gridView');
    var listBtn = document.getElementById('listViewBtn');
    var gridBtn = document.getElementById('gridViewBtn');
    
    if (view === 'grid') {
        tableView.classList.add('hidden');
        gridView.classList.remove('hidden');
        gridBtn.classList.add('bg-purple-100', 'border-purple-300');
        gridBtn.classList.remove('bg-white', 'border-purple-200');
        listBtn.classList.remove('bg-purple-100', 'border-purple-300');
        listBtn.classList.add('bg-white', 'border-purple-200');
    } else {
        tableView.classList.remove('hidden');
        gridView.classList.add('hidden');
        listBtn.classList.add('bg-purple-100', 'border-purple-300');
        listBtn.classList.remove('bg-white', 'border-purple-200');
        gridBtn.classList.remove('bg-purple-100', 'border-purple-300');
        gridBtn.classList.add('bg-white', 'border-purple-200');
    }
}
</script>