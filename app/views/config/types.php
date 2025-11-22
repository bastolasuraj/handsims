<?php
$is_edit = isset($editing_type) && $editing_type;
$form_action = $is_edit ? 'edit' : 'add';
$form_title = $is_edit ? 'Edit Type' : 'Add New Type';
$button_text = $is_edit ? 'Update Type' : 'Add Type';
$button_icon = $is_edit ? 'fa-save' : 'fa-plus';
?>

<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-gradient-to-r from-pink-500 to-rose-600 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-tags text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-800">Manage Types</h1>
                    <p class="text-sm text-gray-600">Add, edit, or delete product types</p>
                </div>
            </div>
        </div>
    </div>

    <div id="alert-area">
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Add/Edit Form -->
        <div class="bg-white shadow-xl rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4" id="form-title"><i class="fas <?php echo $button_icon; ?> text-pink-500 mr-2"></i><?php echo $form_title; ?></h2>
            <form id="type-form" method="POST" class="space-y-4" data-master-data-type="type">
                <?php require_once __DIR__ . '/../../Helpers/csrf.php';
                echo csrf_field(); ?>
                <input type="hidden" name="action" id="form-action" value="<?php echo $form_action; ?>">
                <?php if ($is_edit) : ?>
                <input type="hidden" name="id" id="item-id" value="<?php echo $editing_type['id']; ?>">
                <?php else : ?>
                <input type="hidden" name="id" id="item-id" value="">
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Name *</label>
                    <input type="text" name="name" id="type-name" required class="w-full px-4 py-2 rounded-lg border-2 border-gray-200 focus:border-pink-500 focus:ring-4 focus:ring-pink-100 transition-all" value="<?php echo $is_edit ? htmlspecialchars($editing_type['name']) : ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category (Parent)</label>
                    <select name="category_id" id="type-category_id" class="w-full px-4 py-2 rounded-lg border-2 border-gray-200 focus:border-pink-500 focus:ring-4 focus:ring-pink-100 transition-all">
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo ($is_edit && $editing_type['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="type-description" rows="2" class="w-full px-4 py-2 rounded-lg border-2 border-gray-200 focus:border-pink-500 focus:ring-4 focus:ring-pink-100 transition-all"><?php echo $is_edit ? htmlspecialchars($editing_type['description'] ?? '') : ''; ?></textarea>
                </div>
                <button type="submit" id="form-submit-button" class="w-full bg-gradient-to-r from-pink-500 to-rose-600 hover:from-pink-600 hover:to-rose-700 text-white font-semibold py-3 px-6 rounded-lg transition-all shadow-lg">
                    <i class="fas <?php echo $button_icon; ?> mr-2"></i> <span id="button-text"><?php echo $button_text; ?></span>
                </button>

            </form>
        </div>

        <!-- List -->
        <div class="bg-white shadow-xl rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-list text-pink-500 mr-2"></i>Existing Types (<span id="type-count"><?php echo count($types); ?></span>)</h2>
            <div class="space-y-3 max-h-[600px] overflow-y-auto" id="type-list">
                <?php if (empty($types)) : ?>
                <p class="text-gray-500 text-center py-8" id="no-types-message">No types yet. Add one to get started!</p>
                <?php else : ?>
                    <?php foreach ($types as $type) : ?>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-pink-300 transition-all" id="type-item-<?php echo $type['id']; ?>">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800" id="type-name-<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['name']); ?></h3>
                            <?php if ($type['category_name']) :
                                ?><p class="text-xs text-gray-600" id="type-category-<?php echo $type['id']; ?>">Category: <?php echo htmlspecialchars($type['category_name']); ?></p><?php
                            endif; ?>
                            <?php if ($type['description']) :
                                ?><p class="text-xs text-gray-500 mt-1" id="type-description-<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['description'] ?? ''); ?></p><?php
                            endif; ?>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button type="button" onclick="editMasterItem('type', '<?php echo base64_encode(json_encode($type)); ?>')" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded transition-all" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <form method="POST" class="inline delete-form">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $type['id']; ?>">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded transition-all" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo APP_URL; ?>/public/js/master-data-edit.js"></script>
