<?php
$is_edit = isset($editing_category) && $editing_category;
$form_action = $is_edit ? 'edit' : 'add';
$form_title = $is_edit ? 'Edit Category' : 'Add New Category';
$button_text = $is_edit ? 'Update Category' : 'Add Category';
$button_icon = $is_edit ? 'fa-save' : 'fa-plus';
?>

<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-folder text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-800">Manage Categories</h1>
                    <p class="text-sm text-gray-600">Add, edit, or delete product categories</p>
                </div>
            </div>
        </div>
    </div>

    <div id="alert-area">
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Add/Edit Form -->
        <div class="bg-white shadow-xl rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4" id="form-title"><i class="fas <?php echo $button_icon; ?> text-blue-500 mr-2"></i><?php echo $form_title; ?></h2>
            <form id="category-form" method="POST" class="space-y-4" data-master-data-type="category">
                <?php require_once __DIR__ . '/../../Helpers/csrf.php';
                echo csrf_field(); ?>
                <input type="hidden" name="action" id="form-action" value="<?php echo $form_action; ?>">
                <?php if ($is_edit) : ?>
                <input type="hidden" name="id" id="item-id" value="<?php echo $editing_category['id']; ?>">
                <?php else : ?>
                <input type="hidden" name="id" id="item-id" value="">
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Name *</label>
                    <input type="text" name="name" id="category-name" required class="w-full px-4 py-2 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all" value="<?php echo $is_edit ? htmlspecialchars($editing_category['name']) : ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="category-description" rows="2" class="w-full px-4 py-2 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all"><?php echo $is_edit ? htmlspecialchars($editing_category['description'] ?? '') : ''; ?></textarea>
                </div>
                <button type="submit" id="form-submit-button" class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition-all shadow-lg">
                    <i class="fas <?php echo $button_icon; ?> mr-2"></i> <span id="button-text"><?php echo $button_text; ?></span>
                </button>

            </form>
        </div>

        <!-- List -->
        <div class="bg-white shadow-xl rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-list text-blue-500 mr-2"></i>Existing Categories (<span id="category-count"><?php echo count($categories); ?></span>)</h2>
            <div class="space-y-3 max-h-[600px] overflow-y-auto" id="category-list">
                <?php if (empty($categories)) : ?>
                <p class="text-gray-500 text-center py-8" id="no-categories-message">No categories yet. Add one to get started!</p>
                <?php else : ?>
                    <?php foreach ($categories as $cat) : ?>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-blue-300 transition-all" id="category-item-<?php echo $cat['id']; ?>">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800" id="category-name-<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></h3>
                            <?php if ($cat['description']) :
                                ?><p class="text-xs text-gray-500 mt-1" id="category-description-<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['description'] ?? ''); ?></p><?php
                            endif; ?>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button type="button" onclick="editMasterItem('category', '<?php echo base64_encode(json_encode($cat)); ?>')" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded transition-all" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <form method="POST" class="inline delete-form">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
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
