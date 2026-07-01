<?php
/**
 * Anchored Peptides — Brand layer (data-driven per-site branding).
 *
 * Every generated customer site is a SEPARATE WordPress install, so the theme
 * code stays byte-identical across brands — only *values* change. This file is
 * the single seam that turns hardcoded palette / fonts / copy into WordPress
 * options so a site can be reskinned entirely from the database (set by the
 * Provisioner plugin at deploy time), with the shipped defaults preserved.
 *
 *   - Palette : option `ap_brand_tokens`  (JSON map of `--ap-*` => value)
 *   - Fonts   : option `ap_fonts_url`     (Google Fonts URL) + the serif/sans
 *               family names carried inside `ap_brand_tokens`
 *   - Copy    : options `ap_copy_<key>`   (read via ap_copy())
 *   - Hero img: option `ap_hero_image_id`  (see template.php)
 *
 * @package AnchoredPeptides
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Print a `:root{}` override block from the `ap_brand_tokens` option.
 *
 * tokens.css remains the baseline; this only emits the keys a site overrides.
 * Hooked late on wp_head so it lands AFTER the enqueued tokens.css link and
 * therefore wins on source order (both target :root, equal specificity).
 */
function ap_brand_tokens_css() {
	$raw = get_option( 'ap_brand_tokens' );
	if ( empty( $raw ) ) return;

	$tokens = is_array( $raw ) ? $raw : json_decode( (string) $raw, true );
	if ( ! is_array( $tokens ) || ! $tokens ) return;

	$decls = '';
	foreach ( $tokens as $prop => $value ) {
		// Only accept our own custom properties, e.g. --ap-olive, --ap-serif.
		if ( ! is_string( $prop ) || ! preg_match( '/^--ap-[a-z0-9\-]+$/', $prop ) ) continue;
		$safe = ap_sanitize_css_value( (string) $value );
		if ( '' === $safe ) continue;
		$decls .= $prop . ':' . $safe . ';';
	}
	if ( '' === $decls ) return;

	echo "\n<style id=\"ap-brand-tokens\">:root{" . $decls . "}</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- values sanitized above
}
add_action( 'wp_head', 'ap_brand_tokens_css', 200 );

/**
 * Sanitize a single CSS custom-property value.
 *
 * Values are author-controlled at deploy time (colors, px sizes, font stacks
 * like `'Newsreader', Georgia, serif`) but never trusted: strip anything that
 * could break out of the declaration or inject markup / another rule.
 */
function ap_sanitize_css_value( $value ) {
	$value = trim( $value );
	// Kill declaration/rule/markup break-outs and CSS comments.
	$value = str_replace( array( '<', '>', '{', '}', ';', '\\', '/*', '*/' ), '', $value );
	// Reject javascript:/expression()/url() vectors outright.
	if ( preg_match( '/(javascript:|expression\s*\(|url\s*\()/i', $value ) ) return '';
	return trim( $value );
}

/**
 * Brand copy with a shipped default.
 *
 * Reads option `ap_copy_<key>`, falling back to the string that currently ships
 * on Anchored Peptides so the flagship site renders unchanged when no option is
 * set. The Provisioner sets these per customer at deploy time.
 *
 * @param string $key     Copy key, e.g. 'hero_h1'.
 * @param string $default Shipped default string.
 * @return string
 */
function ap_copy( $key, $default = '' ) {
	$val = get_option( 'ap_copy_' . $key, null );
	if ( null === $val || '' === $val ) return $default;
	return (string) $val;
}
