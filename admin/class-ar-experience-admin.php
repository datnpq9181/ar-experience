<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://datngo.com
 * @since      1.0.0
 *
 * @package    Ar_Experience
 * @subpackage Ar_Experience/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ar_Experience
 * @subpackage Ar_Experience/admin
 * @author     Dat Ngo <contact@realitech.dev>
 */
class Ar_Experience_Admin {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/ar-experience-core.php';
        add_action('add_meta_boxes', array($this, 'add_ar_model_meta_box'));
        add_action('woocommerce_process_product_meta', array($this, 'save_ar_model_meta_box'));
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        add_action('admin_init', array($this, 'settings_init'));
        add_action('admin_init', array($this, 'validate_license_on_admin_init'));
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            __('AR Experience Settings', 'ar-experience'),
            __('AR Experience', 'ar-experience'),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_configuration_page'),
            'dashicons-admin-generic',
            20
        );
    }

    public function display_configuration_page() {
        $options = get_option('ar_experience_options');
        $license_status = isset($options['license_status']) ? $options['license_status'] : '';
        $is_license_valid = ($license_status === 'valid');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Configuration', 'ar-experience'); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_errors();
                settings_fields('ar_experience_options_group');
                ?>
                <div class="license-section">
                    <div class="license-key-row">
                        <?php do_settings_sections('ar_experience_license'); ?>
                        <div class="license-actions">
                            <?php if ($is_license_valid): ?>
                                <button type="submit" name="deactivate_license" class="button button-secondary"><?php echo esc_html__('Deactivate', 'ar-experience'); ?></button>
                            <?php else: ?>
                                <button type="submit" class="button button-primary"><?php echo esc_html__('Activate', 'ar-experience'); ?></button>
                            <?php endif; ?>
                            <a href="#" target="_blank"><?php echo esc_html__('How to find your purchase code?', 'ar-experience'); ?></a>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <h2><?php echo esc_html__('Enable/Disable', 'ar-experience'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="view_in_space"><?php echo esc_html__('Enable WooCommerce Product View in AR', 'ar-experience'); ?></label>
                            </th>
                            <td>
                                <input id="view_in_space" name="ar_experience_options[view_in_space]" type="checkbox" value="1" <?php checked(1, isset($options['view_in_space']) ? $options['view_in_space'] : 0, true); ?> <?php echo $is_license_valid ? '' : 'disabled'; ?> />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="ruler_button"><?php echo esc_html__('Enable Ruler Button', 'ar-experience'); ?></label>
                            </th>
                            <td>
                                <input id="ruler_button" name="ar_experience_options[ruler_button]" type="checkbox" value="1" <?php checked(1, isset($options['ruler_button']) ? $options['ruler_button'] : 0, true); ?> <?php echo $is_license_valid ? '' : 'disabled'; ?> />
                            </td>
                        </tr>
                    </table>
                </div>
                <?php submit_button(esc_html__('Save Changes', 'ar-experience')); ?>
                <div class="rating">
                    <?php echo esc_html__('If you really like our plugin, please leave us a ', 'ar-experience'); ?>
                    <a href="https://wordpress.org/support/plugin/ar-experience/reviews/#new-post" target="_blank">
                        &#9733;&#9733;&#9733;&#9733;&#9733;
                    </a>
                    <?php echo esc_html__(', we\'ll really appreciate it.', 'ar-experience'); ?>
                </div>
            </form>
        </div>
        <?php
    }
    
    
    
    
    public function settings_init() {
        register_setting(
            'ar_experience_options_group',
            'ar_experience_options',
            array($this, 'options_validate')
        );
    
        // Configuration Section
        add_settings_section(
            'ar_experience_configuration_main',
            __('Main Settings', 'ar-experience'),
            array($this, 'section_text'),
            'ar_experience_configuration'
        );
    
        add_settings_field(
            'ar_experience_view_in_space',
            __('View in your space', 'ar-experience'),
            array($this, 'view_in_space_input'),
            'ar_experience_configuration',
            'ar_experience_configuration_main'
        );
    
        add_settings_field(
            'ar_experience_ruler_button',
            __('Ruler Button', 'ar-experience'),
            array($this, 'ruler_button_input'),
            'ar_experience_configuration',
            'ar_experience_configuration_main'
        );
    
        // License Section
        add_settings_section(
            'ar_experience_license_main',
            __('License Settings', 'ar-experience'),
            array($this, 'section_text'),
            'ar_experience_license'
        );
    
        add_settings_field(
            'ar_experience_license_key',
            __('License Key', 'ar-experience'),
            array($this, 'license_key_input'),
            'ar_experience_license',
            'ar_experience_license_main'
        );
    }
    
    public function section_text() {
        echo '<p></p>';
    }
    
    public function license_key_input() {
        $options = get_option('ar_experience_options');
        $licenseKey = isset($options['license_key']) ? esc_attr($options['license_key']) : '';
        $license_status = isset($options['license_status']) ? $options['license_status'] : '';
        $tickIcon = $license_status === 'valid' ? '<span style="color: green; margin-left: 5px;">&#10004;</span>' : '';
        $disabled = $license_status === 'valid' ? 'disabled' : '';
    
        echo "<input id='license_key' name='ar_experience_options[license_key]' size='40' type='text' value='{$licenseKey}' $disabled/> $tickIcon";
    }
    
    public function view_in_space_input() {
        $options = get_option('ar_experience_options');
        $view_in_space = isset($options['view_in_space']) ? $options['view_in_space'] : 0;
        $checked = $view_in_space ? 'checked' : '';
        echo "<input id='view_in_space' name='ar_experience_options[view_in_space]' type='checkbox' value='1' $checked />";
    }
    
    public function ruler_button_input() {
        $options = get_option('ar_experience_options');
        $ruler_button = isset($options['ruler_button']) ? $options['ruler_button'] : 0;
        $checked = $ruler_button ? 'checked' : '';
        echo "<input id='ruler_button' name='ar_experience_options[ruler_button]' type='checkbox' value='1' $checked />";
    }
    
    public function options_validate($input) {
        $newinput = array();
    
        if (isset($_POST['deactivate_license'])) {
            $newinput['license_key'] = '';
            $newinput['license_status'] = 'invalid';
            add_settings_error('license_key', 'license-deactivated', __('License deactivated successfully!', 'ar-experience'), 'updated');
        } else {
            $options = get_option('ar_experience_options');
            
            // Chỉ xử lý kích hoạt nếu license chưa kích hoạt
            if (isset($options['license_status']) && $options['license_status'] === 'valid') {
                $newinput['license_key'] = $options['license_key'];
                $newinput['license_status'] = 'valid';
            } else {
                $newinput['license_key'] = sanitize_text_field(isset($input['license_key']) ? $input['license_key'] : '');
    
                if (!empty($newinput['license_key'])) {
                    $site_url = urlencode(parse_url(get_site_url(), PHP_URL_HOST));
                    $activation_url = "http://customer.local/wp-json/lmfwc/v2/licenses/activate/{$newinput['license_key']}?label={$site_url}";
    
                    $response = wp_remote_get($activation_url, array(
                        'headers' => array(
                            'Authorization' => 'Basic ' . base64_encode('ck_0be3d796c18bdde5ce0f6795a69e2107174a0e30:cs_23d94800fa1b0627f6d6b30039ef71d98225a3bb')
                        )
                    ));
    
                    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
                        $body = json_decode(wp_remote_retrieve_body($response), true);
                        if (isset($body['data']['activationData']) && $body['data']['activationData'] != NULL) {
                            $newinput['license_status'] = 'valid';
    
                            // Hiển thị thông báo kích hoạt thành công một lần
                            $license_activated_once = get_option('ar_experience_license_activated_once', false);
    
                            if (!$license_activated_once) {
                                add_settings_error('license_key', 'license-activated', __('License activated successfully!', 'ar-experience'), 'updated');
                                update_option('ar_experience_license_activated_once', true);
                            }
                        } else {
                            $newinput['license_key'] = '';
                            $newinput['license_status'] = 'invalid';
                            update_option('ar_experience_license_activated_once', false); // Cập nhật cờ trạng thái
                            add_settings_error('license_key', 'license-activation-failed', __('Failed to validate license: Invalid response from server', 'ar-experience'), 'error');
                        }
                    } else {
                        $newinput['license_key'] = '';
                        $newinput['license_status'] = 'invalid';
                        update_option('ar_experience_license_activated_once', false); // Cập nhật cờ trạng thái
                        add_settings_error('license_key', 'license-activation-failed', __('Failed to validate license: ', 'ar-experience') . wp_remote_retrieve_response_message($response), 'error');
                    }
                } else {
                    $newinput['license_status'] = 'invalid';
                }
            }
        }
    
        // Sao chép các tùy chọn khác
        $newinput['view_in_space'] = isset($input['view_in_space']) ? 1 : 0;
        $newinput['ruler_button'] = isset($input['ruler_button']) ? 1 : 0;
    
        return $newinput;
    }
    
    
    
    public function validate_license() {
        $options = get_option('ar_experience_options');
        if (isset($options['license_key']) && !empty($options['license_key'])) {
            $license_code = $options['license_key'];
            $validate_url = "http://customer.local/wp-json/lmfwc/v2/licenses/validate/{$license_code}";

            $response = wp_remote_get($validate_url, array(
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode('ck_0be3d796c18bdde5ce0f6795a69e2107174a0e30:cs_23d94800fa1b0627f6d6b30039ef71d98225a3bb')
                )
            ));
            $body = json_decode($response['body'], true);

            if (is_wp_error($response) || !isset($body['data']) || ($body['data']['activationData'] == NULL) || wp_remote_retrieve_response_code($response) != 200) {
                update_option('ar_experience_options', array('license_status' => 'invalid'));
                add_settings_error('license_key', 'license-validation-error', __('Failed to validate license: ', 'ar-experience') . wp_remote_retrieve_response_message($response), 'error');
            }
        }
    }

    public function validate_license_on_admin_init() {
        $this->validate_license();
    }

    public function add_ar_model_meta_box() {
        add_meta_box(
            'ar_model_meta_box',
            'AR Model Display',
            array($this, 'render_ar_model_meta_box'),
            'product',
            'side',
            'default'
        );
    }

    public function render_ar_model_meta_box($post) {
        $model_file = get_post_meta($post->ID, 'ar_model_file', true);
        ?>
        <label for="ar_model_file">Select single Model:</label>
        <div>
            <td>
                <tr>
                    <p>
                        <input type="text" id="ar_model_file" name="ar_model_file"
                            value="<?php echo esc_attr(basename($model_file)); ?>" readonly>
                    </p>
                    <button type="button" class="button button-primary" id="ar_model_file_button">Select Model</button>
                    <?php if ($model_file): ?>
                        <button type="button" class="button" id="ar_model_file_remove_button">Remove Model</button>
                    <?php endif; ?>
                    <p><a href="https://realitech.dev/pro" target="_blank"><strong>Buy Pro to unlock Unlimited Features</strong></a></p>
                </tr>
            </td>
        </div>
        <p><?php echo $model_file ? '' : 'No model file selected.'; ?></p>
        <script>
            jQuery(document).ready(function ($) {
                $('#ar_model_file_button').on('click', function (e) {
                    e.preventDefault();
                    var frame = wp.media({
                        title: 'Select Model',
                        button: { text: 'Use this file' },
                        multiple: false,
                        library: { type: ['model/gltf-binary', 'application/octet-stream', 'model/vnd.usdz+zip', 'application/octet-stream'] }
                    });
                    frame.on('select', function () {
                        var attachment = frame.state().get('selection').first().toJSON();
                        $('#ar_model_file').val(attachment.url);
                    });
                    frame.open();
                });
                $('#ar_model_file_remove_button').on('click', function (e) {
                    e.preventDefault();
                    $('#ar_model_file').val('');
                });
                $('#post').submit(function () {
                    var arModelPath = $('#ar_model_file').val();
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'update_ar_model_path',
                            post_id: $('#post_ID').val(),
                            ar_model_path: arModelPath
                        },
                        success: function (response) {
                            console.log(response);
                        }
                    });
                });
            });
        </script>
        <?php
    }

    public function save_ar_model_meta_box($post_id) {
        if (isset($_POST['ar_model_file'])) {
            $model_file = $_POST['ar_model_file'];
            update_post_meta($post_id, 'ar_model_file', $model_file);
            global $wpdb;
            $lookup_table = $wpdb->prefix . 'wc_product_meta_lookup';
            $wpdb->update($lookup_table, array('ar_model_path' => $model_file), array('product_id' => $post_id));
        }
    }

    public function remove_ar_model_meta_box($post_id) {
        delete_post_meta($post_id, 'ar_model_file');
        global $wpdb;
        $lookup_table = $wpdb->prefix . 'wc_product_meta_lookup';
        $wpdb->update($lookup_table, array('ar_model_path' => ''), array('product_id' => $post_id), array('%s'), array('%d'));
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ar-experience-admin.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ar-experience-admin.js', array('jquery'), $this->version, false);
    }
}
