<?php
$is_edit = isset($editing_seller) && $editing_seller;
$form_action = $is_edit ? 'edit' : 'add';
$form_title = $is_edit ? 'Edit Seller' : 'Add New Seller';
$button_text = $is_edit ? 'Update Seller' : 'Add Seller';
$button_icon = $is_edit ? 'fa-save' : 'fa-plus';
?>

<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-gradient-to-r from-teal-500 to-cyan-600 p-3 rounded-lg shadow-lg">
                    <i class="fas fa-store text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-800">Manage Sellers</h1>
                    <p class="text-sm text-gray-600">Add, edit, or delete sellers/suppliers</p>
                </div>
            </div>
        </div>
    </div>

    <div id="alert-area"></div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Add/Edit Form -->
        <div class="bg-white shadow-xl rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4" id="form-title"><i class="fas <?php echo $button_icon; ?> text-teal-500 mr-2"></i><?php echo $form_title; ?></h2>
            <form id="seller-form" method="POST" class="space-y-3" data-master-data-type="seller">
                <?php require_once __DIR__ . '/../../Helpers/csrf.php';
                echo csrf_field(); ?>
                <input type="hidden" name="action" id="form-action" value="<?php echo $form_action; ?>">
                <?php if ($is_edit) : ?>
                <input type="hidden" name="id" id="item-id" value="<?php echo $editing_seller['id']; ?>">
                <?php else : ?>
                <input type="hidden" name="id" id="item-id" value="">
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Seller Name *</label>
                    <input type="text" name="name" id="seller-name" required class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-100 transition-all text-sm" value="<?php echo $is_edit ? htmlspecialchars($editing_seller['name']) : ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" id="seller-contact_person" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-100 transition-all text-sm" value="<?php echo $is_edit ? htmlspecialchars($editing_seller['contact_person']) : ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" id="seller-phone" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-100 transition-all text-sm" value="<?php echo $is_edit ? htmlspecialchars($editing_seller['phone']) : ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="seller-email" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-100 transition-all text-sm" value="<?php echo $is_edit ? htmlspecialchars($editing_seller['email']) : ''; ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
                    <textarea name="address" id="seller-address" rows="2" class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-100 transition-all text-sm"><?php echo $is_edit ? htmlspecialchars($editing_seller['address']) : ''; ?></textarea>
                </div>
                <button type="submit" id="form-submit-button" class="w-full bg-gradient-to-r from-teal-500 to-cyan-600 hover:from-teal-600 hover:to-cyan-700 text-white font-semibold py-2.5 px-6 rounded-lg transition-all shadow-lg text-sm">
                    <i class="fas <?php echo $button_icon; ?> mr-2"></i> <span id="button-text"><?php echo $button_text; ?></span>
                </button>

            </form>
        </div>

        <!-- List -->
        <div class="bg-white shadow-xl rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-list text-teal-500 mr-2"></i>Existing Sellers (<span id="seller-count"><?php echo count($sellers); ?></span>)</h2>
            <div class="space-y-3 max-h-[700px] overflow-y-auto" id="seller-list">
                <?php if (empty($sellers)) : ?>
                <p class="text-gray-500 text-center py-8" id="no-sellers-message">No sellers yet. Add one to get started!</p>
                <?php else : ?>
                    <?php foreach ($sellers as $seller) : ?>
                <div class="bg-gradient-to-r from-teal-50 to-cyan-50 rounded-lg p-4 border border-teal-200 hover:border-teal-400 transition-all" id="seller-item-<?php echo $seller['id']; ?>">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800 text-lg" id="seller-name-<?php echo $seller['id']; ?>"><?php echo htmlspecialchars($seller['name']); ?></h3>
                            <div class="grid grid-cols-2 gap-2 mt-2 text-sm">
                                <?php if ($seller['contact_person']) : ?>
                                <div class="flex items-center text-gray-600" id="seller-contact_person-<?php echo $seller['id']; ?>">
                                    <i class="fas fa-user text-teal-500 mr-2 w-4"></i>
                                    <span><?php echo htmlspecialchars($seller['contact_person']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($seller['phone']) : ?>
                                <div class="flex items-center text-gray-600" id="seller-phone-<?php echo $seller['id']; ?>">
                                    <i class="fas fa-phone text-teal-500 mr-2 w-4"></i>
                                    <span><?php echo htmlspecialchars($seller['phone']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($seller['email']) : ?>
                                <div class="flex items-center text-gray-600" id="seller-email-<?php echo $seller['id']; ?>">
                                    <i class="fas fa-envelope text-teal-500 mr-2 w-4"></i>
                                    <span><?php echo htmlspecialchars($seller['email']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($seller['address']) : ?>
                                <div class="flex items-start text-gray-600 col-span-2" id="seller-address-<?php echo $seller['id']; ?>">
                                    <i class="fas fa-map-marker-alt text-teal-500 mr-2 w-4 mt-1"></i>
                                    <span><?php echo htmlspecialchars($seller['address']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button type="button" onclick="editMasterItem('seller', '<?php echo base64_encode(json_encode($seller)); ?>')" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded transition-all" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <form method="POST" class="inline delete-form">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $seller['id']; ?>">
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
