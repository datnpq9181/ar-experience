<?php

global $token;

// Validate token every time use plugin
function validate_token($token) {
    $url = "https://realitech.dev/wp-json/dlm/v1/licenses/validate/$token";
    $consumer_key = 'ck_d3c9e56085e071ccfae821622dcb1854cb3252cb';
    $consumer_secret = 'cs_1af560c3e840a21015938cf170ffc3a4943afad9';

    $response = wp_remote_get($url, [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode("$consumer_key:$consumer_secret")
        ]
    ]);

    if (is_wp_error($response) || $response['response']['code'] !== 200) {
        return false;
        echo '<div class="notice notice-error is-dismissible"><p><strong>' . esc_html__('Validated token failed.') . '</strong></p></div>';
    } else {
        return true;
        echo '<div class="notice notice-success is-dismissible"><p><strong>' . esc_html__('Validated token successfully') . '</strong></p></div>';
    }
}

// Callback function for the License page
function license_page()
{
    // Output the License page HTML
    ?>
    <div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <p><?php echo esc_html__('Enter your license key below to activate your plugin and receive updates and support.', 'my-plugin'); ?></p>

    <?php 
if (isset($_POST['license_key'])) {
    // Activate the license key via API
    $site_url = parse_url(get_site_url(), PHP_URL_HOST);
    $license_key = $_POST['license_key'];
    $consumer_key = 'ck_d3c9e56085e071ccfae821622dcb1854cb3252cb';
    $consumer_secret = 'cs_1af560c3e840a21015938cf170ffc3a4943afad9';
    $label = urlencode($site_url);
    
    // Make the request to activate the license key
    $url = "https://realitech.dev/wp-json/dlm/v1/licenses/activate/$license_key";
    $response = wp_remote_get($url, [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode("$consumer_key:$consumer_secret")
        ],
        'body' => [
            'label' => $label
        ]
    ]);
    

    // Check if the request was successful
    if (is_wp_error($response) || $response['response']['code'] !== 200) {
        $error_msg = esc_html__('License key activation failed.', 'my-plugin');
        $error_msg .= ' ' . esc_html__('Please try again or contact support.', 'my-plugin');
        echo '<div class="notice notice-error is-dismissible"><p><strong>' . $error_msg . '</strong></p></div>';
    } else {
        $token = json_decode($response['body'])->data->token;
        $success_msg = esc_html__('License key activated successfully:', 'my-plugin');
        $success_msg .= ' ' . esc_html($license_key);
        echo '<div class="notice notice-success is-dismissible"><p><strong>' . $success_msg . '</strong></p></div>';
        echo '<div class="notice notice-success is-dismissible"><p><strong>' . $token . '</strong></p></div>';
    }
}
?>


    <form method="post">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="license_key"><?php echo esc_html__('License key:', 'my-plugin'); ?></label></th>
                <td><input type="text" name="license_key" id="license_key" class="regular-text"></td>
            </tr>
        </table>
        <?php submit_button(esc_html__('Activate License', 'my-plugin'), 'primary', 'activate_license'); ?>
    </form>

    <hr>

    <h2><?php echo esc_html__('Need a license key?', 'my-plugin'); ?></h2>
    <p><?php echo esc_html__('Visit the plugin website to purchase a license key and gain access to updates and support.', 'my-plugin'); ?></p>
    <a href="https://realitech.dev/" class="button button-primary"><?php echo esc_html__('Purchase License', 'my-plugin'); ?></a>

</div>
    <?php
}


// function my_alert_message() {
//     echo '<div class="notice notice-info"><p>Token value: ' . esc_html( $success_msg) . '</p></div>';
// }
// add_action( 'admin_notices', 'my_alert_message' );
