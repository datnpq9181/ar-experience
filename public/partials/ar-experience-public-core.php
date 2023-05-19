<?php

function is_ar_model_path_exists($product_id)
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'wc_product_meta_lookup';
  $ar_model_path = $wpdb->get_var($wpdb->prepare("SELECT ar_model_path FROM $table_name WHERE product_id = %d", $product_id));

  return $ar_model_path !== null;
}

function hide_product_gallery_if_model_exists()
{
  if (is_ar_model_path_exists(get_the_ID())) {
    echo '<style>.large-12.columns.mobile-padding-left-0.mobile-padding-right-0.nasa-flex.align-start { display: none; }</style>';
  }
}

add_action('woocommerce_before_single_product_summary', 'hide_product_gallery_if_model_exists');

function display_ar_model_viewer()
{
  global $wpdb;

  $table_name = $wpdb->prefix . 'wc_product_meta_lookup';
  $product_id = get_the_ID();
  $model_src = $wpdb->get_var($wpdb->prepare("SELECT ar_model_path FROM $table_name WHERE product_id = %d", $product_id));

  if ($model_src) {
    echo '<script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@latest"></script>
            <div class="ar-model-viewer-container">
            <button class="ar-button">View in your space</button>
                <model-viewer id="superAR" alt="" environment-image="neutral" src="' . esc_url($model_src) . '" ar="webxr scene-viewer quick-look fallback" poster="" shadow-intensity="1.5" camera-controls="" touch-action="pan-y" data-js-focus-visible="" ar-status="not-presenting" loading="auto" reveal="auto" style="width:100%; height:600px; position:relative; top:0; margin:auto;">
                <button slot="hotspot-dot+X-Y+Z" class="dot" data-position="1 -1 1" data-normal="1 0 0"></button>
                <button slot="hotspot-dim+X-Y" class="dim" data-position="1 -1 0" data-normal="1 0 0"></button>
                <button slot="hotspot-dot+X-Y-Z" class="dot" data-position="1 -1 -1" data-normal="1 0 0"></button>
                <button slot="hotspot-dim+X-Z" class="dim" data-position="1 0 -1" data-normal="1 0 0"></button>
                <button slot="hotspot-dot+X+Y-Z" class="dot" data-position="1 1 -1" data-normal="0 1 0"></button>
                <button slot="hotspot-dim+Y-Z" class="dim" data-position="0 -1 -1" data-normal="0 1 0"></button>
                <button slot="hotspot-dot-X+Y-Z" class="dot" data-position="-1 1 -1" data-normal="0 1 0"></button>
                <button slot="hotspot-dim-X-Z" class="dim" data-position="-1 0 -1" data-normal="-1 0 0"></button>
                <button slot="hotspot-dot-X-Y-Z" class="dot" data-position="-1 -1 -1" data-normal="-1 0 0"></button>
                <button slot="hotspot-dim-X-Y" class="dim" data-position="-1 -1 0" data-normal="-1 0 0"></button>
                <button slot="hotspot-dot-X-Y+Z" class="dot" data-position="-1 -1 1" data-normal="-1 0 0"></button>
                <svg id="dimLines" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" class="dimensionLineContainer">
                    <line class="dimensionLine"></line>
                    <line class="dimensionLine"></line>
                    <line class="dimensionLine"></line>
                    <line class="dimensionLine"></line>
                    <line class="dimensionLine"></line>
                </svg>
                <button id="myButton"><img src=' . plugins_url( "/img/ruler.png", __FILE__ ) . '></button>
                <div class="progress-bar hide" slot="progress-bar">
                  <div class="update-bar"></div>
                </div>
                <div id="ar-prompt">
                  <img src=' . plugins_url( "/img/ar_icon.png", __FILE__ ) . '>
                </div>
                <div class="popup" id="popup">
                <button class="close-button" onclick="closePopup()">×</button>
                <p>Scan this QR code on your mobile device to view in your space:</p>
                <img id="qr-code-img" alt="QR Code">
                </div>
                </model-viewer>
            </div>';
  }
}

add_action('woocommerce_before_single_product_summary', 'display_ar_model_viewer', 5);