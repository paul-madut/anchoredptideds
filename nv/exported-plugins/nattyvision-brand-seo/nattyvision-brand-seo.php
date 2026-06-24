<?php
/**
 * Plugin Name: Natty Vision Brand SEO
 * Description: Adds Organization, OnlineStore, and WebSite structured data so search engines treat "Natty Vision" / "Natty Vision Peptides" as one brand entity tied to this site. Purely additive: no theme, content, or front-end changes.
 * Version: 1.0.0
 * Author: Natty Vision
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ============================================================
 *  CONFIG. Edit the values below, save, activate. That is all.
 * ============================================================
 */
function nvbs_config() {
	return array(

		// Canonical brand name.
		'name' => 'Natty Vision',

		// Every variant you want Google to associate with the brand.
		// This is the part that makes "Natty Vision Peptides" resolve to you.
		'alternate_names' => array(
			'Natty Vision Peptides',
			'NattyVision',
			'Natty Vision Research Peptides',
		),

		// One-line entity description.
		'description' => 'Canadian supplier of third-party tested, 99%+ purity research peptides.',

		// Logo for the knowledge graph. Leave blank to auto-detect: it uses your
		// WordPress Site Icon first (served as a square PNG, ideal for Google),
		// then your theme logo, then the site's SVG mark as a last resort.
		// Only put a URL here if you want to force a specific image.
		'logo' => '',

		// Public profiles / listings that mention the brand. FILL THESE IN.
		// Each real, consistent link is an entity signal. The more, the faster
		// Google trusts that "Natty Vision" = this site.
		'same_as' => array(
			// 'https://www.instagram.com/nattyvision/',
			// 'https://www.tiktok.com/@nattyvision',
			// 'https://x.com/nattyvision',
			// 'https://www.youtube.com/@nattyvision',
		),

		// Support email (optional).
		'email' => 'support@nattyvision.com',
	);
}

/**
 * Resolve the best available logo URL.
 * Priority: explicit config override > WordPress Site Icon (square PNG) >
 * theme custom logo > the site's SVG mark.
 */
function nvbs_get_logo() {

	$cfg = nvbs_config();
	if ( ! empty( $cfg['logo'] ) ) {
		return $cfg['logo'];
	}

	// WordPress Site Icon: rasterized square PNG, exactly what Google wants.
	$icon = get_site_icon_url( 512 );
	if ( $icon ) {
		return $icon;
	}

	// Theme custom logo.
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$src = wp_get_attachment_image_src( $logo_id, 'full' );
		if ( ! empty( $src[0] ) ) {
			return $src[0];
		}
	}

	// Last resort: the brand mark that already ships on the site.
	return 'https://nattyvision.com/wp-content/uploads/2026/05/Horizontal-Black-2.svg';
}

/**
 * Output the brand schema on the homepage only.
 * Priority 1 so it lands early in <head>.
 */
add_action( 'wp_head', 'nvbs_output_schema', 1 );
function nvbs_output_schema() {

	if ( ! is_front_page() ) {
		return;
	}

	$cfg  = nvbs_config();
	$home = trailingslashit( home_url() );
	$logo = nvbs_get_logo();

	// --- Organization / OnlineStore node ---
	$org = array(
		'@type'         => array( 'Organization', 'OnlineStore' ),
		'@id'           => $home . '#organization',
		'name'          => $cfg['name'],
		'alternateName' => array_values( $cfg['alternate_names'] ),
		'url'           => $home,
		'description'   => $cfg['description'],
		'logo'          => array(
			'@type' => 'ImageObject',
			'@id'   => $home . '#logo',
			'url'   => $logo,
		),
		'image'         => array( '@id' => $home . '#logo' ),
	);

	if ( ! empty( $cfg['same_as'] ) ) {
		$org['sameAs'] = array_values( $cfg['same_as'] );
	}

	if ( ! empty( $cfg['email'] ) ) {
		$org['contactPoint'] = array(
			'@type'       => 'ContactPoint',
			'email'       => $cfg['email'],
			'contactType' => 'customer support',
		);
	}

	// --- WebSite node (enables the brand sitelinks search box) ---
	$website = array(
		'@type'           => 'WebSite',
		'@id'             => $home . '#website',
		'url'             => $home,
		'name'            => $cfg['name'],
		'alternateName'   => array_values( $cfg['alternate_names'] ),
		'publisher'       => array( '@id' => $home . '#organization' ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => $home . '?s={search_term_string}&post_type=product',
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array( $org, $website ),
	);

	echo "\n<!-- Natty Vision Brand SEO -->\n";
	echo '<script type="application/ld+json">'
		. wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
		. "</script>\n";
}
