<style>
    .log-terminal {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        font-family: 'Courier New', monospace;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.5);
    }
    
    .log-line:hover {
        background: rgba(74, 222, 128, 0.1);
        border-left: 3px solid #4ade80;
        padding-left: 13px;
    }
    
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #1a1a2e;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #4ade80 0%, #22c55e 100%);
        border-radius: 10px;
    }
</style>

<div class="space-y-6">
    <!-- Page Header -->
    <div id="log-main-content" class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-history text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-800">Activity Logs</h1>
                    <p class="text-sm text-gray-600">System activity and audit trail</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="<?php echo APP_URL; ?>/activity-logs/export" 
                   class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-2.5 px-5 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-download mr-2"></i> Export Logs
                </a>
                <?php if ($_SESSION['role'] === 'admin') : ?>
                <button onclick="document.getElementById('clearModal').classList.remove('hidden')"
                        class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold py-2.5 px-5 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-trash mr-2"></i> Clear Old Logs
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])) : ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md" role="alert">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-3 text-green-500"></i>
            <span><?php echo $_SESSION['success'];
            unset($_SESSION['success']); ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Log Categories Tabs -->
    <div class="bg-white shadow-xl rounded-xl overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50">
            <nav class="flex space-x-2 p-2" aria-label="Tabs">
                <?php
                $categories = ['all', 'user', 'product', 'stock', 'report', 'system', 'error'];
                $icons = ['globe-americas', 'user', 'box', 'warehouse', 'chart-bar', 'cogs', 'bug'];
                ?>
                <?php foreach ($categories as $index => $cat) : ?>
                <a href="?category=<?php echo $cat; ?>" 
                   class="<?php echo $category == $cat ? 'bg-white shadow-md text-gray-800' : 'text-gray-600 hover:bg-gray-100'; ?> px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-<?php echo $icons[$index]; ?> mr-2"></i> <?php echo ucfirst($cat); ?>
                </a>
                <?php endforeach; ?>
            </nav>
        </div>
    </div>

    <div id="log-content-area" class="space-y-6">
        <?php include 'partials/log_content.php'; ?>
    </div>
</div>

<!-- Clear Logs Modal -->
<?php if ($_SESSION['role'] === 'admin') : ?>
<div id="clearModal" class="fixed z-50 inset-0 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" onclick="document.getElementById('clearModal').classList.add('hidden')">
            <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
        </div>
        
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="<?php echo APP_URL; ?>/activity-logs/clear" method="POST">
                <div class="bg-gradient-to-r from-red-500 to-rose-600 px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-white bg-opacity-20">
                            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                        <h3 class="ml-4 text-lg font-bold text-white">
                            Clear Old Logs
                        </h3>
                    </div>
                </div>
                <div class="bg-white px-6 py-6">
                    <p class="text-sm text-gray-600 mb-4">
                        This will permanently delete old log entries. This action cannot be undone.
                    </p>
                    <div class="group">
                        <label for="days_to_keep" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-blue-500 mr-1"></i>
                            Keep logs from the last (days):
                        </label>
                        <input type="number" id="days_to_keep" name="days_to_keep" value="30" min="7" max="365"
                               class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 text-sm font-medium hover:border-blue-300">
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Logs older than this will be permanently deleted
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('clearModal').classList.add('hidden')"
                            class="bg-gradient-to-r from-gray-200 to-gray-300 hover:from-gray-300 hover:to-gray-400 text-gray-700 font-semibold py-2.5 px-5 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-semibold py-2.5 px-6 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-trash mr-2"></i> Clear Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const logContentArea = document.getElementById('log-content-area');
    const logTabs = document.querySelector('.bg-gradient-to-r.from-indigo-50.to-purple-50 nav');
    let currentUrl = new URL(window.location.href);

    function showLoadingIndicator() {
        logContentArea.innerHTML = `
            <div class="flex justify-center items-center h-64">
                <div class="animate-spin rounded-full h-32 w-32 border-t-2 border-b-2 border-indigo-500"></div>
            </div>
        `;
    }

    async function fetchLogContent(url) {
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
            logContentArea.innerHTML = html;
            // Re-attach event listeners to new content (e.g., pagination links)
            attachEventListenersToContent();
            // Update URL in browser history
            window.history.pushState({}, '', url);
            currentUrl = new URL(url); // Update currentUrl for subsequent requests
        } catch (error) {
            console.error('Error fetching log content:', error);
            logContentArea.innerHTML = `<div class="text-red-500 text-center py-8">Error loading logs. Please try again.</div>`;
        }
    }

    function attachEventListenersToContent() {
        // Re-attach event listeners for pagination links
        logContentArea.querySelectorAll('.pagination a').forEach(link => {
            link.removeEventListener('click', handlePaginationClick); // Prevent duplicate listeners
            link.addEventListener('click', handlePaginationClick);
        });

        // Re-attach event listeners for search form submission
        const searchForm = logContentArea.querySelector('#log-search-filter-area form');
        if (searchForm) {
            searchForm.removeEventListener('submit', handleSearchSubmit);
            searchForm.addEventListener('submit', handleSearchSubmit);
            
            const clearFiltersBtn = logContentArea.querySelector('#log-clear-filters');
            if (clearFiltersBtn) {
                clearFiltersBtn.removeEventListener('click', handleClearFilters);
                clearFiltersBtn.addEventListener('click', handleClearFilters);
            }
        }
    }

    function handleTabClick(event) {
        event.preventDefault();
        const targetUrl = event.currentTarget.href;
        fetchLogContent(targetUrl);

        // Update active tab styling
        logTabs.querySelectorAll('a').forEach(tab => {
            tab.classList.remove('bg-white', 'shadow-md', 'text-gray-800');
            tab.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        event.currentTarget.classList.remove('text-gray-600', 'hover:bg-gray-100');
        event.currentTarget.classList.add('bg-white', 'shadow-md', 'text-gray-800');
    }

    function handleSearchSubmit(event) {
        event.preventDefault();
        const form = event.currentTarget;
        const formData = new FormData(form);
        const searchParams = new URLSearchParams(currentUrl.search);

        // Update search params from form
        formData.forEach((value, key) => {
            searchParams.set(key, value);
        });
        // Ensure category is preserved if not explicitly in form (e.g., error tab)
        if (!searchParams.has('category') && currentUrl.searchParams.has('category')) {
            searchParams.set('category', currentUrl.searchParams.get('category'));
        }
        // Reset page to 1 on new search
        searchParams.set('page', 1);

        const newUrl = `${currentUrl.origin}${currentUrl.pathname}?${searchParams.toString()}`;
        fetchLogContent(newUrl);
    }

    function handleClearFilters(event) {
        event.preventDefault();
        const searchParams = new URLSearchParams(currentUrl.search);
        searchParams.delete('search');
        searchParams.delete('date_from');
        searchParams.delete('date_to');
        searchParams.set('page', 1); // Reset page
        // Keep category if it exists
        const newUrl = `${currentUrl.origin}${currentUrl.pathname}?${searchParams.toString()}`;
        fetchLogContent(newUrl);
    }

    function handlePaginationClick(event) {
        event.preventDefault();
        const targetUrl = event.currentTarget.href;
        fetchLogContent(targetUrl);
    }

    // Initial event listeners for tabs
    logTabs.querySelectorAll('a').forEach(tab => {
        tab.addEventListener('click', handleTabClick);
    });

    // Initial event listeners for search form and pagination (for initial content)
    attachEventListenersToContent();

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        fetchLogContent(window.location.href);
    });

    // Auto-refresh logs every 30 seconds (modified to use fetchLogContent)
    // Removed auto-refresh as it interferes with user interaction and AJAX loading.
    // If auto-refresh is desired, it should be re-implemented carefully to not disrupt user input or current view.
});
</script>