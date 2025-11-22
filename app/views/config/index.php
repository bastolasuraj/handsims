<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex items-center">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-3 rounded-lg shadow-lg">
                <i class="fas fa-cog text-white text-xl"></i>
            </div>
            <div class="ml-4">
                <h1 class="text-2xl font-bold text-gray-800">Master Data Configuration</h1>
                <p class="text-sm text-gray-600">Manage categories, types, sizes, locations, and departments</p>
            </div>
        </div>
    </div>

    <!-- Configuration Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Categories Card -->
        <a href="<?php echo APP_URL; ?>/master-data/categories" class="group">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border-2 border-blue-200 hover:border-blue-400 transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-lg shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-folder text-white text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-blue-600"><?php echo $category_count; ?></span>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Categories</h3>
                <p class="text-sm text-gray-600">Manage product categories</p>
                <div class="mt-4 flex items-center text-blue-600 font-medium text-sm">
                    <span>Manage</span>
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                </div>
            </div>
        </a>

        <!-- Types Card -->
        <a href="<?php echo APP_URL; ?>/master-data/types" class="group">
            <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl p-6 border-2 border-pink-200 hover:border-pink-400 transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-pink-500 to-rose-600 p-3 rounded-lg shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-tags text-white text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-pink-600"><?php echo $type_count; ?></span>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Types</h3>
                <p class="text-sm text-gray-600">Manage product types</p>
                <div class="mt-4 flex items-center text-pink-600 font-medium text-sm">
                    <span>Manage</span>
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                </div>
            </div>
        </a>

        <!-- Sizes Card -->
        <a href="<?php echo APP_URL; ?>/master-data/sizes" class="group">
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border-2 border-green-200 hover:border-green-400 transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-3 rounded-lg shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-ruler text-white text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-green-600"><?php echo $size_count; ?></span>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Sizes</h3>
                <p class="text-sm text-gray-600">Manage product sizes</p>
                <div class="mt-4 flex items-center text-green-600 font-medium text-sm">
                    <span>Manage</span>
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                </div>
            </div>
        </a>

        <!-- Locations Card -->
        <a href="<?php echo APP_URL; ?>/master-data/locations" class="group">
            <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 border-2 border-orange-200 hover:border-orange-400 transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-orange-500 to-red-600 p-3 rounded-lg shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-warehouse text-white text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-orange-600"><?php echo $location_count; ?></span>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Locations</h3>
                <p class="text-sm text-gray-600">Manage warehouse locations</p>
                <div class="mt-4 flex items-center text-orange-600 font-medium text-sm">
                    <span>Manage</span>
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                </div>
            </div>
        </a>

        <!-- Departments Card -->
        <a href="<?php echo APP_URL; ?>/master-data/departments" class="group">
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border-2 border-purple-200 hover:border-purple-400 transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-3 rounded-lg shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-building text-white text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-purple-600"><?php echo $department_count; ?></span>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Departments</h3>
                <p class="text-sm text-gray-600">Manage departments</p>
                <div class="mt-4 flex items-center text-purple-600 font-medium text-sm">
                    <span>Manage</span>
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                </div>
            </div>
        </a>

        <!-- Sellers Card -->
        <a href="<?php echo APP_URL; ?>/master-data/sellers" class="group">
            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-6 border-2 border-teal-200 hover:border-teal-400 transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-r from-teal-500 to-cyan-600 p-3 rounded-lg shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fas fa-store text-white text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-teal-600"><?php echo $seller_count; ?></span>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Sellers</h3>
                <p class="text-sm text-gray-600">Manage sellers/suppliers</p>
                <div class="mt-4 flex items-center text-teal-600 font-medium text-sm">
                    <span>Manage</span>
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Info Box -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
        <div class="flex items-start">
            <div class="bg-blue-500 p-2 rounded-lg mr-4">
                <i class="fas fa-info-circle text-white text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 mb-2">About Master Data</h3>
                <p class="text-sm text-gray-700">
                    Master data configuration allows you to manage the foundational data used throughout the system. 
                    Categories and sizes must be created before adding products. Locations and departments are used for stock management.
                </p>
            </div>
        </div>
    </div>
</div>
