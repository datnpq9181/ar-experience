<?php
// Callback function for the License page
function license_page()
{
    // Your code for the License page
    if (isset($_POST['license_key'])) {
        // Handle the form submission and activate the license here
        $license_key = sanitize_text_field($_POST['license_key']);
        // Code to activate the license goes here
        echo '<p>License key "' . $license_key . '" activated successfully.</p>';

    } else {
        // Display the license input form
        echo '<h1>License content</h1>';
        echo '<form method="post">';
        echo '<table>';
        echo '<tr><th><label for="license_key">License key:</label></th><td><input type="text" name="license_key" id="license_key"></td></tr>';
        echo '<tr><td colspan="2"><button type="submit" name="activate_license">Activate License</button></td></tr>';
        echo '</table>';
        echo '</form>';
    }
}
