<?php
/**
 * Anchored Peptides — Coming Soon page (self-contained takeover).
 * @package AnchoredPeptides
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$apcs_nonce   = wp_create_nonce( 'apcs_subscribe' );
$apcs_ajaxurl = admin_url( 'admin-ajax.php' );
$apcs_year    = gmdate( 'Y' );

// Hand-placed drifting "motes" (peptide-chain particles): left%, top%, size px, delay s, dur s, opacity.
$apcs_motes = array(
	array( 8, 22, 5, 0.0, 17, .30 ), array( 18, 70, 3, 2.4, 21, .22 ),
	array( 27, 38, 7, 1.1, 19, .26 ), array( 39, 84, 4, 3.2, 23, .20 ),
	array( 62, 18, 6, 0.6, 18, .28 ), array( 71, 60, 3, 2.0, 22, .20 ),
	array( 80, 33, 8, 1.5, 20, .24 ), array( 88, 76, 4, 3.6, 24, .18 ),
	array( 48, 12, 3, 2.8, 21, .22 ), array( 55, 90, 5, 0.9, 19, .24 ),
	array( 14, 50, 4, 4.0, 25, .18 ), array( 93, 48, 3, 1.8, 20, .20 ),
);
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html( get_bloginfo( 'name' ) ?: 'Anchored Peptides' ); ?> — Launching soon</title>
<meta name="description" content="Anchored Peptides is launching soon — third-party HPLC-tested research peptides, shipped same-day from the USA. Join the waitlist.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600&family=Newsreader:ital,opsz,wght@0,18..72,400;0,18..72,500;1,18..72,400;1,18..72,500&display=swap" rel="stylesheet">
<?php wp_site_icon(); ?>
<style>
:root{
	--bg0:#161a11; --bg1:#20261a; --bg2:#2c3322;
	--cream:#F4F0E6; --muted:#b9b5a4; --muted2:#8d8b7c;
	--sage:#adc4a3; --sage-d:#7e9a73;
	--line:rgba(244,240,230,.12);
	--serif:'Newsreader',Georgia,serif;
	--sans:'Hanken Grotesk',-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,sans-serif;
	--ease:cubic-bezier(.16,1,.3,1);
}
*{margin:0;padding:0;box-sizing:border-box}
html,body{height:100%}
body{
	font-family:var(--sans);
	color:var(--cream);
	background:var(--bg0);
	-webkit-font-smoothing:antialiased;
	overflow:hidden;
	position:relative;
}

/* ── Ambient background ── */
.apcs-bg{position:fixed;inset:0;z-index:0;overflow:hidden;background:
	radial-gradient(120% 90% at 50% -10%, #36402b 0%, #262d1c 38%, var(--bg0) 75%),
	linear-gradient(180deg, var(--bg1) 0%, var(--bg0) 100%);}
/* slow caustic light drift */
.apcs-bg::before{content:'';position:absolute;inset:-25%;
	background:radial-gradient(40% 38% at 30% 30%, rgba(173,196,163,.16), transparent 60%),
		radial-gradient(36% 34% at 76% 64%, rgba(126,154,115,.13), transparent 60%);
	animation:apcs-drift 24s ease-in-out infinite alternate;will-change:transform;}
/* grain */
.apcs-bg::after{content:'';position:absolute;inset:0;opacity:.05;mix-blend-mode:overlay;pointer-events:none;
	background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='2'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");}
/* vignette */
.apcs-vig{position:fixed;inset:0;z-index:1;pointer-events:none;
	box-shadow:inset 0 0 240px 40px rgba(10,12,7,.6);}
/* drifting motes (peptide particles) */
.apcs-mote{position:absolute;border-radius:50%;background:var(--sage);
	box-shadow:0 0 10px 1px rgba(173,196,163,.4);animation:apcs-float linear infinite;will-change:transform,opacity}

/* ── Layout ── */
.apcs-wrap{position:relative;z-index:2;min-height:100svh;display:flex;flex-direction:column;
	align-items:center;justify-content:center;text-align:center;padding:48px 24px;gap:0;}
.apcs-inner{max-width:620px;display:flex;flex-direction:column;align-items:center}

/* staggered reveal */
.apcs-rise{opacity:0;transform:translateY(18px);filter:blur(9px);
	animation:apcs-rise 1s var(--ease) forwards;}
.d1{animation-delay:.15s}.d2{animation-delay:.32s}.d3{animation-delay:.5s}
.d4{animation-delay:.7s}.d5{animation-delay:.9s}.d6{animation-delay:1.1s}

/* logo */
.apcs-logo{display:flex;flex-direction:column;align-items:center;gap:14px;margin-bottom:30px}
.apcs-anchor{width:54px;height:54px}
.apcs-anchor path,.apcs-anchor circle,.apcs-anchor line{
	stroke:var(--sage);stroke-width:2.1;fill:none;stroke-linecap:round;stroke-linejoin:round;
	stroke-dasharray:1;stroke-dashoffset:1;animation:apcs-draw 1.5s var(--ease) .25s forwards;}
.apcs-word{font-family:var(--sans);font-size:12px;font-weight:600;letter-spacing:.42em;
	text-transform:uppercase;color:var(--muted);padding-left:.42em}

.apcs-eyebrow{display:inline-flex;align-items:center;gap:9px;font-size:11px;font-weight:600;
	letter-spacing:.26em;text-transform:uppercase;color:var(--sage);margin-bottom:22px}
.apcs-eyebrow .dot{width:6px;height:6px;border-radius:50%;background:var(--sage);position:relative}
.apcs-eyebrow .dot::after{content:'';position:absolute;inset:-5px;border:1px solid var(--sage);
	border-radius:50%;opacity:.5;animation:apcs-ping 2.4s var(--ease) infinite}

h1.apcs-h{font-family:var(--serif);font-weight:400;color:var(--cream);
	font-size:clamp(40px,7.4vw,76px);line-height:1.02;letter-spacing:-.018em;margin-bottom:22px}
h1.apcs-h em{font-style:italic;color:var(--sage);position:relative;white-space:nowrap}

.apcs-sub{font-size:clamp(15px,1.7vw,17px);line-height:1.72;color:var(--muted);
	max-width:478px;margin-bottom:36px}

/* ── Form ── */
.apcs-form{display:flex;gap:10px;width:100%;max-width:440px;position:relative}
.apcs-field{flex:1;position:relative}
.apcs-form input[type=email]{width:100%;height:56px;padding:0 18px;font-family:var(--sans);
	font-size:15px;color:var(--cream);background:rgba(244,240,230,.05);
	border:1px solid var(--line);border-radius:14px;outline:none;
	transition:border-color .22s var(--ease),background .22s var(--ease),box-shadow .22s var(--ease),transform .22s var(--ease)}
.apcs-form input::placeholder{color:var(--muted2)}
.apcs-form input:focus{border-color:var(--sage-d);background:rgba(244,240,230,.08);
	box-shadow:0 0 0 4px rgba(173,196,163,.12);transform:translateY(-1px)}
.apcs-btn{height:56px;padding:0 26px;font-family:var(--sans);font-size:14px;font-weight:600;
	letter-spacing:.02em;color:#181c11;background:var(--sage);border:none;border-radius:14px;
	cursor:pointer;white-space:nowrap;display:inline-flex;align-items:center;justify-content:center;gap:8px;
	transition:background .2s var(--ease),transform .12s var(--ease),box-shadow .2s var(--ease);
	box-shadow:0 8px 24px rgba(0,0,0,.28)}
.apcs-btn:hover{background:#bcd2b2}
.apcs-btn:active{transform:scale(.97)}
.apcs-btn[disabled]{opacity:.7;cursor:default}
.apcs-btn .spin{width:16px;height:16px;border:2px solid rgba(24,28,17,.35);border-top-color:#181c11;
	border-radius:50%;display:none;animation:apcs-spin .7s linear infinite}
.apcs-btn.loading .label{display:none}
.apcs-btn.loading .spin{display:inline-block}

.apcs-note{font-size:12.5px;color:var(--muted2);margin-top:14px;min-height:18px;transition:color .2s}
.apcs-note.err{color:#e0a59a}

/* honeypot */
.apcs-hp{position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden}

/* ── Success state ── */
.apcs-success{display:none;flex-direction:column;align-items:center;gap:16px}
.apcs-success.show{display:flex;animation:apcs-rise .7s var(--ease) forwards}
.apcs-check{width:60px;height:60px;border-radius:50%;border:1.5px solid var(--sage-d);
	display:flex;align-items:center;justify-content:center;background:rgba(173,196,163,.08)}
.apcs-check svg{width:30px;height:30px}
.apcs-check path{stroke:var(--sage);stroke-width:2.6;fill:none;stroke-linecap:round;stroke-linejoin:round;
	stroke-dasharray:1;stroke-dashoffset:1;animation:apcs-draw .6s var(--ease) .1s forwards}
.apcs-success h2{font-family:var(--serif);font-weight:400;font-size:30px;color:var(--cream)}
.apcs-success p{font-size:14.5px;color:var(--muted);max-width:360px}

/* ── Footer ── */
.apcs-foot{position:fixed;bottom:0;left:0;right:0;z-index:2;display:flex;justify-content:center;
	padding:22px 24px}
.apcs-pills{display:flex;gap:8px;flex-wrap:wrap;justify-content:center;align-items:center;
	font-size:11px;letter-spacing:.04em;color:var(--muted2)}
.apcs-pills span{padding:6px 13px;border:1px solid var(--line);border-radius:100px;white-space:nowrap}
.apcs-pills i{font-style:normal;opacity:.5}

/* ── Keyframes ── */
@keyframes apcs-rise{to{opacity:1;transform:none;filter:blur(0)}}
@keyframes apcs-draw{to{stroke-dashoffset:0}}
@keyframes apcs-spin{to{transform:rotate(360deg)}}
@keyframes apcs-drift{0%{transform:translate3d(-2%,-1%,0) scale(1)}100%{transform:translate3d(3%,2%,0) scale(1.08)}}
@keyframes apcs-ping{0%{transform:scale(.6);opacity:.7}80%,100%{transform:scale(1.9);opacity:0}}
@keyframes apcs-float{0%{transform:translateY(0)}50%{transform:translateY(-26px)}100%{transform:translateY(0)}}

@media (max-width:560px){
	.apcs-form{flex-direction:column}
	.apcs-btn{width:100%}
	.apcs-foot{position:static;margin-top:36px}
	body{overflow:auto}
}

/* ── Reduced motion ── */
@media (prefers-reduced-motion: reduce){
	.apcs-rise{animation:apcs-fade .5s ease forwards}
	.apcs-bg::before,.apcs-mote,.apcs-eyebrow .dot::after{animation:none}
	.apcs-anchor path,.apcs-anchor circle,.apcs-anchor line,.apcs-check path{animation:none;stroke-dashoffset:0}
	@keyframes apcs-fade{to{opacity:1;transform:none;filter:none}}
}
</style>
</head>
<body>
<div class="apcs-bg" aria-hidden="true">
	<?php foreach ( $apcs_motes as $m ) : ?>
		<span class="apcs-mote" style="left:<?php echo (int) $m[0]; ?>%;top:<?php echo (int) $m[1]; ?>%;width:<?php echo (int) $m[2]; ?>px;height:<?php echo (int) $m[2]; ?>px;opacity:<?php echo esc_attr( $m[5] ); ?>;animation-delay:<?php echo esc_attr( $m[3] ); ?>s;animation-duration:<?php echo esc_attr( $m[4] ); ?>s"></span>
	<?php endforeach; ?>
</div>
<div class="apcs-vig" aria-hidden="true"></div>

<main class="apcs-wrap">
	<div class="apcs-inner">

		<div class="apcs-logo apcs-rise d1">
			<svg class="apcs-anchor" viewBox="0 0 40 40" aria-hidden="true">
				<circle cx="20" cy="9" r="3.6" pathLength="1"/>
				<line x1="20" y1="12.6" x2="20" y2="32" pathLength="1"/>
				<line x1="12.5" y1="16.5" x2="27.5" y2="16.5" pathLength="1"/>
				<path d="M10.5 24.5c0 6.5 9.5 10 9.5 10s9.5-3.5 9.5-10" pathLength="1"/>
			</svg>
			<span class="apcs-word"><?php echo esc_html( get_bloginfo( 'name' ) ?: 'Anchored Peptides' ); ?></span>
		</div>

		<p class="apcs-eyebrow apcs-rise d2"><span class="dot"></span> Launching soon</p>

		<h1 class="apcs-h apcs-rise d3">We’re dropping<br>anchor <em>soon.</em></h1>

		<p class="apcs-sub apcs-rise d4">A new U.S. home for third-party HPLC-tested research compounds — purity you can verify, dispatched same-day. Join the waitlist to be first through the door.</p>

		<form class="apcs-form apcs-rise d5" id="apcs-form" novalidate>
			<div class="apcs-field">
				<input type="email" id="apcs-email" name="email" placeholder="you@lab.com" autocomplete="email" required aria-label="Email address">
			</div>
			<div class="apcs-hp" aria-hidden="true">
				<label>Leave this empty <input type="text" name="apcs_hp" tabindex="-1" autocomplete="off"></label>
			</div>
			<button type="submit" class="apcs-btn" id="apcs-btn">
				<span class="label">Notify me</span><span class="spin"></span>
			</button>
		</form>
		<p class="apcs-note apcs-rise d6" id="apcs-note">No spam — just the launch date and our first-batch COAs.</p>

		<div class="apcs-success" id="apcs-success" aria-live="polite">
			<div class="apcs-check"><svg viewBox="0 0 24 24"><path d="M5 12.5l4.2 4.2L19 7" pathLength="1"/></svg></div>
			<h2>You’re anchored in.</h2>
			<p>We’ll email you the moment we open the doors. Welcome aboard.</p>
		</div>

	</div>
</main>

<footer class="apcs-foot apcs-rise d6">
	<div class="apcs-pills">
		<span>Third-party HPLC tested</span><i>·</i>
		<span>Ships from the USA</span><i>·</i>
		<span>For research use only</span>
		<i>·</i><span style="border:none;opacity:.6">© <?php echo esc_html( $apcs_year ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ?: 'Anchored Peptides' ); ?></span>
	</div>
</footer>

<script>
(function(){
	var form = document.getElementById('apcs-form'),
		btn  = document.getElementById('apcs-btn'),
		note = document.getElementById('apcs-note'),
		emailEl = document.getElementById('apcs-email'),
		success = document.getElementById('apcs-success'),
		AJAX = <?php echo wp_json_encode( $apcs_ajaxurl ); ?>,
		NONCE = <?php echo wp_json_encode( $apcs_nonce ); ?>;

	function setNote(msg, err){ note.textContent = msg; note.classList.toggle('err', !!err); }

	form.addEventListener('submit', function(e){
		e.preventDefault();
		var email = (emailEl.value||'').trim();
		if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)){ setNote('Please enter a valid email address.', true); emailEl.focus(); return; }

		btn.classList.add('loading'); btn.disabled = true; setNote('No spam — just the launch date and our first-batch COAs.', false);

		var body = new URLSearchParams();
		body.set('action','apcs_subscribe');
		body.set('nonce', NONCE);
		body.set('email', email);
		body.set('source','coming-soon');
		body.set('apcs_hp', form.apcs_hp ? form.apcs_hp.value : '');

		fetch(AJAX, { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:body.toString() })
			.then(function(r){ return r.json().catch(function(){ return {success:false,data:{message:'Something went wrong — please try again.'}}; }); })
			.then(function(res){
				btn.classList.remove('loading'); btn.disabled = false;
				if(res && res.success){
					// Best-effort: also push into Omnisend if its snippet is present.
					try{ if(window.omnisend){ window.omnisend.push(["identifyContact",{email:email}]); window.omnisend.push(["track","$subscribed",{email:email,source:"coming-soon"}]); } }catch(e){}
					form.style.transition='opacity .35s, transform .35s'; form.style.opacity='0'; form.style.transform='translateY(-8px)';
					note.style.transition='opacity .3s'; note.style.opacity='0';
					setTimeout(function(){ form.style.display='none'; note.style.display='none'; success.classList.add('show'); }, 340);
				} else {
					setNote((res && res.data && res.data.message) || 'Something went wrong — please try again.', true);
				}
			})
			.catch(function(){ btn.classList.remove('loading'); btn.disabled=false; setNote('Network error — please try again.', true); });
	});
})();
</script>
</body>
</html>
