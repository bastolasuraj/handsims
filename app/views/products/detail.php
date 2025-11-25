<style>
    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .fade-in-scale {
        animation: fadeInScale 0.5s ease-out;
    }
    
    .detail-row {
        transition: all 0.3s ease;
    }
    
    .detail-row:hover {
        background: linear-gradient(to right, #f0f9ff, #e0f2fe);
        transform: translateX(5px);
    }
</style>

<div class="space-y-6">
    <!-- Back Button -->
    <div class="fade-in-scale">
        <a href="<?php echo APP_URL; ?>/products" 
           class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Products
        </a>
    </div>

    <!-- Product Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-2xl p-8 text-white fade-in-scale" style="animation-delay: 0.1s">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold mb-2">
                    <?php echo htmlspecialchars($product['part_number']); ?>
                </h1>
                <p class="text-xl text-white/90 mb-4">
                    <?php echo htmlspecialchars($product['product_type'] ?? ''); ?>
                </p>
                <div class="flex flex-wrap gap-2">
                    <?php if ($product['category_name']) : ?>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-white/20 backdrop-blur-lg border border-white/30">
                        <i class="fas fa-tag mr-2"></i>
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </span>
                    <?php endif; ?>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-green-400 to-emerald-500 text-white">
                        <i class="fas fa-boxes mr-2"></i>
                        <?php echo $product['total_stock'] ?? 0; ?> Total Units
                    </span>
                </div>
            </div>
            <div class="flex flex-col space-y-2">
                <a href="<?php echo APP_URL; ?>/products/edit?id=<?php echo $product['id']; ?>" 
                   class="bg-white/20 backdrop-blur-lg hover:bg-white/30 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 border border-white/30 text-center">
                    <i class="fas fa-edit mr-2"></i> Edit Product
                </a>
                <a href="<?php echo APP_URL; ?>/stock/add?product_id=<?php echo $product['id']; ?>" 
                   class="bg-gradient-to-r from-green-400 to-emerald-500 hover:from-green-500 hover:to-emerald-600 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg text-center">
                    <i class="fas fa-plus-circle mr-2"></i> Add Stock
                </a>
            </div>
        </div>
    </div>

    <!-- Product Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="bg-white shadow-xl rounded-xl p-6 fade-in-scale" style="animation-delay: 0.2s">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-2 rounded-lg mr-3">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                Product Information
            </h2>
            <dl class="space-y-4">
                <div class="detail-row p-3 rounded-lg">
                    <dt class="text-sm font-semibold text-gray-500 mb-1">Part Number</dt>
                    <dd class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($product['part_number']); ?></dd>
                </div>
                <div class="detail-row p-3 rounded-lg">
                    <dt class="text-sm font-semibold text-gray-500 mb-1">Product Type</dt>
                    <dd class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($product['product_type'] ?? ''); ?></dd>
                </div>
                <div class="detail-row p-3 rounded-lg">
                    <dt class="text-sm font-semibold text-gray-500 mb-1">Category</dt>
                    <dd>
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800">
                            <?php echo htmlspecialchars($product['category_name'] ?? 'Not categorized'); ?>
                        </span>
                    </dd>
                </div>
                <?php if ($product['description']) : ?>
                <div class="detail-row p-3 rounded-lg">
                    <dt class="text-sm font-semibold text-gray-500 mb-1">Description</dt>
                    <dd class="text-gray-700"><?php echo nl2br(htmlspecialchars($product['description'])); ?></dd>
                </div>
                <?php endif; ?>
                <div class="detail-row p-3 rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200">
                    <dt class="text-sm font-semibold text-green-700 mb-1">Total Stock</dt>
                    <dd class="text-2xl font-bold text-green-800"><?php echo $product['total_stock'] ?? 0; ?> units</dd>
                </div>
            </dl>
        </div>

        <!-- Codes and Identifiers -->
        <div class="bg-white shadow-xl rounded-xl p-6 fade-in-scale" style="animation-delay: 0.3s">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-2 rounded-lg mr-3">
                    <i class="fas fa-qrcode text-white"></i>
                </div>
                Codes & Identifiers
            </h2>
            <div class="space-y-6">
                <?php
                // Load autoloader and include barcode generator
                if (file_exists(APP_ROOT . '/vendor/autoload.php')) {
                    include_once APP_ROOT . '/vendor/autoload.php';
                }
                require_once APP_ROOT . '/app/Helpers/BarcodeGenerator.php';
                use App\Helpers\BarcodeGenerator;

                $barcodeValue = $product['part_number'];

                // Generate security token for QR code
                $securityToken = hash_hmac('sha256', $product['id'], SECURE_AUTH_KEY);

                // Create secure URL with token
                $secureUrl = APP_URL . '/scan.php?id=' . $product['id'] . '&token=' . $securityToken;

                // QR Code data - use secure URL
                $qrData = $secureUrl;
                ?>
                
                <?php if ($barcodeValue) : ?>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Barcode</h3>
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-2 border-gray-200 p-4 rounded-xl text-center">
                        <?php echo BarcodeGenerator::generateBarcode($barcodeValue, 2, 60); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">QR Code</h3>
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 p-4 rounded-xl text-center cursor-pointer hover:shadow-lg transition-all duration-300 hover:scale-105" onclick="openQRLightbox()" title="Click to enlarge">
                        <div id="qrCodeSmall">
                            <?php
                            // Generate QR code once and store it
                            $qrCodeSVG = BarcodeGenerator::generateQRCode($qrData, 180);
                            echo $qrCodeSVG;
                            ?>
                        </div>
                        <p class="mt-2 text-xs text-purple-600 font-medium">
                            <i class="fas fa-search-plus mr-1"></i> Click to enlarge for scanning
                        </p>
                    </div>
                </div>
                
                <!-- Print and Download Options -->
                <div class="grid grid-cols-3 gap-2 pt-4 border-t">
                    <button onclick="printCodes()" 
                            class="bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-bold py-2 px-3 rounded-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <a href="<?php echo APP_URL; ?>/barcode.php?type=barcode&product_id=<?php echo $product['id']; ?>" 
                       download="barcode_<?php echo $product['part_number']; ?>.svg"
                       class="bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 text-white text-sm font-bold py-2 px-3 rounded-lg transition-all duration-200 transform hover:scale-105 text-center">
                        <i class="fas fa-download mr-1"></i> Barcode
                    </a>
                    <a href="<?php echo APP_URL; ?>/barcode.php?type=qr&product_id=<?php echo $product['id']; ?>" 
                       download="qr_<?php echo $product['part_number']; ?>.svg"
                       class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white text-sm font-bold py-2 px-3 rounded-lg transition-all duration-200 transform hover:scale-105 text-center">
                        <i class="fas fa-download mr-1"></i> QR
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory by Location Table -->
    <div class="bg-white shadow-2xl rounded-xl overflow-hidden fade-in-scale" style="animation-delay: 0.4s">
        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-6">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-warehouse mr-3"></i>
                Stock by Location & Size
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-100 to-gray-200">
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>
                                Location
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-ruler mr-2 text-purple-500"></i>
                                Size
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-cubes mr-2 text-blue-500"></i>
                                Quantity
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-flag mr-2 text-orange-500"></i>
                                Min Qty
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-traffic-light mr-2 text-green-500"></i>
                                Status
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-clock mr-2 text-gray-500"></i>
                                Last Updated
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($inventory as $index => $item) : ?>
                    <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 transition-all duration-300" style="animation: fadeInScale 0.5s ease-out <?php echo $index * 0.1; ?>s both">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-gradient-to-r from-red-400 to-rose-500 p-1.5 rounded mr-2">
                                    <i class="fas fa-warehouse text-white text-xs"></i>
                                </div>
                                <span class="font-medium text-gray-900"><?php echo htmlspecialchars($item['location_name']); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800">
                                <?php echo htmlspecialchars($item['size'] ?? 'One Size'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-lg font-bold <?php echo $item['quantity'] > $item['min_quantity'] * 2 ? 'text-green-600' : ($item['quantity'] > $item['min_quantity'] ? 'text-yellow-600' : 'text-red-600'); ?>">
                                <?php echo $item['quantity']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                            <?php echo $item['min_quantity']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($item['quantity'] <= $item['min_quantity']) : ?>
                            <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-red-400 to-rose-600 text-white animate-pulse">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Low Stock
                            </span>
                            <?php elseif ($item['quantity'] <= $item['min_quantity'] * 2) : ?>
                            <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 text-white">
                                <i class="fas fa-exclamation mr-1"></i> Warning
                            </span>
                            <?php else : ?>
                            <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-green-400 to-emerald-500 text-white">
                                <i class="fas fa-check-circle mr-1"></i> In Stock
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <i class="far fa-calendar mr-1"></i>
                            <?php echo date('M d, Y', strtotime($item['last_updated'])); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($inventory)) : ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gradient-to-r from-gray-200 to-gray-300 p-6 rounded-full mb-4">
                                    <i class="fas fa-box-open text-4xl text-gray-500"></i>
                                </div>
                                <p class="text-lg font-medium text-gray-600">No inventory records found</p>
                                <a href="<?php echo APP_URL; ?>/stock/add?product_id=<?php echo $product['id']; ?>" 
                                   class="mt-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold py-2 px-6 rounded-lg hover:shadow-lg transition-all duration-200">
                                    <i class="fas fa-plus mr-2"></i> Add Stock
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transaction History Table -->
    <div class="bg-white shadow-2xl rounded-xl overflow-hidden fade-in-scale" style="animation-delay: 0.5s">
        <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-6">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-history mr-3"></i>
                Transaction History
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-100 to-gray-200">
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-calendar mr-2 text-blue-500"></i>
                                Date
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-exchange-alt mr-2 text-purple-500"></i>
                                Type
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-warehouse mr-2 text-orange-500"></i>
                                Location
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-building mr-2 text-red-500"></i>
                                Department
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-ruler mr-2 text-indigo-500"></i>
                                Size
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-sort-numeric-up mr-2 text-green-500"></i>
                                Qty
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-user mr-2 text-cyan-500"></i>
                                User
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <div class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-comment mr-2 text-gray-500"></i>
                                Remarks
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($transactions as $index => $transaction) : ?>
                    <tr class="hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 transition-all duration-300" style="animation: fadeInScale 0.5s ease-out <?php echo $index * 0.1; ?>s both">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center">
                                <i class="far fa-clock text-gray-400 mr-2"></i>
                                <div>
                                    <div class="font-medium text-gray-900"><?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo date('H:i', strtotime($transaction['transaction_date'])); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($transaction['transaction_type'] == 'IN') : ?>
                            <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-green-400 to-emerald-500 text-white">
                                <i class="fas fa-plus mr-1"></i> IN
                            </span>
                            <?php else : ?>
                            <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-red-400 to-rose-500 text-white">
                                <i class="fas fa-minus mr-1"></i> OUT
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                            <?php echo htmlspecialchars($transaction['location_name']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?php echo htmlspecialchars($transaction['department_name'] ?? '-'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-800">
                                <?php echo htmlspecialchars($transaction['size'] ?? 'One Size'); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-lg font-bold <?php echo $transaction['transaction_type'] == 'IN' ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo $transaction['transaction_type'] == 'IN' ? '+' : '-'; ?><?php echo $transaction['quantity']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-6 w-6 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 flex items-center justify-center text-white text-xs font-bold mr-2">
                                    <?php echo strtoupper(substr($transaction['username'] ?? 'S', 0, 1)); ?>
                                </div>
                                <span class="text-sm text-gray-700"><?php echo htmlspecialchars($transaction['username'] ?? 'System'); ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?php if ($transaction['remarks']) : ?>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-comment-dots text-gray-400 mr-1"></i>
                                    <?php echo htmlspecialchars($transaction['remarks']); ?>
                                </span>
                            <?php else : ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($transactions)) : ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gradient-to-r from-gray-200 to-gray-300 p-6 rounded-full mb-4">
                                    <i class="fas fa-history text-4xl text-gray-500"></i>
                                </div>
                                <p class="text-lg font-medium text-gray-600">No transaction history</p>
                                <p class="text-sm text-gray-500 mt-2">Transactions will appear here when stock is added or removed</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- QR Code Lightbox -->
<div id="qrLightbox" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; justify-content: center; align-items: center;" onclick="closeQRLightbox()">
    <div style="position: relative; max-width: 90%; max-height: 90%; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.5);" onclick="event.stopPropagation()">
        <button onclick="closeQRLightbox()" style="position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; border: none; width: 40px; height: 40px; border-radius: 50%; font-size: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: bold; transition: all 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            ×
        </button>
        <div style="text-align: center;">
            <h2 style="margin-bottom: 20px; color: #333;">
                <i class="fas fa-qrcode" style="color: #667eea;"></i> Scan QR Code
            </h2>
            <div id="qrCodeLarge" style="display: inline-block; padding: 20px; background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <!-- QR Code will be inserted here by JavaScript -->
            </div>
            <p style="margin-top: 20px; color: #666; font-size: 14px;">
                <i class="fas fa-mobile-alt"></i> Scan with your mobile device to view product details
            </p>
            <p style="margin-top: 10px; color: #999; font-size: 12px;">
                Product: <strong><?php echo htmlspecialchars($product['part_number']); ?></strong>
            </p>
        </div>
    </div>
</div>

<script>
function openQRLightbox() {
    // Clone the small QR code and scale it up
    const smallQR = document.getElementById('qrCodeSmall');
    const largeQR = document.getElementById('qrCodeLarge');
    
    // Copy the SVG content
    largeQR.innerHTML = smallQR.innerHTML;
    
    // Scale up the SVG
    const svg = largeQR.querySelector('svg');
    if (svg) {
        svg.setAttribute('width', '400');
        svg.setAttribute('height', '400');
    }
    
    document.getElementById('qrLightbox').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeQRLightbox() {
    document.getElementById('qrLightbox').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close lightbox on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeQRLightbox();
    }
});

function printCodes() {
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    const productInfo = {
        partNumber: '<?php echo addslashes($product['part_number']); ?>',
        partType: '<?php echo addslashes($product['product_type'] ?? ''); ?>',
        category: '<?php echo addslashes($product['category_name'] ?? 'N/A'); ?>',
        totalStock: '<?php echo $product['total_stock'] ?? 0; ?>'
    };
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print Codes - ${productInfo.partNumber}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .codes-container { display: flex; justify-content: space-around; margin: 40px 0; }
                .code-section { text-align: center; padding: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Product Codes - ${productInfo.partNumber}</h1>
            </div>
            <div class="codes-container">
                <div class="code-section">
                    <h3>Barcode</h3>
                    <?php echo BarcodeGenerator::generateBarcode($barcodeValue, 2, 80); ?>
                </div>
                <div class="code-section">
                    <h3>QR Code</h3>
                    <?php echo BarcodeGenerator::generateQRCode($qrData, 300); ?>
                </div>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    setTimeout(() => printWindow.print(), 500);
}
</script>