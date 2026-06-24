<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'customize_register', function ( $wp_customize ) {
    $wp_customize->add_section( 'natty_general', array(
        'title'    => __( 'Natty Vision — General', 'natty-vision' ),
        'priority' => 30,
    ) );

    // Announce bar
    $wp_customize->add_setting( 'nv_announce_enabled', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean' ) );
    $wp_customize->add_control( 'nv_announce_enabled', array( 'label' => __( 'Show announcement bar', 'natty-vision' ), 'section' => 'natty_general', 'type' => 'checkbox' ) );

    $wp_customize->add_setting( 'nv_announce_html', array( 'default' => '<strong>20% off</strong> select orders of $500+ <a href="#">Terms apply</a>', 'sanitize_callback' => 'wp_kses_post' ) );
    $wp_customize->add_control( 'nv_announce_html', array( 'label' => __( 'Announcement HTML', 'natty-vision' ), 'section' => 'natty_general', 'type' => 'textarea' ) );

    // Default disclaimer
    $wp_customize->add_setting( 'nv_disclaimer', array(
        'default'           => __( 'For research use only. Not for human or veterinary use. Not a drug, food, or cosmetic. Not for diagnostic or therapeutic use.', 'natty-vision' ),
        'sanitize_callback' => 'wp_kses_post',
    ) );
    $wp_customize->add_control( 'nv_disclaimer', array( 'label' => __( 'Default disclaimer (overridable per product)', 'natty-vision' ), 'section' => 'natty_general', 'type' => 'textarea' ) );

    // Consistent feature / trust line — shown as the last spec bullet on every product.
    $wp_customize->add_setting( 'nv_spec_4', array(
        'default'           => __( 'Third-party tested · cold-chain shipping', 'natty-vision' ),
        'sanitize_callback' => 'wp_kses_post',
    ) );
    $wp_customize->add_control( 'nv_spec_4', array( 'label' => __( 'Consistent feature line (last spec bullet, overridable per product)', 'natty-vision' ), 'section' => 'natty_general', 'type' => 'text' ) );
} );
