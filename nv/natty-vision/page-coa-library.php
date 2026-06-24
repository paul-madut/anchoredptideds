<?php
/**
 * Template Name: COA Library
 *
 * Natty Vision — Certificate of Analysis library.
 * Auto-lists every published product that has a Kovera verify URL
 * (_nv_coa_url) set. The "View COA" button links off-site to the
 * independent lab. No batch history — the link reflects the latest lot.
 *
 * To use: create a Page (e.g. "COA Library") and select this template
 * under Page Attributes → Template.
 *
 * @package NattyVision
 */

defined( 'ABSPATH' ) || exit;

get_header();

$nv_coa_query = new WP_Query( array(
	'post_type'      => 'product',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'title',
	'order'          => 'ASC',
	'meta_query'     => array(
		array(
			'key'     => '_nv_coa_url',
			'value'   => '',
			'compare' => '!=',
		),
	),
) );

// Collect categories present in the result set for the filter pills.
$nv_coa_cats = array();
if ( $nv_coa_query->have_posts() ) {
	foreach ( $nv_coa_query->posts as $p ) {
		$terms = get_the_terms( $p->ID, 'product_cat' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $t ) {
				$nv_coa_cats[ $t->slug ] = $t->name;
			}
		}
	}
	asort( $nv_coa_cats );
}
?>

<main class="nv-coa">
	<div class="nv-container">

		<header class="nv-coa-hero">
			<p class="nv-coa-eyebrow"><?php esc_html_e( 'Certificate of Analysis', 'natty-vision' ); ?></p>
			<h1 class="nv-coa-title">COA <em>Library</em></h1>
			<p class="nv-coa-intro"><?php esc_html_e( 'Verified third-party lab results for every batch we ship. Search by compound or lot — every report, in the open.', 'natty-vision' ); ?></p>
		</header>

		<section class="nv-coa-endo">
			<div class="nv-coa-endo-grid">
				<div>
					<span class="nv-coa-endo-tag"><span class="dot"></span> <?php esc_html_e( 'The test most labs skip', 'natty-vision' ); ?></span>
					<h2><?php esc_html_e( 'We screen every batch for', 'natty-vision' ); ?> <span class="u"><?php esc_html_e( 'endotoxins', 'natty-vision' ); ?></span> — <?php esc_html_e( 'not just purity.', 'natty-vision' ); ?></h2>
					<p class="sub"><?php esc_html_e( 'Purity tells you what is in the vial. The endotoxin screen tells you what should not be. Bacterial endotoxins survive sterilization and do not show on a purity readout, so most suppliers never test for them. Every Natty Vision lot is screened — alongside purity, net content and LC-MS identity confirmation.', 'natty-vision' ); ?></p>
				</div>
				<div class="nv-coa-tiles">
					<div class="ttile"><span class="tn"><?php esc_html_e( 'Purity', 'natty-vision' ); ?></span><span class="tv">99<small>%+</small></span><span class="meth">HPLC</span></div>
					<div class="ttile"><span class="tn"><?php esc_html_e( 'Net content', 'natty-vision' ); ?></span><span class="tv"><?php esc_html_e( 'Full', 'natty-vision' ); ?><small> mg</small></span><span class="meth"><?php esc_html_e( 'Verified vs label', 'natty-vision' ); ?></span></div>
					<div class="ttile"><span class="tn"><?php esc_html_e( 'Identity', 'natty-vision' ); ?></span><span class="tv"><?php esc_html_e( 'Match', 'natty-vision' ); ?></span><span class="meth">LC-MS</span></div>
					<div class="ttile hl"><span class="tn"><?php esc_html_e( 'Endotoxins', 'natty-vision' ); ?></span><span class="tv"><?php esc_html_e( 'Pass', 'natty-vision' ); ?> <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span><span class="meth"><?php esc_html_e( 'LAL assay', 'natty-vision' ); ?></span></div>
				</div>
			</div>
		</section>

		<div class="nv-coa-stats">
			<div class="stat"><b>99%+</b><span><?php esc_html_e( 'Verified purity, every batch', 'natty-vision' ); ?></span></div>
			<div class="stat"><b>100%</b><span><?php esc_html_e( 'Of lots screened for endotoxins', 'natty-vision' ); ?></span></div>
			<div class="stat"><b><?php esc_html_e( 'Every lot', 'natty-vision' ); ?></b><span><?php esc_html_e( 'Independently lab-tested before it ships', 'natty-vision' ); ?></span></div>
		</div>

		<div class="nv-coa-controls">
			<div class="nv-coa-search">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
				<input type="text" id="nv-coa-search" placeholder="<?php esc_attr_e( 'Search by product or lot number…', 'natty-vision' ); ?>">
			</div>
			<?php if ( ! empty( $nv_coa_cats ) ) : ?>
			<div class="nv-coa-filters">
				<button class="nv-coa-pill active" data-cat="all"><?php esc_html_e( 'All', 'natty-vision' ); ?></button>
				<?php foreach ( $nv_coa_cats as $slug => $name ) : ?>
					<button class="nv-coa-pill" data-cat="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?></button>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>

		<?php if ( $nv_coa_query->have_posts() ) : ?>
		<div class="nv-coa-grid" id="nv-coa-grid">
			<?php
			while ( $nv_coa_query->have_posts() ) :
				$nv_coa_query->the_post();
				$id      = get_the_ID();
				$coa_url = nv_meta( $id, '_nv_coa_url' );
				if ( ! $coa_url ) continue;

				$lot     = nv_meta( $id, '_nv_coa_lot' );
				$purity  = nv_meta( $id, '_nv_coa_purity' );
				$tested  = nv_meta( $id, '_nv_coa_tested' );
				$badge   = nv_meta( $id, '_nv_badge' );

				$terms      = get_the_terms( $id, 'product_cat' );
				$cat_slugs  = array();
				$cat_label  = '';
				if ( $terms && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $t ) { $cat_slugs[] = $t->slug; }
					$cat_label = $terms[0]->name;
				}
				?>
				<article class="nv-coa-card"
					data-cats="<?php echo esc_attr( implode( ' ', $cat_slugs ) ); ?>"
					data-search="<?php echo esc_attr( strtolower( get_the_title() . ' ' . $lot ) ); ?>">

					<div class="nv-coa-cimg">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'woocommerce_thumbnail', array( 'class' => 'nv-coa-img', 'loading' => 'lazy' ) ); ?>
						<?php else : ?>
							<div class="nv-coa-img-ph" aria-hidden="true">
								<svg viewBox="0 0 100 130"><rect x="35" y="0" width="30" height="14" rx="2"/><rect x="20" y="20" width="60" height="105" rx="6"/></svg>
							</div>
						<?php endif; ?>
						<?php if ( $cat_label ) : ?><span class="nv-coa-cat"><?php echo esc_html( $cat_label ); ?></span><?php endif; ?>
						<?php if ( $purity ) : ?>
							<div class="nv-coa-pur"><b><?php echo esc_html( $purity ); ?>%</b><span><?php esc_html_e( 'Purity', 'natty-vision' ); ?></span></div>
						<?php endif; ?>
					</div>

					<div class="nv-coa-cbody">
						<p class="nv-coa-card-eyebrow"><?php echo esc_html( $cat_label ); ?><?php echo $badge ? ' · ' . esc_html( $badge ) : ''; ?></p>
						<h3 class="nv-coa-card-title"><?php the_title(); ?></h3>

						<?php if ( $tested || $lot ) : ?>
						<div class="nv-coa-meta">
							<?php if ( $tested ) : ?><div><div class="k"><?php esc_html_e( 'Latest batch', 'natty-vision' ); ?></div><div class="v"><?php echo esc_html( $tested ); ?></div></div><?php endif; ?>
							<?php if ( $lot ) : ?><div><div class="k"><?php esc_html_e( 'Lot', 'natty-vision' ); ?></div><div class="v"><?php echo esc_html( $lot ); ?></div></div><?php endif; ?>
						</div>
						<?php endif; ?>

						<div class="nv-coa-badges">
							<span class="b"><?php esc_html_e( 'Purity', 'natty-vision' ); ?></span>
							<span class="b"><?php esc_html_e( 'LC-MS ID', 'natty-vision' ); ?></span>
							<span class="b"><?php esc_html_e( 'Net content', 'natty-vision' ); ?></span>
							<span class="b endo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> <?php esc_html_e( 'Endotoxin', 'natty-vision' ); ?></span>
						</div>

						<a class="nv-coa-view" href="<?php echo esc_url( $coa_url ); ?>" target="_blank" rel="noopener nofollow">
							<?php esc_html_e( 'View COA', 'natty-vision' ); ?>
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M9 7h8v8"/></svg>
						</a>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<p class="nv-coa-empty" id="nv-coa-empty" hidden><?php esc_html_e( 'No results match your search.', 'natty-vision' ); ?></p>
		<?php else : ?>
			<p class="nv-coa-empty"><?php esc_html_e( 'Certificates of analysis are being added. Check back shortly.', 'natty-vision' ); ?></p>
		<?php endif; ?>

	</div>
</main>

<style>
.nv-coa{--cream:#f2f0eb;--paper:#fbfaf7;--ink:#1a1e1c;--ink-soft:#5a615b;--dark:#1a1e1c;--dark-2:#2a302d;--sage:#dce5d8;--sage-line:#c5d4c0;--sage-deep:#a8bfa2;--green-label:#4a6b54;--green:#5d9e74;--green-deep:#2f5740;--line:rgba(26,30,28,.14);--shadow:0 1px 2px rgba(26,30,28,.04),0 12px 36px rgba(26,30,28,.07);background:var(--cream);padding-bottom:64px;}
.nv-coa *{box-sizing:border-box;}
.nv-coa-hero{padding:58px 0 26px;}
.nv-coa-eyebrow{font-family:'DM Mono',monospace;font-size:11.5px;letter-spacing:.16em;text-transform:uppercase;color:var(--green-label);margin:0 0 6px;}
.nv-coa-title{font-family:'Instrument Serif',Georgia,serif;font-weight:400;font-size:clamp(52px,8vw,90px);line-height:.94;letter-spacing:-.01em;margin:0;color:var(--ink);}
.nv-coa-title em{font-style:italic;color:var(--green-deep);}
.nv-coa-intro{margin:14px 0 0;font-size:18px;color:var(--ink-soft);max-width:540px;}

.nv-coa-endo{position:relative;border-radius:20px;overflow:hidden;background:radial-gradient(130% 150% at 12% 0%,#27302b 0%,var(--dark-2) 44%,#11150f 100%);color:#eef2ea;padding:44px 46px;margin-top:24px;box-shadow:var(--shadow);}
.nv-coa-endo:before{content:"";position:absolute;inset:0;opacity:.045;background-image:radial-gradient(#fff 1px,transparent 1px);background-size:22px 22px;}
.nv-coa-endo-grid{position:relative;display:grid;grid-template-columns:1.12fr .88fr;gap:42px;align-items:center;}
.nv-coa-endo-tag{font-family:'DM Mono',monospace;display:inline-flex;align-items:center;gap:8px;font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:var(--sage-deep);margin-bottom:16px;}
.nv-coa-endo-tag .dot{width:7px;height:7px;border-radius:50%;background:var(--green);box-shadow:0 0 0 4px rgba(93,158,116,.2);}
.nv-coa-endo h2{font-family:'Instrument Serif',Georgia,serif;font-weight:400;font-size:clamp(30px,3.8vw,44px);line-height:1.04;margin:0;}
.nv-coa-endo h2 .u{font-style:italic;color:#bfe0c4;}
.nv-coa-endo .sub{margin:14px 0 0;font-size:15px;line-height:1.62;color:#bcc6ba;max-width:480px;}
.nv-coa-tiles{display:grid;grid-template-columns:1fr 1fr;gap:11px;}
.nv-coa-tiles .ttile{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.11);border-radius:13px;padding:16px 17px;min-height:104px;display:flex;flex-direction:column;justify-content:space-between;}
.nv-coa-tiles .tn{font-family:'DM Mono',monospace;font-size:9.5px;letter-spacing:.13em;text-transform:uppercase;color:#9fb6a3;}
.nv-coa-tiles .tv{font-family:'Instrument Serif',Georgia,serif;font-size:27px;line-height:.95;color:#fff;}
.nv-coa-tiles .tv small{font-size:14px;}
.nv-coa-tiles .meth{font-family:'DM Mono',monospace;font-size:9px;letter-spacing:.1em;text-transform:uppercase;color:#7f9485;margin-top:5px;}
.nv-coa-tiles .ttile.hl{background:rgba(93,158,116,.2);border-color:rgba(93,158,116,.5);}
.nv-coa-tiles .ttile.hl .tn{color:#bfe0c4;}.nv-coa-tiles .ttile.hl .meth{color:#9cc7a8;}
.nv-coa-tiles .ttile.hl .tv{color:#e6f3e8;display:flex;align-items:center;gap:9px;}
.nv-coa-tiles .ttile.hl .tv svg{width:22px;height:22px;color:#bfe0c4;flex:none;}

.nv-coa-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:16px;}
.nv-coa-stats .stat{background:var(--paper);border:1px solid var(--line);border-radius:14px;padding:22px 24px;}
.nv-coa-stats .stat b{font-family:'Instrument Serif',Georgia,serif;font-weight:400;font-size:40px;display:block;line-height:1;color:var(--ink);}
.nv-coa-stats .stat span{font-family:'DM Mono',monospace;font-size:11.5px;letter-spacing:.04em;color:var(--ink-soft);display:block;margin-top:9px;line-height:1.5;}

.nv-coa-controls{margin:40px 0 22px;display:flex;flex-direction:column;gap:18px;}
.nv-coa-search{display:flex;align-items:center;gap:12px;background:var(--paper);border:1px solid var(--line);border-radius:12px;padding:0 18px;height:52px;max-width:520px;}
.nv-coa-search svg{width:18px;height:18px;opacity:.5;color:var(--ink);}
.nv-coa-search input{border:0;background:none;outline:none;font-family:inherit;font-size:15px;width:100%;color:var(--ink);}
.nv-coa-filters{display:flex;gap:9px;flex-wrap:wrap;}
.nv-coa-pill{font-family:'DM Mono',monospace;border:1px solid var(--line);background:transparent;border-radius:999px;padding:9px 16px;font-size:11px;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;color:var(--ink);}
.nv-coa-pill.active{background:var(--ink);color:var(--cream);border-color:var(--ink);}

.nv-coa-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;}
.nv-coa-card{background:var(--paper);border:1px solid var(--line);border-radius:16px;overflow:hidden;box-shadow:var(--shadow);transition:transform .25s,box-shadow .25s;}
.nv-coa-card:hover{transform:translateY(-4px);box-shadow:0 2px 4px rgba(26,30,28,.05),0 18px 44px rgba(26,30,28,.13);}
.nv-coa-cimg{position:relative;aspect-ratio:1;background:radial-gradient(120% 120% at 35% 22%,#2b362e 0%,var(--dark-2) 46%,#10140e 100%);display:flex;align-items:center;justify-content:center;overflow:hidden;}
.nv-coa-cimg .nv-coa-img{width:100%;height:100%;object-fit:cover;}
.nv-coa-img-ph{display:flex;align-items:center;justify-content:center;}
.nv-coa-img-ph svg{width:74px;fill:rgba(255,255,255,.14);}
.nv-coa-cat{font-family:'DM Mono',monospace;position:absolute;top:15px;left:15px;font-size:9.5px;letter-spacing:.13em;text-transform:uppercase;color:#bcd0c0;border:1px solid rgba(255,255,255,.18);padding:5px 9px;border-radius:6px;background:rgba(0,0,0,.15);}
.nv-coa-pur{position:absolute;top:14px;right:15px;text-align:right;}
.nv-coa-pur b{font-family:'Instrument Serif',Georgia,serif;font-size:24px;color:#fff;display:block;line-height:1;}
.nv-coa-pur span{font-family:'DM Mono',monospace;font-size:8.5px;letter-spacing:.16em;text-transform:uppercase;color:#9fb6a3;}
.nv-coa-cbody{padding:18px;}
.nv-coa-card-eyebrow{font-family:'DM Mono',monospace;font-size:11px;letter-spacing:.13em;text-transform:uppercase;color:var(--green-label);margin:0 0 2px;}
.nv-coa-card-title{font-family:'Instrument Serif',Georgia,serif;font-weight:400;font-size:26px;margin:5px 0 0;line-height:1;color:var(--ink);}
.nv-coa-meta{display:flex;gap:24px;margin:13px 0 14px;padding:13px 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line);}
.nv-coa-meta .k{font-family:'DM Mono',monospace;font-size:9px;letter-spacing:.12em;text-transform:uppercase;color:var(--ink-soft);margin-bottom:4px;}
.nv-coa-meta .v{font-family:'DM Mono',monospace;font-size:13px;color:var(--ink);}
.nv-coa-badges{display:flex;flex-wrap:wrap;gap:6px;margin:14px 0 16px;}
.nv-coa-badges .b{font-family:'DM Mono',monospace;font-size:9.5px;letter-spacing:.03em;color:var(--green-label);background:rgba(74,107,84,.08);border:1px solid rgba(74,107,84,.18);border-radius:6px;height:26px;padding:0 9px;display:inline-flex;align-items:center;gap:5px;text-transform:uppercase;line-height:1;}
.nv-coa-badges .b svg{width:11px;height:11px;flex:none;}
.nv-coa-badges .b.endo{background:var(--sage);border-color:var(--sage-line);color:var(--green-deep);}
.nv-coa-view{font-family:'DM Mono',monospace;display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;height:46px;background:var(--ink);color:var(--cream);border:0;border-radius:10px;font-size:11.5px;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;text-decoration:none;transition:background .18s;}
.nv-coa-view:hover{background:#0e120f;}
.nv-coa-view svg{width:14px;height:14px;}
.nv-coa-empty{text-align:center;color:var(--ink-soft);padding:40px 0;font-size:16px;}

@media(max-width:1000px){.nv-coa-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:820px){.nv-coa-endo-grid{grid-template-columns:1fr;gap:26px;}.nv-coa-endo{padding:32px 24px;}}
@media(max-width:640px){.nv-coa-grid{grid-template-columns:1fr;}.nv-coa-stats{grid-template-columns:1fr;}}
</style>

<script>
(function(){
	var grid=document.getElementById('nv-coa-grid');
	if(!grid)return;
	var cards=[].slice.call(grid.querySelectorAll('.nv-coa-card'));
	var search=document.getElementById('nv-coa-search');
	var empty=document.getElementById('nv-coa-empty');
	var pills=[].slice.call(document.querySelectorAll('.nv-coa-pill'));
	var activeCat='all';

	function apply(){
		var q=(search&&search.value||'').trim().toLowerCase();
		var shown=0;
		cards.forEach(function(c){
			var catOk=activeCat==='all'||(' '+c.dataset.cats+' ').indexOf(' '+activeCat+' ')>-1;
			var qOk=!q||(c.dataset.search||'').indexOf(q)>-1;
			var vis=catOk&&qOk;
			c.style.display=vis?'':'none';
			if(vis)shown++;
		});
		if(empty)empty.hidden=shown>0;
	}
	pills.forEach(function(p){p.addEventListener('click',function(){
		pills.forEach(function(x){x.classList.remove('active');});
		p.classList.add('active');activeCat=p.dataset.cat;apply();
	});});
	if(search)search.addEventListener('input',apply);
})();
</script>

<?php
get_footer();
