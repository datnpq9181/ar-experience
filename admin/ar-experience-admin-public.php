<?php

// Include pages
include (plugin_dir_path(__FILE__) . '/pages/model-manager/ar-experience-admin-model-manager.php');
include (plugin_dir_path(__FILE__) . '/pages/license/ar-experience-admin-license.php');

// Add the AR Product View menu item and its sub-menus
function add_ar_product_view_menu() {
    // Add the main menu item
    add_menu_page(
        'AR Experience',
        'AR Experience',
        'manage_options',
        'ar-product-view',
        'ar_product_view_page',
        'dashicons-editor-code'
    );
    // Add the Model Manager sub-menu item
    add_submenu_page(
        'ar-product-view',
        'Model Manager',
        'Model Manager',
        'manage_options',
        'model-manager',
        'model_manager_page'
    );
    // Add the License sub-menu item
    add_submenu_page(
        'ar-product-view',
        'License',
        'License',
        'manage_options',
        'license',
        'license_page'
    );
}
add_action('admin_menu', 'add_ar_product_view_menu');

// Callback function for the AR Product View page
function ar_product_view_page()
{
    echo '<div class="wrap">';
    echo '<h1>AR Experience</h1>';
    echo '<h2>Using AR to enhance your shopping experience</h2>';
    echo '<p>Welcome to our AR Product View page! Here at WeDev, we\'re always looking for new ways to improve your shopping experience. That\'s why we\'re excited to offer AR product viewing for select items in our store.</p>';
    echo '<p>With AR, you can visualize products in your own space before making a purchase. Simply click the "View in AR" button on eligible product pages, and you\'ll be able to see a 3D model of the item superimposed on your camera view.</p>';
    echo '<p>Whether you\'re trying to determine if a piece of furniture will fit in your living room, or just want to get a better sense of a product\'s scale and details, AR product viewing can help. We hope you enjoy this feature and find it helpful in your shopping journey.</p>';
    echo '<h2>How to use AR product viewing</h2>';
    echo '<p>Using AR product viewing is easy! Here\'s a quick guide:</p>';
    echo '<ol>';
    echo '<li>Find a product with the "View in AR" button on its page</li>';
    echo '<li>Click the button to launch the AR viewer</li>';
    echo '<li>Follow the prompts to allow camera access and position the product in your space</li>';
    echo '<li>Enjoy exploring the product in AR!</li>';
    echo '</ol>';
    echo '<p>If you have any questions or encounter any issues while using AR product viewing, please don\'t hesitate to contact our customer support team. We\'re here to help make your shopping experience as seamless and enjoyable as possible.</p>';
    echo '</div>';
}
