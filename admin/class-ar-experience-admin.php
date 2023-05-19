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
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/ar-experience-core.php';
		add_action('add_meta_boxes', array($this, 'add_ar_model_meta_box'));
		add_action('woocommerce_process_product_meta', array($this, 'save_ar_model_meta_box'));
	}

	public function add_ar_model_meta_box()
	{
		add_meta_box(
			'ar_model_meta_box',
			'AR Model Details',
			array($this, 'render_ar_model_meta_box'),
			'product',
			'side',
			'default'
		);
	}

	public function render_ar_model_meta_box($post)
	{
		// Retrieve the existing model file URL if it exists
		$model_file = get_post_meta($post->ID, 'ar_model_file', true);

		// Output the HTML for the meta box content
		?>
		<label for="ar_model_file">Select Model:</label>
		<div>
			<input type="text" id="ar_model_file" name="ar_model_file" value="<?php echo esc_attr($model_file); ?>" readonly>
			<button type="button" class="button button-primary" id="ar_model_file_button">Select Model</button>
		</div>
		<p>
			<?php if ($model_file): ?>
				<a href="<?php echo esc_url($model_file); ?>" target="_blank">View Model</a>
			<?php else: ?>
				No model file selected.
			<?php endif; ?>
		</p>

		<script>
			jQuery(document).ready(function ($) {
				// Handle file selection from media library
				$('#ar_model_file_button').on('click', function (e) {
					e.preventDefault();

					// Create a new media frame
					var frame = wp.media({
						title: 'Select Model',
						button: {
							text: 'Use this file'
						},
						multiple: false,  // Set to true if you want to allow multiple file selection
						library: {
							type: ['model/gltf-binary', 'application/octet-stream']  // Restrict to .glb and .usdz file formats
						}
					});

					// Handle file selection event
					frame.on('select', function () {
						var attachment = frame.state().get('selection').first().toJSON();
						$('#ar_model_file').val(attachment.url);
					});

					// Open the media frame
					frame.open();
				});
			});
		</script>
		<?php
	}

	public function save_ar_model_meta_box($post_id)
	{
		if (isset($_POST['ar_model_file'])) {
			$model_file = $_POST['ar_model_file'];
			update_post_meta($post_id, 'ar_model_file', $model_file);

			// Save the model file path to wc_product_meta_lookup table
			global $wpdb;
			$lookup_table = $wpdb->prefix . 'wc_product_meta_lookup';
			$wpdb->update(
				$lookup_table,
				array('ar_model_path' => $model_file),
				array('product_id' => $post_id)
			);
		}
	}


	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ar_Experience_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ar_Experience_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ar-experience-admin.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ar_Experience_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ar_Experience_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ar-experience-admin.js', array('jquery'), $this->version, false);

	}

}