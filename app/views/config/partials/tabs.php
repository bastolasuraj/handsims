<?php
$current_page = basename($_SERVER['REQUEST_URI']);
$current_page = explode('?', $current_page)[0]; // Remove query parameters

$tabs = [
    ['name' => 'Categories', 'icon' => 'folder', 'url' => 'categories'],
    ['name' => 'Sizes', 'icon' => 'ruler', 'url' => 'sizes'],
    ['name' => 'Locations', 'icon' => 'warehouse', 'url' => 'locations'],
    ['name' => 'Departments', 'icon' => 'building', 'url' => 'departments'],
    ['name' => 'Sellers', 'icon' => 'store', 'url' => 'sellers'],
];
?>

<div class="bg-white shadow-xl rounded-xl overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50">
        <nav class="flex space-x-2 p-2" aria-label="Tabs">
            <?php foreach ($tabs as $tab) : ?>
            <a href="<?php echo APP_URL; ?>/master-data/<?php echo $tab['url']; ?>" 
               class="<?php echo $current_page === $tab['url'] ? 'bg-white shadow-md text-gray-800' : 'text-gray-600 hover:bg-gray-100'; ?> px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                <i class="fas fa-<?php echo $tab['icon']; ?> mr-2"></i> <?php echo $tab['name']; ?>
            </a>
            <?php endforeach; ?>
        </nav>
    </div>
</div>
