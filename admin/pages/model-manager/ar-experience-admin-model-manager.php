<?php
// Callback function for the Model Manager page
function model_manager_page()
{
    ?>
    <div class="wrap">
        <h1>Model Manager</h1>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>File Type</th>
                    <th>File URL</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Get all attachments of type 'model/gltf-binary' or 'model/vnd.usdz+zip'
                $args = array(
                    'post_type' => 'attachment',
                    'post_mime_type' => array('model/gltf-binary', 'model/vnd.usdz+zip'),
                    'posts_per_page' => -1,
                );
                $attachments = get_posts($args);

                // Loop through each attachment and display if the file exists
                foreach ($attachments as $attachment) {
                    $file_url = wp_get_attachment_url($attachment->ID);
                    if (file_exists(get_attached_file($attachment->ID))) {
                        $file_type = get_post_mime_type($attachment->ID);
                        $extension = pathinfo($file_url, PATHINFO_EXTENSION);
                        ?>
                        <tr>
                            <td><?php echo $attachment->post_title; ?></td>
                            <td><?php echo $file_type; ?></td>
                            <td><a href="<?php echo $file_url; ?>" target="_blank"><?php echo $file_url; ?></a></td>
                            <td>
                                <?php if ($extension == 'glb') { ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="attachment_id" value="<?php echo $attachment->ID; ?>">
                                        <?php wp_nonce_field( 'remove_attachment_' . $attachment->ID, 'remove_attachment_nonce' ); ?>
                                        <button type="submit" class="button" onClick="return confirm('Are you sure you want to remove this model?')">Remove</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Handle form submission to remove attachment
add_action( 'init', 'remove_attachment' );
function remove_attachment() {
    if ( isset( $_POST['attachment_id'] ) && isset( $_POST['remove_attachment_nonce'] ) && wp_verify_nonce( $_POST['remove_attachment_nonce'], 'remove_attachment_' . $_POST['attachment_id'] ) ) {
        wp_delete_attachment( $_POST['attachment_id'] );
    }
}
