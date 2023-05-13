<?php
include plugin_dir_path(__FILE__) . 'ar-model-viewer-for-woocommerce-admin-public.php';

function ar_product_meta_box_callback( $post ) {
    // Enqueue the WordPress media uploader scripts.
    wp_enqueue_media(); 

    // Get existing AR model path if it exists.
    $ar_model_path = get_post_meta( $post->ID, '_ar_model_path', true );
    ?>

    <div class="options_group">
        <p>
            <a href="#" class="add_ar_model_path_button button"><?php esc_html_e( 'Select AR Model', 'woocommerce' ); ?></a>
        </p>
        <div class="selected_ar_model_path" <?php echo $ar_model_path ? '' : 'style="display:none;"'; ?>>
            <?php 
                if ($ar_model_path) {
                    $filename = basename($ar_model_path);
                    echo esc_html($filename);
                } 
            ?>
        </div>
        <input type="hidden" id="_ar_model_path" name="_ar_model_path" value="<?php echo esc_attr( $ar_model_path ); ?>">
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var file_frame;

            // Run the media uploader script when the add_ar_model_path_button button is clicked.
            $('.add_ar_model_path_button').on('click', function(event) {
                event.preventDefault();

                if ( file_frame ) {
                    file_frame.open();
                    return;
                }

                // Create the media uploader frame.
                file_frame = wp.media.frames.file_frame = wp.media({
                    title: '<?php esc_html_e( 'Select or Upload AR Model', 'woocommerce' ); ?>',
                    button: {
                        text: '<?php esc_html_e( 'Use this file', 'woocommerce' ); ?>'
                    },
                    multiple: false
                });

                // Run the callback function when an AR model is selected.
                file_frame.on('select', function() {
                    var attachment = file_frame.state().get('selection').first().toJSON();

                    // Display the selected AR model filename.
                    var filename = attachment.filename ? attachment.filename : attachment.url.substr(attachment.url.lastIndexOf('/') + 1);
                    $('.selected_ar_model_path').text(filename).show();

                    // Set the value of the _ar_model_path field to the selected AR model path.
                    $('#_ar_model_path').val(attachment.url);

                    // Get the selected AR model path.
var ar_model_path = attachment.url;

// Update the hidden input field with the new AR model path.
$('#_ar_model_path').val(ar_model_path);

// Update the existing AR model path variable.
$ar_model_path = ar_model_path;

// Update the displayed filename.
$('.selected_ar_model_path').text(attachment.filename).show();

// Save the new AR model path as post meta.
$.ajax({
    url: ajaxurl,
    type: 'POST',
    data: {
        action: 'save_ar_model_path',
        post_id: <?php echo $post->ID; ?>,
        ar_model_path: ar_model_path
    },
    success: function(response) {
        console.log(response);
    },
    error: function(error) {
        console.log(error);
    }
});
                });

                // Open the media uploader frame.
                file_frame.open();
            });
        });
    </script>
    <?php
}

add_action( 'wp_ajax_save_ar_model_path', 'save_ar_model_path' );
function save_ar_model_path() {
    $post_id = $_POST['post_id'];
    $ar_model_path = $_POST['ar_model_path'];
    update_post_meta( $post_id, '_ar_model_path', $ar_model_path );
    wp_die();
}

// Add AR product view options meta box
add_action( 'add_meta_boxes', 'ar_product_meta_box' );
function ar_product_meta_box() {
    add_meta_box(
        'ar_product_meta_box',
        __( 'AR Product View', 'woocommerce' ),
        'ar_product_meta_box_callback',
        'product',
        'side',
        'default'
    );
}

// Save the AR Product option value and model path
function save_ar_product_option( $post_id ) {
    if ( isset( $_POST['_ar_product'] ) ) {
        $ar_product = 'yes';
    } else {
        $ar_product = 'no';
    }

    $ar_model_path = isset( $_POST['_ar_model_path'] ) ? $_POST['_ar_model_path'] : '';

    if ( ! empty( $ar_model_path ) ) {
        $ar_product = 'yes';
    }

    update_post_meta( $post_id, '_ar_product', $ar_product );

    global $wpdb;
    $table_name = $wpdb->prefix . 'wc_product_meta_lookup';

    if ( 'yes' === $ar_product ) {
        $wpdb->update( $table_name, array( 'ar_product' => 'yes', 'ar_model_path' => $ar_model_path ), array( 'product_id' => $post_id ) );
    } else {
        $wpdb->update( $table_name, array( 'ar_product' => 'no', 'ar_model_path' => NULL ), array( 'product_id' => $post_id ) );
    }
}

add_action( 'woocommerce_process_product_meta', 'save_ar_product_option' );
