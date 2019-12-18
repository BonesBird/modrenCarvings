<?php
/**
 * fordummies functions and definitions
 *
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since fordummies 1.0
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'fordummies_cfg_parent_css' ) ):
    function fordummies_cfg_parent_css() {
        wp_enqueue_style( 'fordummies_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'genericons' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'fordummies_cfg_parent_css', 10 );
