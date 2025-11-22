<?php
$is_edit = isset($editing_size) && $editing_size;
$form_action = $is_edit ? 'edit' : 'add';
$form_title = $is_edit ? 'Edit Size' : 'Add New Size';
$button_text = $is_edit ? 'Update Size' : 'Add Size';
$button_icon = $is_edit ? 'fa-save' : 'fa-plus';
?>

<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-ruler text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-800">Manage Sizes</h1>
                    <p class="text-sm text-gray-600">Add, edit, or delete product sizes</p>
                </div>
            </div>
        </div>
    </div>

    <div id="alert-area"></div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Add/Edit Form -->
        <div class="bg-white shadow-xl rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4" id="form-title"><i class="fas <?php echo $button_icon; ?> text-green-500 mr-2"></i><?php echo $form_title; ?></h2>
            <form id="size-form" method="POST" class="space-y-4" data-master-data-type="size">
                <?php require_once __DIR__ . '/../../Helpers/csrf.php';
                echo csrf_field(); ?>
                <input type="hidden" name="action" id="form-action" value="<?php echo $form_action; ?>">
                <?php if ($is_edit) : ?>
                <input type="hidden" name="id" id="item-id" value="<?php echo $editing_size['id']; ?>">
                <?php else : ?>
                <input type="hidden" name="id" id="item-id" value="">
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Size *</label>
                    <input type="text" name="size" id="size-name" required placeholder="Enter size (e.g., S, M, L, XL)" class="w-full px-4 py-2 rounded-lg border-2 border-gray-200 focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all" value="<?php echo $is_edit ? htmlspecialchars($editing_size['size']) : ''; ?>">
                </div>
                <button type="submit" id="form-submit-button" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3 px-6 rounded-lg transition-all shadow-lg">
                    <i class="fas <?php echo $button_icon; ?> mr-2"></i> <span id="button-text"><?php echo $button_text; ?></span>
                </button>

            </form>
        </div>

        <!-- List -->
        <div class="bg-white shadow-xl rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-list text-green-500 mr-2"></i>Existing Sizes (<span id="size-count"><?php echo count($sizes); ?></span>)</h2>
            <div class="space-y-3 max-h-[600px] overflow-y-auto" id="size-list">
                <?php if (empty($sizes)) : ?>
                <p class="text-gray-500 text-center py-8" id="no-sizes-message">No sizes yet. Add one to get started!</p>
                <?php else : ?>
                    <?php foreach ($sizes as $size) : ?>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:border-green-300 transition-all" id="size-item-<?php echo $size['id']; ?>">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-800 text-lg" id="size-name-<?php echo $size['id']; ?>"><?php echo htmlspecialchars($size['size']); ?></span>
                        <div class="flex space-x-2">
                            <button type="button" onclick="editMasterItem('size', '<?php echo base64_encode(json_encode($size)); ?>')" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded transition-all" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <form method="POST" class="inline delete-form">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $size['id']; ?>">
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
