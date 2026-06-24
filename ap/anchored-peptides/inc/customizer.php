<?php
/**
 * Anchored Peptides — Customizer settings.
 * @package AnchoredPeptides
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'customize_register', function ( $wp_customize ) {
    $wp_customize->add_section( 'ap_general', array(
        'title'    => __( 'Anchored Peptides — General', 'anchored-peptides' ),
        'priority' => 30,
    ) );

    // Announcement marquee toggle + text.
    $wp_customize->add_setting( 'ap_announce_enabled', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean' ) );
    $wp_customize->add_control( 'ap_announce_enabled', array( 'label' => __( 'Show announcement marquee', 'anchored-peptides' ), 'section' => 'ap_general', 'type' => 'checkbox' ) );

    $wp_customize->add_setting( 'ap_marquee', array(
        'default'           => "Batch before 2 PM\nTrusted by 20,000+ researchers\nFor research use only\nShips from Canada\nThird-party HPLC tested",
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'ap_marquee', array( 'label' => __( 'Marquee phrases (one per line)', 'anchored-peptides' ), 'section' => 'ap_general', 'type' => 'textarea' ) );

    // Default disclaimer.
    $wp_customize->add_setting( 'ap_disclaimer', array(
        'default'           => __( 'For research use only. Not for human or veterinary use. Not a drug, food, or cosmetic. Not for diagnostic or therapeutic use.', 'anchored-peptides' ),
        'sanitize_callback' => 'wp_kses_post',
    ) );
    $wp_customize->add_control( 'ap_disclaimer', array( 'label' => __( 'Default disclaimer (overridable per product)', 'anchored-peptides' ), 'section' => 'ap_general', 'type' => 'textarea' ) );

    // Default spec pills (3rd pill on products).
    $wp_customize->add_setting( 'ap_spec_pill_3', array( 'default' => __( 'Ships from Canada', 'anchored-peptides' ), 'sanitize_callback' => 'sanitize_text_field' ) );
    $wp_customize->add_control( 'ap_spec_pill_3', array( 'label' => __( 'Default 3rd spec pill (overridable per product)', 'anchored-peptides' ), 'section' => 'ap_general', 'type' => 'text' ) );

    // Footer blurb.
    $wp_customize->add_setting( 'ap_footer_blurb', array(
        'default'           => __( 'Canada’s source for third-party HPLC-tested research peptides. Shipped same-day from our Canadian fulfillment centre. For laboratory & research use only. Not for human consumption.', 'anchored-peptides' ),
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'ap_footer_blurb', array( 'label' => __( 'Footer blurb', 'anchored-peptides' ), 'section' => 'ap_general', 'type' => 'textarea' ) );
} );
