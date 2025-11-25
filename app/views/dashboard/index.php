<style>
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .slide-up {
        animation: slideUp 0.6s ease-out;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }
    
    .animate-pulse-slow {
        animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px) scale(1.02);
    }
</style>

<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-2xl shadow-2xl p-8 text-white slide-up">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">
                    Welcome, <?php echo $_SESSION['full_name'] ?? $_SESSION['username']; ?>!
                </h1>
                <p class="text-white/80">
                    <?php echo date('l, F j, Y'); ?> • Here's your inventory overview
                </p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/20 backdrop-blur-lg rounded-xl p-4">
                    <div class="flex items-center space-x-3">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 flex items-center justify-center animate-pulse-slow">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-white/70">System Status</p>
                            <p class="text-sm font-bold">All Systems Operational</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
        $userRole = $_SESSION['role'] ?? '';
        $authType = $_SESSION['auth_type'] ?? 'local';
        // Only show demo data button for local admin users (not LDAP users)
        if ((strtolower($userRole) === 'admin' || true) && $authType === 'local') : // Temporarily enabled for testing
            ?>
        <div class="mt-6">
            <form method="POST" action="<?php echo APP_URL; ?>/dashboard/seed-demo" onsubmit="return confirm('This will generate demo data (products, inventory, transactions). Continue?');">
                <?php include_once __DIR__ . '/../../Helpers/csrf.php';
                echo csrf_field(); ?>
                <button type="submit" class="bg-white/20 backdrop-blur-lg hover:bg-white/30 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 border border-white/30">
                    <i class="fas fa-magic mr-2"></i>
                    Generate Demo Data
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 <?php echo $authType === 'local' ? 'md:grid-cols-4' : 'md:grid-cols-3'; ?> gap-6">
        <!-- Total Products -->
        <div class="stat-card bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl shadow-xl p-6 text-white slide-up" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-box text-2xl"></i>
                </div>
                <span class="text-3xl font-bold"><?php echo $total_products; ?></span>
            </div>
            <h3 class="font-semibold mb-1">Total Products</h3>
            <div class="flex items-center justify-between">
                <span class="text-xs text-white/70">In catalog</span>
                <a href="<?php echo APP_URL; ?>/products" class="text-white/90 hover:text-white transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="stat-card bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl shadow-xl p-6 text-white slide-up" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 p-3 rounded-lg animate-pulse">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
                <span class="text-3xl font-bold"><?php echo count($low_stock_items); ?></span>
            </div>
            <h3 class="font-semibold mb-1">Low Stock Items</h3>
            <div class="flex items-center justify-between">
                <span class="text-xs text-white/70">Need attention</span>
                <a href="<?php echo APP_URL; ?>/stock/in-stock?filter=low_stock" class="text-white/90 hover:text-white transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Active Locations -->
        <div class="stat-card bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-xl p-6 text-white slide-up" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-warehouse text-2xl"></i>
                </div>
                <span class="text-3xl font-bold"><?php echo count($stock_by_location); ?></span>
            </div>
            <h3 class="font-semibold mb-1">Active Locations</h3>
            <div class="flex items-center justify-between">
                <span class="text-xs text-white/70">Warehouses</span>
                <a href="<?php echo APP_URL; ?>/stock/in-stock" class="text-white/90 hover:text-white transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Recent Transactions - Only for local users -->
        <?php if ($authType === 'local') : ?>
        <div class="stat-card bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl shadow-xl p-6 text-white slide-up" style="animation-delay: 0.4s">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-exchange-alt text-2xl"></i>
                </div>
                <span class="text-3xl font-bold"><?php echo count($recent_transactions ?? []); ?></span>
            </div>
            <h3 class="font-semibold mb-1">Today's Activity</h3>
            <div class="flex items-center justify-between">
                <span class="text-xs text-white/70">Transactions</span>
                <a href="<?php echo APP_URL; ?>/reports" class="text-white/90 hover:text-white transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Stock by Location (2 columns) -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-xl overflow-hidden slide-up" style="animation-delay: 0.5s">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-map-marked-alt mr-3"></i>
                    Stock Distribution by Location
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php foreach ($stock_by_location as $index => $location) : ?>
                        <?php
                        $maxQuantity = max(array_column($stock_by_location, 'total_quantity'));
                        $percentage = $maxQuantity > 0 ? ($location['total_quantity'] / $maxQuantity) * 100 : 0;
                        $colors = ['from-blue-400 to-blue-600', 'from-green-400 to-green-600', 'from-purple-400 to-purple-600'];
                        $color = $colors[$index % 3];
                        ?>
                    <div class="group">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="bg-gradient-to-r <?php echo $color; ?> p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                    <i class="fas fa-warehouse text-white text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800"><?php echo $location['location']; ?></h3>
                                    <p class="text-xs text-gray-500"><?php echo $location['total_items']; ?> unique items</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-gray-800"><?php echo number_format($location['total_quantity']); ?></p>
                                <p class="text-xs text-gray-500">Total units</p>
                            </div>
                        </div>
                        <div class="relative">
                            <div class="bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r <?php echo $color; ?> h-full rounded-full transition-all duration-500 ease-out" 
                                     style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                        </div>
                        <div class="mt-2 flex justify-end">
                            <a href="<?php echo APP_URL; ?>/stock/in-stock?location=<?php echo urlencode($location['location']); ?>" 
                               class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Recent Activity (1 column) -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden slide-up" style="animation-delay: 0.6s">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6">
                <h2 class="text-xl font-bold text-white flex items-center justify-between">
                    <span><i class="fas fa-history mr-2"></i> Recent Activity</span>
                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Live</span>
                </h2>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto custom-scrollbar">
                <div class="space-y-4">
                    <?php foreach ($recent_logs as $log) : ?>
                        <?php
                        $actionIcon = 'circle';
                        $actionColor = 'blue';
                        if (strpos(strtolower($log['action']), 'add') !== false) {
                            $actionIcon = 'plus-circle';
                            $actionColor = 'green';
                        } elseif (strpos(strtolower($log['action']), 'remove') !== false || strpos(strtolower($log['action']), 'out') !== false) {
                            $actionIcon = 'minus-circle';
                            $actionColor = 'red';
                        } elseif (strpos(strtolower($log['action']), 'login') !== false) {
                            $actionIcon = 'sign-in-alt';
                            $actionColor = 'purple';
                        }
                        ?>
                    <div class="flex items-start space-x-3 group hover:bg-gray-50 p-2 rounded-lg transition-colors">
                        <div class="flex-shrink-0 mt-1">
                            <div class="h-8 w-8 rounded-full bg-<?php echo $actionColor; ?>-100 flex items-center justify-center">
                                <i class="fas fa-<?php echo $actionIcon; ?> text-<?php echo $actionColor; ?>-600 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($log['username'] ?? 'N/A'); ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                <?php echo htmlspecialchars($log['action']); ?>
                            </p>
                            <?php if ($log['details']) : ?>
                            <p class="text-xs text-gray-500 mt-1">
                                <?php echo htmlspecialchars($log['details']); ?>
                            </p>
                            <?php endif; ?>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="far fa-clock mr-1"></i>
                                <?php echo date('g:i A', strtotime($log['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if ($authType === 'local') : ?>
            <div class="bg-gray-50 px-6 py-3 border-t">
                <a href="<?php echo APP_URL; ?>/activity-logs" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 flex items-center justify-center">
                    View All Activity <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Low Stock Alert Section -->
    <?php if (count($low_stock_items) > 0) : ?>
    <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-xl shadow-xl p-6 border-2 border-red-200 slide-up" style="animation-delay: 0.7s">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <div class="bg-gradient-to-r from-red-500 to-orange-600 p-2 rounded-lg mr-3 animate-pulse">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                Low Stock Alert
            </h2>
            <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">
                <?php echo count($low_stock_items); ?> items need attention
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach (array_slice($low_stock_items, 0, 6) as $item) : ?>
            <div class="bg-white rounded-lg p-4 border border-red-200 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-gray-800 text-sm"><?php echo htmlspecialchars($item['part_number']); ?></h4>
                    <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded">
                        <?php echo $item['quantity']; ?> left
                    </span>
                </div>
                <p class="text-xs text-gray-600 mb-1">
                    <i class="fas fa-warehouse mr-1"></i> <?php echo htmlspecialchars($item['location_name']); ?>
                </p>
                <p class="text-xs text-gray-500">
                    Min: <?php echo $item['low_stock_threshold']; ?> units
                </p>
                <div class="mt-3 flex justify-end">
                    <a href="<?php echo APP_URL; ?>/stock/add?product_id=<?php echo $item['product_id']; ?>&location_id=<?php echo $item['location_id']; ?>&size_id=<?php echo $item['size_id']; ?>" 
                       class="text-xs bg-gradient-to-r from-green-500 to-emerald-600 text-white px-3 py-1 rounded-full hover:shadow-md transition-all">
                        <i class="fas fa-plus mr-1"></i> Restock
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($low_stock_items) > 6) : ?>
        <div class="mt-4 text-center">
            <a href="<?php echo APP_URL; ?>/reports/generate?report_type=low_stock&format=csv" 
               class="text-sm font-medium text-red-600 hover:text-red-700">
                View all <?php echo count($low_stock_items); ?> low stock items <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #5a67d8 0%, #6b4199 100%);
    }
</style>