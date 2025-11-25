<?php
// Include helper to clear messages if requested
require_once __DIR__ . '/../../Helpers/clear_messages.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/style.css">
    
    <style>
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animated-gradient {
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        .nav-item {
            position: relative;
            overflow: hidden;
        }
        
        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .nav-item:hover::before {
            left: 100%;
        }
        
        .dropdown-enter {
            animation: dropdownEnter 0.3s ease-out;
        }
        
        @keyframes dropdownEnter {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="bg-gray-50 overflow-x-hidden"
      <?php if (isset($_SESSION['success'])) :
            ?>data-success-message="<?php echo htmlspecialchars($_SESSION['success']); ?>"<?php unset($_SESSION['success']);
      endif; ?>
      <?php if (isset($_SESSION['error'])) :
            ?>data-error-message="<?php echo htmlspecialchars($_SESSION['error']); ?>"<?php unset($_SESSION['error']);
      endif; ?>
      <?php if (isset($_SESSION['warning'])) :
            ?>data-warning-message="<?php echo htmlspecialchars($_SESSION['warning']); ?>"<?php unset($_SESSION['warning']);
      endif; ?>
      <?php if (isset($_SESSION['info'])) :
            ?>data-info-message="<?php echo htmlspecialchars($_SESSION['info']); ?>"<?php unset($_SESSION['info']);
      endif; ?>>
    <!-- Navigation -->
    <nav class="animated-gradient shadow-2xl relative">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="flex justify-between items-center py-4">
                <!-- Logo and Brand -->
                <div class="flex items-center space-x-4">
                    <a href="<?php echo APP_URL; ?>" class="flex items-center space-x-3 group">
                        <div class="bg-white/20 backdrop-blur-lg p-2 rounded-lg group-hover:bg-white/30 transition-all duration-300 transform group-hover:scale-110">
                            <i class="fas fa-warehouse text-white text-xl"></i>
                        </div>
                        <span class="text-xl font-bold text-white">H&S Inventory</span>
                    </a>
                </div>
                
                <?php if (isset($_SESSION['user_id'])) : ?>
                <div class="flex items-center space-x-6">
                    <!-- Main Navigation -->
                    <div class="hidden md:flex items-center space-x-2">
                        <!-- Dashboard -->
                        <a href="<?php echo APP_URL; ?>/dashboard" 
                           class="nav-item flex items-center px-4 py-2 rounded-lg text-white/90 hover:text-white hover:bg-white/20 transition-all duration-300 <?php echo strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'bg-white/20' : ''; ?>">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                        
                        <!-- Products -->
                        <a href="<?php echo APP_URL; ?>/products" 
                           class="nav-item flex items-center px-4 py-2 rounded-lg text-white/90 hover:text-white hover:bg-white/20 transition-all duration-300 <?php echo strpos($_SERVER['REQUEST_URI'], '/products') !== false ? 'bg-white/20' : ''; ?>">
                            <i class="fas fa-box mr-2"></i>
                            <span class="font-medium">Products</span>
                        </a>

                        <!-- Stock Dropdown -->
                        <div class="relative group">
                            <button class="nav-item flex items-center px-4 py-2 rounded-lg text-white/90 hover:text-white hover:bg-white/20 transition-all duration-300">
                                <i class="fas fa-boxes mr-2"></i>
                                <span class="font-medium">Stock</span>
                                <i class="fas fa-chevron-down ml-2 text-xs group-hover:rotate-180 transition-transform duration-300"></i>
                            </button>
                            <div class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 dropdown-enter overflow-hidden">
                                <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-3">
                                    <p class="text-white text-xs font-semibold uppercase tracking-wider">Stock Management</p>
                                </div>
                                <div class="p-2">
                                    <a href="<?php echo APP_URL; ?>/stock/add" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 rounded-lg transition-all duration-200 group">
                                        <div class="bg-gradient-to-r from-green-400 to-emerald-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-plus-circle text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Add Stock</p>
                                            <p class="text-xs text-gray-500">Add inventory items</p>
                                        </div>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/stock/out" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-red-50 hover:to-rose-50 rounded-lg transition-all duration-200 group">
                                        <div class="bg-gradient-to-r from-red-400 to-rose-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-minus-circle text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Stock Out</p>
                                            <p class="text-xs text-gray-500">Remove from inventory</p>
                                        </div>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/stock/transfer" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-cyan-50 hover:to-blue-50 rounded-lg transition-all duration-200 group">
                                        <div class="bg-gradient-to-r from-cyan-400 to-blue-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-exchange-alt text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Transfer Stock</p>
                                            <p class="text-xs text-gray-500">Move between locations</p>
                                        </div>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/stock/in-stock" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 rounded-lg transition-all duration-200 group">
                                        <div class="bg-gradient-to-r from-blue-400 to-indigo-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-warehouse text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">In Stock</p>
                                            <p class="text-xs text-gray-500">View current stock</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Master Data Dropdown -->
                        <div class="relative group">
                            <button class="nav-item flex items-center px-4 py-2 rounded-lg text-white/90 hover:text-white hover:bg-white/20 transition-all duration-300">
                                <i class="fas fa-database mr-2"></i>
                                <span class="font-medium">Master Data</span>
                                <i class="fas fa-chevron-down ml-2 text-xs group-hover:rotate-180 transition-transform duration-300"></i>
                            </button>
                            <div class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 dropdown-enter overflow-hidden">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 p-3">
                                    <p class="text-white text-xs font-semibold uppercase tracking-wider">Configuration</p>
                                </div>
                                <div class="p-2">
                                    <a href="<?php echo APP_URL; ?>/master-data" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 rounded-lg transition-all duration-200 group border-b border-gray-200 mb-2">
                                        <div class="bg-gradient-to-r from-indigo-400 to-purple-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-cog text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Master Data Home</p>
                                            <p class="text-xs text-gray-500">Overview dashboard</p>
                                        </div>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/master-data/categories" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 rounded-lg transition-all duration-200 group">
                                        <div class="bg-gradient-to-r from-blue-400 to-indigo-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-folder text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Categories</p>
                                            <p class="text-xs text-gray-500">Product categories</p>
                                        </div>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/master-data/sizes" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 rounded-lg transition-all duration-200 group">
                                        <div class="bg-gradient-to-r from-green-400 to-emerald-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-ruler text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Sizes</p>
                                            <p class="text-xs text-gray-500">Product sizes</p>
                                        </div>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/master-data/locations" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 rounded-lg transition-all duration-200 group">
                                        <div class="bg-gradient-to-r from-orange-400 to-red-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-warehouse text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Locations</p>
                                            <p class="text-xs text-gray-500">Warehouse locations</p>
                                        </div>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/master-data/departments" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 rounded-lg transition-all duration-200 group">
                                        <div class="bg-gradient-to-r from-purple-400 to-pink-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-building text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Departments</p>
                                            <p class="text-xs text-gray-500">Departments</p>
                                        </div>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/master-data/sellers" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-teal-50 hover:to-cyan-50 rounded-lg transition-all duration-200 group">
                                        <div class="bg-gradient-to-r from-teal-400 to-cyan-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-store text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Sellers</p>
                                            <p class="text-xs text-gray-500">Sellers/Suppliers</p>
                                        </div>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/master-data/types" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-pink-50 hover:to-rose-50 rounded-lg transition-all duration-200 group">
                                        <div class="bg-gradient-to-r from-pink-400 to-rose-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-tags text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Types</p>
                                            <p class="text-xs text-gray-500">Product types</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reports -->
                        <a href="<?php echo APP_URL; ?>/reports" 
                           class="nav-item flex items-center px-4 py-2 rounded-lg text-white/90 hover:text-white hover:bg-white/20 transition-all duration-300 <?php echo strpos($_SERVER['REQUEST_URI'], '/reports') !== false ? 'bg-white/20' : ''; ?>">
                            <i class="fas fa-chart-bar mr-2"></i>
                            <span class="font-medium">Reports</span>
                        </a>
                        
                        <!-- Logs - Only visible to local accounts -->
                        <?php if (isset($_SESSION['auth_type']) && $_SESSION['auth_type'] === 'local') : ?>
                        <a href="<?php echo APP_URL; ?>/activity-logs"
                        class="nav-item flex items-center px-4 py-2 rounded-lg text-white/90 hover:text-white hover:bg-white/20 transition-all duration-300 <?php echo strpos($_SERVER['REQUEST_URI'], '/activity-logs') !== false ? 'bg-white/20' : ''; ?>">                            <i class="fas fa-history mr-2"></i>
                            <span class="font-medium">Logs</span>
                        </a>
                        
                        <!-- Settings Dropdown - Only visible to local accounts -->
                        <div class="relative group">
                            <button class="nav-item flex items-center px-4 py-2 rounded-lg text-white/90 hover:text-white hover:bg-white/20 transition-all duration-300">
                                <i class="fas fa-cog mr-2"></i>
                                <span class="font-medium">Settings</span>
                                <i class="fas fa-chevron-down ml-2 text-xs group-hover:rotate-180 transition-transform duration-300"></i>
                            </button>
                            <div class="absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 dropdown-enter overflow-hidden">
                                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 p-3">
                                    <p class="text-white text-xs font-semibold uppercase tracking-wider">System Settings</p>
                                </div>
                                <div class="p-2">
                                    <a href="<?php echo APP_URL; ?>/settings/toast-templates" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-yellow-50 hover:to-orange-50 rounded-lg transition-all duration-200 group">
                                        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-comment-dots text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Toast Templates</p>
                                            <p class="text-xs text-gray-500">Customize notifications</p>
                                        </div>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/settings/database" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 rounded-lg transition-all duration-200 group">
                                        <div class="bg-gradient-to-r from-purple-400 to-pink-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-database text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Database Viewer</p>
                                            <p class="text-xs text-gray-500">View database contents</p>
                                        </div>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/database-schema"
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-teal-50 hover:to-cyan-50 rounded-lg transition-all duration-200 group">
                                        <div class="bg-gradient-to-r from-teal-400 to-cyan-500 p-2 rounded-lg mr-3 group-hover:shadow-lg transition-shadow">
                                            <i class="fas fa-sitemap text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">Database Schema</p>
                                            <p class="text-xs text-gray-500">View database schema</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="hidden md:flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button id="notificationBell" class="relative p-2 text-white/80 hover:text-white transition-colors focus:outline-none">
                                <i class="fas fa-bell text-lg"></i>
                                <span id="notificationCount" class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 rounded-full text-xs text-white flex items-center justify-center font-bold hidden shadow-lg"></span>
                            </button>
                            <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl transition-all duration-300 z-50 hidden opacity-0 invisible overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4 rounded-t-xl">
                                    <div class="flex justify-between items-center">
                                        <h3 class="font-semibold text-white flex items-center">
                                            <i class="fas fa-bell mr-2"></i>
                                            Notifications
                                        </h3>
                                        <button id="markAllAsReadBtn" class="text-xs text-white/90 hover:text-white bg-white/20 hover:bg-white/30 px-3 py-1 rounded-full transition-colors">
                                            <i class="fas fa-check-double mr-1"></i>
                                            Mark all read
                                        </button>
                                    </div>
                                </div>
                                <div id="notificationList" class="max-h-96 overflow-y-auto overflow-x-hidden custom-scrollbar">
                                    <!-- Notifications will be dynamically inserted here -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-3 px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition-all duration-300">
                                <div class="h-8 w-8 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold text-sm">
                                    <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                                </div>
                                <div class="text-left">
                                    <p class="text-white text-sm font-medium"><?php echo $_SESSION['username'] ?? 'Guest'; ?></p>
                                    <p class="text-white/70 text-xs"><?php echo $_SESSION['role'] ?? 'User'; ?></p>
                                </div>
                                <i class="fas fa-chevron-down text-white/70 text-xs group-hover:rotate-180 transition-transform duration-300"></i>
                            </button>
                            
                            <div class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 dropdown-enter overflow-hidden">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 p-4">
                                    <p class="text-white font-medium"><?php echo $_SESSION['full_name'] ?? $_SESSION['username']; ?></p>
                                    <p class="text-white/80 text-xs mt-1"><?php echo $_SESSION['email'] ?? 'user@company.com'; ?></p>
                                </div>
                                <div class="p-2">
                                    <a href="#" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                        <i class="fas fa-user-circle mr-3 text-gray-400"></i>
                                        <span>My Profile</span>
                                    </a>
                                    <a href="#" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                        <i class="fas fa-cog mr-3 text-gray-400"></i>
                                        <span>Settings</span>
                                    </a>
                                    <hr class="my-2">
                                    <a href="<?php echo APP_URL; ?>/logout" 
                                       class="flex items-center px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <i class="fas fa-sign-out-alt mr-3"></i>
                                        <span>Logout</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile Menu Button -->
                <button class="md:hidden text-white p-2" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Mobile Slide-in Sidebar Menu -->
        <div id="mobileMenu" class="fixed inset-0 z-50 hidden md:hidden">
            <!-- Backdrop -->
            <div id="mobileMenuBackdrop" class="absolute inset-0 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300"></div>
            <!-- Sidebar Panel -->
            <div id="mobileMenuPanel" class="absolute right-0 top-0 h-full w-80 max-w-[85vw] bg-gradient-to-b from-purple-500 to-pink-500 text-white shadow-2xl transform translate-x-full transition-transform duration-300">
                <div class="p-4 border-b border-white/20 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-2 rounded-lg"><i class="fas fa-warehouse"></i></div>
                        <span class="font-semibold">H&S Inventory</span>
                    </div>
                    <button class="p-2" onclick="toggleMobileMenu()"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-3 space-y-2 overflow-y-auto h-[calc(100%-64px)]">
                    <a href="<?php echo APP_URL; ?>/dashboard" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>

                    <a href="<?php echo APP_URL; ?>/products" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                        <i class="fas fa-box mr-2"></i> Products
                    </a>

                    <!-- Stock Group -->
                    <div class="mt-2">
                        <div class="px-4 py-2 text-xs uppercase tracking-wider text-white/80">Stock</div>
                        <a href="<?php echo APP_URL; ?>/stock/add" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-plus-circle mr-2"></i> Add Stock
                        </a>
                        <a href="<?php echo APP_URL; ?>/stock/out" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-minus-circle mr-2"></i> Stock Out
                        </a>
                        <a href="<?php echo APP_URL; ?>/stock/transfer" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-exchange-alt mr-2"></i> Transfer Stock
                        </a>
                        <a href="<?php echo APP_URL; ?>/stock/in-stock" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-warehouse mr-2"></i> In Stock
                        </a>
                    </div>

                    <!-- Master Data Group -->
                    <div class="mt-2">
                        <div class="px-4 py-2 text-xs uppercase tracking-wider text-white/80">Master Data</div>
                        <a href="<?php echo APP_URL; ?>/master-data" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-cog mr-2"></i> Master Data Home
                        </a>
                        <a href="<?php echo APP_URL; ?>/master-data/categories" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-folder mr-2"></i> Categories
                        </a>
                        <a href="<?php echo APP_URL; ?>/master-data/sizes" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-ruler mr-2"></i> Sizes
                        </a>
                        <a href="<?php echo APP_URL; ?>/master-data/locations" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-warehouse mr-2"></i> Locations
                        </a>
                        <a href="<?php echo APP_URL; ?>/master-data/departments" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-building mr-2"></i> Departments
                        </a>
                        <a href="<?php echo APP_URL; ?>/master-data/sellers" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-store mr-2"></i> Sellers
                        </a>
                        <a href="<?php echo APP_URL; ?>/master-data/types" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-tags mr-2"></i> Types
                        </a>
                    </div>

                    <a href="<?php echo APP_URL; ?>/reports" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                        <i class="fas fa-chart-bar mr-2"></i> Reports
                    </a>
                    <a href="<?php echo APP_URL; ?>/scan.php" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                        <i class="fas fa-barcode mr-2"></i> Scan
                    </a>

                    <?php if (isset($_SESSION['auth_type']) && $_SESSION['auth_type'] === 'local') : ?>
                    <a href="<?php echo APP_URL; ?>/activity-logs" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                        <i class="fas fa-history mr-2"></i> Logs
                    </a>
                    
                    <!-- Settings Group -->
                    <div class="mt-2">
                        <div class="px-4 py-2 text-xs uppercase tracking-wider text-white/80">Settings</div>
                        <a href="<?php echo APP_URL; ?>/settings/toast-templates" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-comment-dots mr-2"></i> Toast Templates
                        </a>
                        <a href="<?php echo APP_URL; ?>/settings/database" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-database mr-2"></i> Database Viewer
                        </a>
                        <a href="<?php echo APP_URL; ?>/database-schema" class="block px-4 py-3 rounded-lg hover:bg-white/20">
                            <i class="fas fa-sitemap mr-2"></i> Database Schema
                        </a>
                    </div>
                    <?php endif; ?>

                    <hr class="border-white/20 my-2">
                    <a href="<?php echo APP_URL; ?>/logout" class="block px-4 py-3 rounded-lg hover:bg-white/20 text-white">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <?php echo $content; ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-8 mt-auto">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="font-bold text-lg mb-3">
                        <i class="fas fa-warehouse mr-2"></i> H&S Inventory
                    </h3>
                    <p class="text-gray-400 text-sm">
                        Comprehensive inventory management system for efficient stock control and tracking.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?php echo APP_URL; ?>/dashboard" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-chevron-right mr-1 text-xs"></i> Dashboard
                        </a></li>
                        <li><a href="<?php echo APP_URL; ?>/products" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-chevron-right mr-1 text-xs"></i> Products
                        </a></li>
                        <li><a href="<?php echo APP_URL; ?>/reports" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-chevron-right mr-1 text-xs"></i> Reports
                        </a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">System Info</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><i class="fas fa-server mr-2"></i> Version 2.0</li>
                        <li><i class="fas fa-clock mr-2"></i> <?php echo date('Y-m-d H:i:s'); ?></li>
                        <li><i class="fas fa-user mr-2"></i> <?php echo $_SESSION['username'] ?? 'Guest'; ?></li>
                    </ul>
                </div>
            </div>
            <hr class="border-gray-700 my-6">
            <div class="text-center text-sm text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> H&S Inventory Management System. All rights reserved.</p>
                <p class="mt-2">
                    <i class="fas fa-shield-alt mr-1"></i> Secure • 
                    <i class="fas fa-bolt mr-1 ml-2"></i> Fast • 
                    <i class="fas fa-chart-line mr-1 ml-2"></i> Reliable
                </p>
            </div>
        </div>
    </footer>
    
    <!-- Custom JavaScript -->
    <script>
        const initialNotifications = <?php echo json_encode($notifications ?? []); ?>;
        const APP_URL = '<?php echo APP_URL; ?>';
        
        console.log('=== INLINE NOTIFICATION SCRIPT ===');
        console.log('Initial notifications:', initialNotifications);
        console.log('APP_URL:', APP_URL);
    </script>
    <script src="<?php echo APP_URL; ?>/public/js/toast.js"></script>
    <script src="<?php echo APP_URL; ?>/public/js/main.js"></script>
    <script>
        // Inline notification system
        (function() {
            console.log('Starting inline notification initialization');
            
            function initNotifications() {
                console.log('initNotifications called');
                
                const notificationBell = document.getElementById('notificationBell');
                const notificationDropdown = document.getElementById('notificationDropdown');
                const notificationCount = document.getElementById('notificationCount');
                const notificationList = document.getElementById('notificationList');
                const markAllAsReadBtn = document.getElementById('markAllAsReadBtn');

                if (!notificationBell || !notificationDropdown || !notificationCount || !notificationList || !markAllAsReadBtn) {
                    console.error('Notification elements not found');
                    return;
                }
                
                console.log('All notification elements found!');

                let isDropdownOpen = false;
                let currentNotifications = initialNotifications || [];
                
                console.log('Current notifications:', currentNotifications.length);

                function updateNotificationUI(notifications) {
                    console.log('Updating UI with', notifications.length, 'notifications');
                    currentNotifications = notifications;
                    
                    if (notifications.length > 0) {
                        notificationCount.textContent = notifications.length;
                        notificationCount.classList.remove('hidden');
                    } else {
                        notificationCount.classList.add('hidden');
                    }

                    notificationList.innerHTML = '';
                    if (notifications.length === 0) {
                        notificationList.innerHTML = '<p class="text-center text-gray-500 py-4">No new notifications</p>';
                    } else {
                        notifications.forEach(notification => {
                            const notificationItem = document.createElement('div');
                            notificationItem.dataset.id = notification.id;

                            // Color-coded backgrounds based on notification type
                            let bgClass = 'bg-blue-50 border-l-4 border-blue-500 hover:bg-blue-100';
                            let icon = 'fas fa-info-circle text-blue-600';
                            let textColor = 'text-blue-900';
                            let timeColor = 'text-blue-600';
                            
                            if (notification.type === 'inventory') {
                                // Check message content for severity
                                if (notification.message.includes('OUT OF STOCK') || notification.message.includes('⚠️')) {
                                    bgClass = 'bg-red-50 border-l-4 border-red-600 hover:bg-red-100';
                                    icon = 'fas fa-exclamation-circle text-red-600';
                                    textColor = 'text-red-900';
                                    timeColor = 'text-red-600';
                                } else if (notification.message.includes('CRITICAL') || notification.message.includes('🔴')) {
                                    bgClass = 'bg-orange-50 border-l-4 border-orange-600 hover:bg-orange-100';
                                    icon = 'fas fa-exclamation-triangle text-orange-600';
                                    textColor = 'text-orange-900';
                                    timeColor = 'text-orange-600';
                                } else if (notification.message.includes('LOW STOCK') || notification.message.includes('🟡')) {
                                    bgClass = 'bg-yellow-50 border-l-4 border-yellow-500 hover:bg-yellow-100';
                                    icon = 'fas fa-exclamation-triangle text-yellow-600';
                                    textColor = 'text-yellow-900';
                                    timeColor = 'text-yellow-700';
                                }
                            } else if (notification.type === 'activity') {
                                bgClass = 'bg-green-50 border-l-4 border-green-500 hover:bg-green-100';
                                icon = 'fas fa-check-circle text-green-600';
                                textColor = 'text-green-900';
                                timeColor = 'text-green-600';
                            } else if (notification.type === 'system') {
                                bgClass = 'bg-gray-50 border-l-4 border-gray-400 hover:bg-gray-100';
                                icon = 'fas fa-cog text-gray-600';
                                textColor = 'text-gray-900';
                                timeColor = 'text-gray-600';
                            }
                            
                            notificationItem.className = `p-4 ${bgClass} cursor-pointer transition-all duration-200 transform hover:scale-[1.02]`;

                            const div = document.createElement('div');
                            div.textContent = notification.message;
                            const escapedMessage = div.innerHTML;
                            
                            const date = new Date(notification.created_at);
                            const now = new Date();
                            const diffMins = Math.floor((now - date) / 60000);
                            let timeAgo = 'Just now';
                            if (diffMins >= 1 && diffMins < 60) timeAgo = diffMins + ' minute' + (diffMins > 1 ? 's' : '') + ' ago';
                            else if (diffMins >= 60 && diffMins < 1440) timeAgo = Math.floor(diffMins / 60) + ' hour' + (Math.floor(diffMins / 60) > 1 ? 's' : '') + ' ago';
                            else if (diffMins >= 1440) timeAgo = date.toLocaleString();

                            notificationItem.innerHTML = `
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="${icon} text-2xl"></i>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium ${textColor}">${escapedMessage}</p>
                                        <p class="text-xs ${timeColor} mt-1 font-semibold">
                                            <i class="fas fa-clock mr-1"></i>${timeAgo}
                                        </p>
                                    </div>
                                </div>
                            `;
                            notificationList.appendChild(notificationItem);
                        });
                    }
                    console.log('UI updated, list has', notificationList.children.length, 'items');
                }

                notificationBell.addEventListener('click', (e) => {
                    console.log('Bell clicked!');
                    e.stopPropagation();
                    isDropdownOpen = !isDropdownOpen;
                    
                    if (isDropdownOpen) {
                        console.log('Opening dropdown');
                        notificationDropdown.classList.remove('hidden');
                        notificationDropdown.classList.remove('opacity-0');
                        notificationDropdown.classList.remove('invisible');
                        notificationDropdown.classList.add('opacity-100');
                        updateNotificationUI(currentNotifications);
                    } else {
                        console.log('Closing dropdown');
                        notificationDropdown.classList.add('opacity-0');
                        notificationDropdown.classList.add('invisible');
                        setTimeout(() => {
                            notificationDropdown.classList.add('hidden');
                        }, 300);
                    }
                });

                document.addEventListener('click', (e) => {
                    if (isDropdownOpen && !notificationDropdown.contains(e.target) && !notificationBell.contains(e.target)) {
                        isDropdownOpen = false;
                        notificationDropdown.classList.add('opacity-0');
                        notificationDropdown.classList.add('invisible');
                        setTimeout(() => {
                            notificationDropdown.classList.add('hidden');
                        }, 300);
                    }
                });

                notificationList.addEventListener('click', (e) => {
                    const notificationItem = e.target.closest('[data-id]');
                    if (notificationItem) {
                        const notificationId = notificationItem.dataset.id;
                        fetch(APP_URL + '/notifications/mark_as_read?id=' + notificationId)
                            .then(() => {
                                notificationItem.style.opacity = '0';
                                setTimeout(() => {
                                    notificationItem.remove();
                                    currentNotifications = currentNotifications.filter(n => n.id != notificationId);
                                    if (currentNotifications.length === 0) {
                                        notificationCount.classList.add('hidden');
                                        notificationList.innerHTML = '<p class="text-center text-gray-500 py-4">No new notifications</p>';
                                    } else {
                                        notificationCount.textContent = currentNotifications.length;
                                    }
                                }, 300);
                            });
                    }
                });

                markAllAsReadBtn.addEventListener('click', () => {
                    fetch(APP_URL + '/notifications/mark_all_as_read')
                        .then(() => {
                            currentNotifications = [];
                            notificationList.innerHTML = '<p class="text-center text-gray-500 py-4">No new notifications</p>';
                            notificationCount.classList.add('hidden');
                        });
                });

                updateNotificationUI(currentNotifications);
                console.log('Notification system initialized!');
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initNotifications);
            } else {
                initNotifications();
            }
        })();
    </script>
    <script>
        function toggleMobileMenu(open = null) {
            const container = document.getElementById('mobileMenu');
            const panel = document.getElementById('mobileMenuPanel');
            const backdrop = document.getElementById('mobileMenuBackdrop');

            if (!container || !panel || !backdrop) {
                // Fallback: toggle container visibility if elements not found
                if (container) container.classList.toggle('hidden');
                return;
            }

            const isHidden = container.classList.contains('hidden');
            const shouldOpen = open === null ? isHidden : open;

            if (shouldOpen) {
                // Show container
                container.classList.remove('hidden');

                // Enable backdrop interactions and fade in
                backdrop.classList.remove('pointer-events-none');
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');

                // Slide panel in
                panel.classList.remove('translate-x-full');
            } else {
                // Slide panel out
                panel.classList.add('translate-x-full');

                // Fade out backdrop
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0');
                backdrop.classList.add('pointer-events-none');

                // After animation, hide container to remove from flow
                setTimeout(() => {
                    container.classList.add('hidden');
                }, 300);
            }
        }

        // Close when clicking backdrop
        document.addEventListener('DOMContentLoaded', () => {
            const backdrop = document.getElementById('mobileMenuBackdrop');
            if (backdrop) {
                backdrop.addEventListener('click', () => toggleMobileMenu(false));
            }
        });
    </script>
</body>
</html>
