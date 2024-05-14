<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://datngo.com
 * @since      1.0.0
 *
 * @package    Ar_Experience
 * @subpackage Ar_Experience/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <div id="license_message"></div>
    <form method="post" action="options.php">
        <?php
        settings_errors(); // Hiển thị lỗi nếu có
        settings_fields('ar_experience_options_group');
        do_settings_sections('ar_experience');
        submit_button(__('Activate', 'ar-experience'), 'primary', 'activate_license', false);
        ?>
    </form>
</div>


