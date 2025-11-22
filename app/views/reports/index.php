<style>
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .slide-in {
        animation: slideIn 0.5s ease-out;
    }
    
    .report-card:hover {
        transform: translateY(-5px) scale(1.02);
    }
</style>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow-lg rounded-xl p-6 slide-in">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-chart-bar text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-800">Reports Center</h1>
                    <p class="text-sm text-gray-600">Generate and export comprehensive inventory reports</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <span class="px-3 py-1 bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 rounded-full text-xs font-semibold">
                    <i class="fas fa-check-circle mr-1"></i> Ready
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Reports Section -->
    <div class="bg-gradient-to-br from-white to-blue-50 shadow-xl rounded-xl p-6 border border-blue-100 slide-in" style="animation-delay: 0.1s">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 p-2 rounded-lg shadow mr-3">
                <i class="fas fa-bolt text-white text-sm"></i>
            </div>
            Quick Reports
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Low Stock Report -->
            <form action="<?php echo APP_URL; ?>/reports/generate" method="POST">
                <input type="hidden" name="report_type" value="low_stock">
                <input type="hidden" name="format" value="csv">
                <button type="submit" class="w-full report-card bg-gradient-to-br from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white rounded-xl p-5 transition-all duration-300 shadow-lg hover:shadow-2xl text-left group">
                    <div class="flex items-start justify-between mb-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                        </div>
                        <i class="fas fa-arrow-right text-white/50 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-1">Low Stock Alert</h3>
                    <p class="text-sm text-white/90">Export items below minimum quantity</p>
                    <div class="mt-3 flex items-center text-xs text-white/70">
                        <i class="fas fa-clock mr-1"></i> Instant generation
                    </div>
                </button>
            </form>

            <!-- Monthly Transactions -->
            <form action="<?php echo APP_URL; ?>/reports/generate" method="POST">
                <input type="hidden" name="report_type" value="transactions">
                <input type="hidden" name="format" value="csv">
                <input type="hidden" name="date_from" value="<?php echo date('Y-m-01'); ?>">
                <input type="hidden" name="date_to" value="<?php echo date('Y-m-d'); ?>">
                <button type="submit" class="w-full report-card bg-gradient-to-br from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-xl p-5 transition-all duration-300 shadow-lg hover:shadow-2xl text-left group">
                    <div class="flex items-start justify-between mb-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-calendar-alt text-white text-lg"></i>
                        </div>
                        <i class="fas fa-arrow-right text-white/50 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-1">Monthly Activity</h3>
                    <p class="text-sm text-white/90">All transactions this month</p>
                    <div class="mt-3 flex items-center text-xs text-white/70">
                        <i class="fas fa-calendar-check mr-1"></i> <?php echo date('F Y'); ?>
                    </div>
                </button>
            </form>

            <!-- Full Inventory -->
            <form action="<?php echo APP_URL; ?>/reports/generate" method="POST">
                <input type="hidden" name="report_type" value="inventory">
                <input type="hidden" name="format" value="pdf">
                <button type="submit" class="w-full report-card bg-gradient-to-br from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl p-5 transition-all duration-300 shadow-lg hover:shadow-2xl text-left group">
                    <div class="flex items-start justify-between mb-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-warehouse text-white text-lg"></i>
                        </div>
                        <i class="fas fa-arrow-right text-white/50 group-hover:text-white group-hover:translate-x-1 transition-all"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-1">Full Inventory</h3>
                    <p class="text-sm text-white/90">Complete stock across locations</p>
                    <div class="mt-3 flex items-center text-xs text-white/70">
                        <i class="fas fa-file-pdf mr-1"></i> PDF format
                    </div>
                </button>
            </form>
        </div>
    </div>

    <!-- Custom Report Generation -->
    <div class="bg-gradient-to-br from-white to-purple-50 shadow-xl rounded-xl p-8 border border-purple-100 slide-in" style="animation-delay: 0.2s">
        <h2 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
            <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-2 rounded-lg shadow mr-3">
                <i class="fas fa-cog text-white text-sm"></i>
            </div>
            Custom Report Builder
        </h2>
        
        <form action="<?php echo APP_URL; ?>/reports/generate" method="POST" class="space-y-6">
            <!-- Report Type -->
            <div class="group">
                <label for="report_type" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-file-alt text-purple-500 mr-1"></i>
                    Report Type <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <select id="report_type" name="report_type" required
                            class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 text-sm font-medium hover:border-purple-300 appearance-none bg-white">
                        <option value="inventory">📦 Current Inventory Report</option>
                        <option value="transactions">📊 Transaction History Report</option>
                        <option value="low_stock">⚠️ Low Stock Report</option>
                        <option value="activity">📝 Activity Log Report</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400 group-hover:text-purple-500 transition-colors"></i>
                    </div>
                </div>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="group">
                    <label for="date_from" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-start text-green-500 mr-1"></i>
                        Date From
                    </label>
                    <input type="date" id="date_from" name="date_from"
                           class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-200 text-sm font-medium hover:border-green-300">
                </div>
                
                <div class="group">
                    <label for="date_to" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-end text-red-500 mr-1"></i>
                        Date To
                    </label>
                    <input type="date" id="date_to" name="date_to"
                           class="block w-full px-4 py-3 rounded-lg border-2 border-gray-200 shadow-sm focus:border-red-500 focus:ring-4 focus:ring-red-100 transition-all duration-200 text-sm font-medium hover:border-red-300">
                </div>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Location Filter -->
                <div class="group">
                    <label for="location_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-warehouse text-blue-500 mr-1"></i>
                        Location
                    </label>
                    <div class="relative">
                        <select id="location_id" name="location_id"
                                class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 text-sm font-medium hover:border-blue-300 appearance-none bg-white">
                            <option value="">All Locations</option>
                            <?php foreach ($locations as $location) : ?>
                            <option value="<?php echo $location['id']; ?>">
                                <?php echo htmlspecialchars($location['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                        </div>
                    </div>
                </div>

                <!-- Department Filter -->
                <div class="group" id="departmentField">
                    <label for="department_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-building text-orange-500 mr-1"></i>
                        Department
                    </label>
                    <div class="relative">
                        <select id="department_id" name="department_id"
                                class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-orange-500 focus:ring-4 focus:ring-orange-100 transition-all duration-200 text-sm font-medium hover:border-orange-300 appearance-none bg-white">
                            <option value="">All Departments</option>
                            <?php foreach ($departments as $department) : ?>
                            <option value="<?php echo $department['id']; ?>">
                                <?php echo htmlspecialchars($department['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 group-hover:text-orange-500 transition-colors"></i>
                        </div>
                    </div>
                </div>

                <!-- User Filter -->
                <div class="group" id="userField">
                    <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-indigo-500 mr-1"></i>
                        User
                    </label>
                    <div class="relative">
                        <select id="user_id" name="user_id"
                                class="block w-full px-4 py-3 pr-10 rounded-lg border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-sm font-medium hover:border-indigo-300 appearance-none bg-white">
                            <option value="">All Users</option>
                            <?php foreach ($users as $user) : ?>
                            <option value="<?php echo $user['id']; ?>">
                                <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 group-hover:text-indigo-500 transition-colors"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Format -->
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-lg p-4 border border-gray-200">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-file-export text-cyan-500 mr-1"></i>
                    Export Format <span class="text-red-400">*</span>
                </label>
                <div class="flex space-x-4">
                    <label class="flex items-center px-4 py-2 bg-white rounded-lg border-2 border-gray-200 cursor-pointer hover:border-cyan-500 transition-all has-[:checked]:border-cyan-500 has-[:checked]:bg-cyan-50">
                        <input type="radio" name="format" value="csv" checked
                               class="text-cyan-600 focus:ring-cyan-500">
                        <span class="ml-2 font-medium">
                            <i class="fas fa-file-csv text-green-500 mr-1"></i> CSV
                        </span>
                    </label>
                    <label class="flex items-center px-4 py-2 bg-white rounded-lg border-2 border-gray-200 cursor-pointer hover:border-cyan-500 transition-all has-[:checked]:border-cyan-500 has-[:checked]:bg-cyan-50">
                        <input type="radio" name="format" value="pdf"
                               class="text-cyan-600 focus:ring-cyan-500">
                        <span class="ml-2 font-medium">
                            <i class="fas fa-file-pdf text-red-500 mr-1"></i> PDF
                        </span>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t-2 border-gray-100">
                <button type="submit" 
                        class="bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-bold py-3 px-8 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center">
                    <i class="fas fa-download mr-2"></i> Generate Report
                </button>
            </div>
        </form>
    </div>

    <!-- Report History -->
    <div class="bg-gradient-to-br from-white to-gray-50 shadow-xl rounded-xl p-6 border border-gray-200 slide-in" style="animation-delay: 0.3s">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <div class="bg-gradient-to-r from-gray-500 to-slate-600 p-2 rounded-lg shadow mr-3">
                <i class="fas fa-history text-white text-sm"></i>
            </div>
            Report Information
        </h2>
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                <div class="text-sm text-gray-700">
                    <p class="font-semibold mb-1">On-Demand Generation</p>
                    <p>Reports are generated in real-time and downloaded directly. No storage required.</p>
                    <div class="mt-2 flex items-center text-xs text-gray-600">
                        <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                        Secure • Real-time • No data retention
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide filters based on report type
document.getElementById('report_type').addEventListener('change', function() {
    var departmentField = document.getElementById('departmentField');
    var userField = document.getElementById('userField');
    
    if (this.value === 'inventory' || this.value === 'low_stock') {
        departmentField.style.display = 'none';
        userField.style.display = 'none';
    } else if (this.value === 'transactions') {
        departmentField.style.display = 'block';
        userField.style.display = 'block';
    } else if (this.value === 'activity') {
        departmentField.style.display = 'none';
        userField.style.display = 'block';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('report_type').dispatchEvent(new Event('change'));
});
</script>
