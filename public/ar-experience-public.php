<?php
// Add custom content to single product page
add_action( 'woocommerce_single_product_summary', 'add_ar_content', 5 );

function add_ar_content() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'wc_product_meta_lookup';
    $model_src = $wpdb->get_var( "SELECT ar_model_path FROM $table_name WHERE product_id = " . get_the_ID() );
    $poster_url = 'https://upload.wikimedia.org/wikipedia/commons/b/b9/Youtube_loading_symbol_1_(wobbly).gif';
    
        echo '
        <div class="ar-model-viewer-container">
            <model-viewer id="superAR" alt="" src="' . $model_src . '" ar="webxr scene-viewer quick-look fallback" poster="' . $poster_url . '" shadow-intensity="1" camera-controls="" touch-action="pan-y" data-js-focus-visible="" ar-status="not-presenting" loading="auto" reveal="auto" style="width:100%; height:400px; position:relative; top:0; margin:auto;">
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
            </model-viewer>
        </div>';
    
    // echo '$model_src: ' . $model_src . '<br>';
    // echo '$poster_url: ' . $poster_url . '<br>';
    
}


