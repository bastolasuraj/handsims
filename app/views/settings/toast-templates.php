<?php
// Load CSRF helper at the top of the file
require_once __DIR__ . '/../../Helpers/csrf.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toast Message Templates - <?php echo APP_NAME; ?></title>
</head>
<body>
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                        <i class="fas fa-comment-dots text-blue-600"></i>
                        Toast Message Templates
                    </h1>
                    <p class="text-gray-600 mt-2">Customize notification messages shown after actions. Use placeholders to make messages dynamic.</p>
                </div>
                <div class="flex gap-3">
                    <a href="<?php echo APP_URL; ?>/dashboard" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                    <form method="POST" action="<?php echo APP_URL; ?>/settings/toast-templates/reset-all" onsubmit="return confirm('Are you sure you want to reset ALL templates to defaults? This cannot be undone.');">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors flex items-center gap-2">
                            <i class="fas fa-undo"></i>
                            Reset All to Defaults
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
                <div>
                    <h3 class="font-semibold text-blue-800 mb-2">How to use placeholders:</h3>
                    <p class="text-blue-700 text-sm mb-2">Placeholders are special codes that get replaced with actual values when the message is shown. For example:</p>
                    <ul class="text-blue-700 text-sm space-y-1 ml-4">
                        <li><code class="bg-blue-100 px-2 py-1 rounded">%q</code> = Quantity (e.g., "5")</li>
                        <li><code class="bg-blue-100 px-2 py-1 rounded">%s</code> = Size (e.g., "Large")</li>
                        <li><code class="bg-blue-100 px-2 py-1 rounded">%p</code> = Product type (e.g., "Safety Gloves")</li>
                        <li><code class="bg-blue-100 px-2 py-1 rounded">%l</code> = Location name (e.g., "Main Warehouse")</li>
                    </ul>
                    <p class="text-blue-700 text-sm mt-2">Each template has its own set of available placeholders listed below.</p>
                </div>
            </div>
        </div>

        <!-- Templates Grid -->
        <div class="grid grid-cols-1 gap-6">
            <?php foreach ($templates as $template) : ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4">
                    <h3 class="text-white font-semibold text-lg flex items-center gap-2">
                        <i class="fas fa-tag"></i>
                        <?php echo ucwords(str_replace('_', ' ', $template['template_key'])); ?>
                    </h3>
                    <p class="text-blue-100 text-sm mt-1"><?php echo htmlspecialchars($template['description']); ?></p>
                </div>
                
                <div class="p-6">
                    <form method="POST" action="<?php echo APP_URL; ?>/settings/toast-templates/update" class="space-y-4">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="template_key" value="<?php echo htmlspecialchars($template['template_key']); ?>">
                        
                        <!-- Available Placeholders -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-code text-purple-600"></i>
                                Available Placeholders:
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <?php
                                $placeholders = explode(', ', $template['available_placeholders']);
                                foreach ($placeholders as $placeholder) :
                                    list($code, $description) = explode(' = ', $placeholder);
                                    ?>
                                <span class="inline-flex items-center gap-2 bg-white px-3 py-1 rounded-full border border-gray-300 text-sm">
                                    <code class="font-mono font-bold text-purple-600"><?php echo htmlspecialchars($code); ?></code>
                                    <span class="text-gray-600"><?php echo htmlspecialchars($description); ?></span>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Template Message Input -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-edit text-blue-600"></i>
                                Template Message:
                            </label>
                            <textarea 
                                name="template_message" 
                                rows="3" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                required
                                placeholder="Enter your custom message template..."
                            ><?php echo htmlspecialchars($template['template_message']); ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-lightbulb text-yellow-500"></i>
                                Tip: Use the placeholders above to create dynamic messages
                            </p>
                        </div>
                        
                        <!-- Preview -->
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 p-4 rounded-lg border border-green-200">
                            <h4 class="font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-eye text-green-600"></i>
                                Preview Example:
                            </h4>
                            <div class="bg-white p-3 rounded-lg border border-gray-200">
                                <p class="text-gray-800 font-medium">
                                    <?php
                                    // Generate example preview
                                    $preview = $template['template_message'];
                                    $preview = str_replace('%q', '<span class="text-blue-600 font-bold">5</span>', $preview);
                                    $preview = str_replace('%s', '<span class="text-purple-600 font-bold">Large</span>', $preview);
                                    $preview = str_replace('%p', '<span class="text-green-600 font-bold">Safety Gloves</span>', $preview);
                                    $preview = str_replace('%l', '<span class="text-orange-600 font-bold">Main Warehouse</span>', $preview);
                                    $preview = str_replace('%d', '<span class="text-red-600 font-bold">Engineering</span>', $preview);
                                    $preview = str_replace('%f', '<span class="text-indigo-600 font-bold">Storage A</span>', $preview);
                                    $preview = str_replace('%t', '<span class="text-pink-600 font-bold">Storage B</span>', $preview);
                                    $preview = str_replace('%n', '<span class="text-teal-600 font-bold">Product Name</span>', $preview);
                                    $preview = str_replace('%pn', '<span class="text-cyan-600 font-bold">ABC-123</span>', $preview);
                                    echo $preview;
                                    ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-2">
                            <button 
                                type="submit" 
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all font-semibold flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                            >
                                <i class="fas fa-save"></i>
                                Save Template
                            </button>
                            <button 
                                type="button" 
                                onclick="resetTemplate('<?php echo htmlspecialchars($template['template_key']); ?>')"
                                class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors font-semibold flex items-center gap-2"
                            >
                                <i class="fas fa-undo"></i>
                                Reset to Default
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Last Updated -->
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-clock"></i>
                        Last updated: <?php echo date('M d, Y H:i', strtotime($template['updated_at'])); ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function resetTemplate(templateKey) {
            if (confirm('Are you sure you want to reset this template to its default value?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo APP_URL; ?>/settings/toast-templates/reset';
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = 'csrf';
                csrfInput.value = '<?php echo csrf_token(); ?>';
                
                const keyInput = document.createElement('input');
                keyInput.type = 'hidden';
                keyInput.name = 'template_key';
                keyInput.value = templateKey;
                
                form.appendChild(csrfInput);
                form.appendChild(keyInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
