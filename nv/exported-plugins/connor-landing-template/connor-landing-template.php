<?php
/**
 * Plugin Name: Connor Landing Template
 * Plugin URI:  https://example.com/
 * Description: Adds a "Connor 90-Day Landing" page template you can assign to any Page from the Page Attributes panel. Renders a standalone full-page landing — bypasses the active theme's header/footer.
 * Version:     2.5.0
 * Author:      Natty Vision
 * License:     GPL-2.0-or-later
 * Text Domain: connor-landing-template
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Connor_Landing_Template {

    const TEMPLATE_FILE = 'connor-landing.php';
    const TEMPLATE_NAME = 'Connor 90-Day Landing';

    public function __construct() {
        // Add the template to the Page Attributes dropdown.
        add_filter( 'theme_page_templates', array( $this, 'add_template_to_dropdown' ) );

        // Tell WordPress where to load the template file from when selected.
        add_filter( 'template_include', array( $this, 'load_template' ) );

        // Make sure WP recognizes the template as valid for the page.
        add_filter( 'page_template', array( $this, 'override_page_template' ) );
    }

    /**
     * Adds our template name to the Page Attributes → Template dropdown.
     */
    public function add_template_to_dropdown( $templates ) {
        $templates[ self::TEMPLATE_FILE ] = self::TEMPLATE_NAME;
        return $templates;
    }

    /**
     * Intercepts template loading. If the page has our template assigned,
     * load the file from this plugin instead of looking in the theme.
     */
    public function load_template( $template ) {
        if ( ! is_page() ) {
            return $template;
        }

        $assigned = get_post_meta( get_queried_object_id(), '_wp_page_template', true );

        if ( $assigned === self::TEMPLATE_FILE ) {
            $plugin_template = plugin_dir_path( __FILE__ ) . 'templates/' . self::TEMPLATE_FILE;
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }

        return $template;
    }

    /**
     * Older filter for the same purpose — keeps things working across WP versions
     * and when other plugins/themes intercept page_template specifically.
     */
    public function override_page_template( $template ) {
        if ( ! is_page() ) {
            return $template;
        }

        $assigned = get_post_meta( get_queried_object_id(), '_wp_page_template', true );

        if ( $assigned === self::TEMPLATE_FILE ) {
            $plugin_template = plugin_dir_path( __FILE__ ) . 'templates/' . self::TEMPLATE_FILE;
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }

        return $template;
    }
}

new Connor_Landing_Template();
