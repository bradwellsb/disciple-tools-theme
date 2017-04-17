<?php

/**
 * Setup JavaScript
 */
add_action( 'wp_enqueue_scripts', function() {

    //load script
    wp_enqueue_script( 'dt-post-submitter', get_stylesheet_directory_uri( ) . '/assets/js/post-submitter.js', array( 'jquery' ) );

    //localize data for script
    wp_localize_script( 'dt-post-submitter', 'POST_SUBMITTER', array(
            'root' => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'success' => __( 'Thanks for your submission!', 'your-text-domain' ),
            'failure' => __( 'Your submission could not be processed.', 'your-text-domain' ),
            'current_user_id' => get_current_user_id()
        )
    );

});