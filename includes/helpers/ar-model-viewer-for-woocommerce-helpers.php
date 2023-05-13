<?php
// Allow to upload .glb and .usdz files
function allow_ar_upload_mimes( $mimes ) {
    $mimes['glb'] = 'model/gltf-binary';
    $mimes['usdz'] = 'model/vnd.usdz+zip';
    return $mimes;
}

// Add filter to check file type
add_filter( 'wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {
    $filetype = wp_check_filetype( $filename, $mimes );

    return [
        'ext'             => $filetype['ext'],
        'type'            => $filetype['type'],
        'proper_filename' => $data['proper_filename']
    ];
}, 10, 4 );

add_filter( 'upload_mimes', 'allow_ar_upload_mimes' );
