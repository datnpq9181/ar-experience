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
class Ar_Experience_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/ar-experience-core.php';
        add_action('add_meta_boxes', array($this, 'add_ar_model_meta_box'));
        add_action('woocommerce_process_product_meta', array($this, 'save_ar_model_meta_box'));
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        add_action('admin_init', array($this, 'settings_init'));
        add_action('admin_init', array($this, 'validate_license_on_admin_init'));
        add_action('wp_ajax_validate_license_ajax', array($this, 'validate_license_ajax'));
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            __('AR Experience Settings', 'ar-experience'),
            __('AR Experience', 'ar-experience'),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_admin_page'),
            'dashicons-admin-generic',
            20
        );
    }

    public function display_plugin_admin_page() {
        $this->validate_license();
        $options = get_option('ar_experience_options');
        $license_status = isset($options['license_status']) ? $options['license_status'] : '';
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('AR Experience Settings', 'ar-experience'); ?></h1>
            <div id="license_message"></div>
            <form action="options.php" method="post">
                <?php
                settings_errors();
                settings_fields('ar_experience_options_group');
                do_settings_sections('ar_experience');
                submit_button(__('Activate', 'ar-experience'), 'primary', 'activate_license', false);
                ?>
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

        add_settings_section(
            'ar_experience_main',
            __('Main Settings', 'ar-experience'),
            array($this, 'section_text'),
            'ar_experience'
        );

        add_settings_field(
            'ar_experience_license_key',
            __('License Key', 'ar-experience'),
            array($this, 'license_key_input'),
            'ar_experience',
            'ar_experience_main'
        );
    }

    public function section_text() {
        echo '<p>' . esc_html__('Enter your settings below:', 'ar-experience') . '</p>';
    }

    public function license_key_input() {
        $options = get_option('ar_experience_options');
        $licenseKey = isset($options['license_key']) ? esc_attr($options['license_key']) : '';
        $license_status = isset($options['license_status']) ? $options['license_status'] : '';
        $tickIcon = $license_status === 'valid' ? '<span style="color: green; margin-left: 5px;">&#10004;</span>' : '';
        
        echo "<input id='license_key' name='ar_experience_options[license_key]' size='40' type='text' value='{$licenseKey}'/> $tickIcon";
    }

    public function options_validate($input) {
        $newinput = array();

        $newinput['license_key'] = sanitize_text_field(isset($input['license_key']) ? $input['license_key'] : '');

        if (!preg_match('/^[A-Z0-9-]{19}$/i', $newinput['license_key'])) {
            add_settings_error('license_key', 'invalid-license', __('Invalid license key, please contact us or buy a new one at https://wedev.mobi', 'ar-experience'), 'error');
            return $input;
        } else {
            $site_url = urlencode(parse_url(get_site_url(), PHP_URL_HOST));
            $activation_url = "http://customer.local/wp-json/lmfwc/v2/licenses/activate/{$newinput['license_key']}?label={$site_url}";

            $response = wp_remote_get($activation_url, array(
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode('ck_0be3d796c18bdde5ce0f6795a69e2107174a0e30:cs_23d94800fa1b0627f6d6b30039ef71d98225a3bb')
                )
            ));

            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
                add_settings_error('license_key', 'api-error', __('Failed to activate license: ', 'ar-experience') . wp_remote_retrieve_response_message($response), 'error');
                $newinput['license_key'] = '';
            } else {
                $newinput['license_status'] = 'valid';
                add_settings_error('license_key', 'license-activated', __('License activated successfully!', 'ar-experience'), 'updated');
                update_option('ar_experience_license_activated', true);
            }
        }

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

    public function validate_license_ajax() {
        if (!isset($_POST['license_key'])) {
            wp_send_json_error(array('message' => __('License key is missing.', 'ar-experience')));
        }

        $license_key = sanitize_text_field($_POST['license_key']);
        $site_url = urlencode(parse_url(get_site_url(), PHP_URL_HOST));
        $activation_url = "http://customer.local/wp-json/lmfwc/v2/licenses/activate/{$license_key}?label={$site_url}";

        $response = wp_remote_get($activation_url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode('ck_0be3d796c18bdde5ce0f6795a69e2107174a0e30:cs_23d94800fa1b0627f6d6b30039ef71d98225a3bb')
            )
        ));

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
            wp_send_json_error(array('message' => __('Failed to activate license: ', 'ar-experience') . wp_remote_retrieve_response_message($response)));
        }

        $body = json_decode($response['body'], true);

        if (!isset($body['data']) || $body['data']['activationData'] == NULL) {
            wp_send_json_error(array('message' => __('Invalid license key.', 'ar-experience')));
        }

        update_option('ar_experience_license_activated', true);
        update_option('ar_experience_options', array('license_key' => $license_key, 'license_status' => 'valid'));
        wp_send_json_success(array('message' => __('License activated successfully!', 'ar-experience')));
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
