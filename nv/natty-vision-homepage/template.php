<?php if (!defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Natty Vision — Lab-Grade Peptides for Precision Research</title>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
<link href="https://api.fontshare.com/v2/css?f[]=neue-montreal@400,500,700&display=swap" rel="stylesheet">
<style>
:root{--bg:#f2f0eb;--bg2:#e9e7e1;--bg-card:#eae8e2;--sage:#c5d4c0;--sage-s:#dce5d8;--sage-d:#a8bfa2;--dark:#1a1e1c;--dark2:#232826;--green:#2d6a4f;--green-l:#40916c;--green-b:#52b788;--text:#1a1e1c;--t2:#4a4f4c;--t3:#7a7f7c;--ti:#f2f0eb;--ti2:#b0aea8;--brd:#d4d2cc;--brd2:#c4c2bc;--navy:#1b2a4a;--r:16px;--rs:12px;--rx:8px;--rp:100px}
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
body{font-family:'Neue Montreal',-apple-system,Helvetica,sans-serif;background:var(--bg);color:var(--text);-webkit-font-smoothing:antialiased;overflow-x:hidden}
.container{max-width:1240px;margin:0 auto;padding:0 32px}
.mono{font-family:'DM Mono',monospace;text-transform:uppercase;letter-spacing:.1em}

/* Real image styling */
.bqv img{width:100%;height:100%;object-fit:cover;display:block;position:absolute;inset:0;z-index:0}

/* ── ANNOUNCE ── */
.announce{background:var(--dark);color:var(--ti2);text-align:center;padding:11px 20px;font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.12em}
.announce strong{color:var(--ti)}
.announce a{color:var(--green-b);text-decoration:underline;text-underline-offset:3px;margin-left:8px}

/* ── NAV ── */
nav{position:sticky;top:0;z-index:100;background:rgba(242,240,235,.85);backdrop-filter:blur(20px);border-bottom:1px solid var(--brd);padding:0 40px}
.nav-i{max-width:1240px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;height:64px}
.nl{display:flex;align-items:center;text-decoration:none}
.nl-svg{height:38px;width:auto}
.nc{display:flex;align-items:center;background:var(--bg-card);border:1px solid var(--brd);border-radius:var(--rp);padding:4px;gap:0}
.nc a{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.08em;padding:8px 18px;border-radius:var(--rp);text-decoration:none;color:var(--t2);transition:all .3s}
.nc a:hover,.nc a.on{color:var(--text);background:var(--bg)}
.nr{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--t2);text-decoration:none;transition:color .3s}
.nr:hover{color:var(--text)}
.nav-right{display:flex;align-items:center;gap:20px}
.nav-cart{position:relative;display:flex;align-items:center;color:var(--t2);transition:color .3s}
.nav-cart:hover{color:var(--text)}
.nav-cart-count{position:absolute;top:-6px;right:-8px;background:var(--green-b);color:var(--dark);font-family:'DM Mono',monospace;font-size:9px;font-weight:500;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center}

/* ── HERO ── */
.hero{position:relative;min-height:92vh;display:flex;align-items:flex-start;padding:120px 0 60px;overflow:hidden}
.hero-bg{position:absolute;inset:0;background:url('<?php echo plugins_url('images/hero.png', __FILE__); ?>') center center/cover no-repeat}
.hero-bg::after{content:'';position:absolute;inset:0;background:linear-gradient(to right,rgba(242,240,235,.92) 0%,rgba(242,240,235,.7) 35%,rgba(242,240,235,.15) 65%,transparent 100%),linear-gradient(to bottom,transparent 70%,rgba(242,240,235,1) 100%)}
.hero-c{position:relative;z-index:2;max-width:740px}
.hero h1{font-family:'Instrument Serif',serif;font-size:clamp(42px,5.5vw,72px);font-weight:400;line-height:1.05;letter-spacing:-.03em;margin-bottom:20px}
.hero h1 em{font-style:italic}
.hero-s{font-size:15px;line-height:1.7;color:var(--t2);max-width:520px;margin-bottom:32px}
.hero-btns{display:flex;gap:12px;flex-wrap:wrap}
.bd{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.1em;background:var(--dark);color:var(--ti);padding:14px 28px;border-radius:var(--rx);text-decoration:none;transition:all .3s;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px}
.bd:hover{background:#2a302d;transform:translateY(-1px)}
.bo{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.1em;background:transparent;color:var(--text);padding:14px 28px;border-radius:var(--rx);border:1px solid var(--brd2);text-decoration:none;transition:all .3s;cursor:pointer;display:inline-flex;align-items:center;gap:8px}
.bo:hover{background:var(--bg-card);border-color:var(--t3)}
.hero-p{margin-top:36px;font-size:13px;color:var(--t3)}
.hero-p strong{color:var(--text)}

/* ── CATS ── */
.cats{padding:64px 0;border-bottom:1px solid var(--brd)}
.cats-head{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:32px;gap:20px;flex-wrap:wrap}
.cats-head h2{font-family:'Instrument Serif',serif;font-size:clamp(28px,3.2vw,40px);font-weight:400;letter-spacing:-.02em;line-height:1.1;max-width:560px}
.cats-head h2 em{font-style:italic;color:var(--green)}
.cats-head p{font-size:14px;color:var(--t2);max-width:340px;line-height:1.5}
.promo{display:flex;align-items:center;justify-content:center;gap:14px;background:var(--sage-s);border:1px solid var(--sage);border-radius:var(--rp);padding:12px 28px;margin-bottom:24px;flex-wrap:wrap}
.promo .pn{font-family:'Instrument Serif',serif;font-size:22px;font-weight:400;line-height:1;color:var(--green)}
.promo .ps{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:var(--t2)}
.promo .pa{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:var(--green);text-decoration:none;border-left:1px solid var(--sage-d);padding-left:14px;transition:color .3s}
.promo .pa:hover{color:var(--dark)}
.cgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.cc{background:var(--sage-s);border:1px solid var(--sage);border-radius:var(--r);padding:32px 30px;text-decoration:none;color:var(--text);transition:all .35s;display:flex;flex-direction:column;justify-content:space-between;min-height:220px;position:relative;overflow:hidden}
.cc:hover{transform:translateY(-3px);background:#d3e0cf;border-color:var(--sage-d)}
.cc-icon{width:42px;height:42px;border-radius:12px;background:rgba(45,106,79,.1);display:flex;align-items:center;justify-content:center;color:var(--green);margin-bottom:22px}
.cc-icon svg{width:22px;height:22px}
.cc-name{font-family:'Instrument Serif',serif;font-size:26px;font-weight:400;line-height:1.15;letter-spacing:-.01em;margin-bottom:8px}
.cc-desc{font-size:13px;color:var(--t2);line-height:1.5;margin-bottom:14px}
.cc-arrow{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--green);display:flex;align-items:center;gap:6px;margin-top:auto;transition:gap .3s}
.cc:hover .cc-arrow{gap:10px}

/* ── BESTSELLERS ── */
.bs{padding:80px 0;background:var(--bg2)}
.bs-head{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:32px;gap:20px;flex-wrap:wrap;padding:0 32px;max-width:1240px;margin-left:auto;margin-right:auto}
.bs-head .l{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.15em;color:var(--green);margin-bottom:10px}
.bs-head h2{font-family:'Instrument Serif',serif;font-size:clamp(28px,3.2vw,42px);font-weight:400;letter-spacing:-.02em;line-height:1.1}
.bs-head h2 em{font-style:italic}
.bs-head p{font-size:14px;color:var(--t2);max-width:300px;line-height:1.5}
.bs-nav{display:flex;gap:8px}
.bs-nav button{width:40px;height:40px;border-radius:50%;border:1px solid var(--brd2);background:var(--bg);color:var(--text);cursor:pointer;font-size:16px;transition:all .3s;display:flex;align-items:center;justify-content:center}
.bs-nav button:hover{background:var(--dark);color:var(--ti);border-color:var(--dark)}
.bs-nav button:disabled{opacity:.3;cursor:default}
.bs-nav button:disabled:hover{background:var(--bg);color:var(--text);border-color:var(--brd2)}
.bs-track{display:flex;gap:16px;overflow-x:auto;padding:8px 32px 24px;scroll-snap-type:x mandatory;scroll-padding:32px;-ms-overflow-style:none;scrollbar-width:none;max-width:1240px;margin:0 auto}
.bs-track::-webkit-scrollbar{display:none}
.bsc{flex:0 0 280px;scroll-snap-align:start;background:var(--bg);border:1px solid var(--brd);border-radius:var(--r);overflow:hidden;text-decoration:none;color:var(--text);transition:all .3s;display:flex;flex-direction:column}
.bsc:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(26,30,28,.08);border-color:var(--brd2)}
.bsc-img{aspect-ratio:1;background:linear-gradient(145deg,var(--sage-s),var(--bg2));display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden}
.bsc-img img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0;z-index:0}
.bsc-img .ic{font-size:64px;opacity:.14;color:var(--green)}
.bsc-tag{position:absolute;top:12px;left:12px;background:var(--green-b);color:white;font-family:'DM Mono',monospace;font-size:9px;text-transform:uppercase;letter-spacing:.08em;padding:4px 10px;border-radius:6px;font-weight:500}
.bsc-tag.alt{background:var(--dark);color:var(--ti)}
.bsc-body{padding:20px;display:flex;flex-direction:column;flex:1}
.bsc-cat{font-family:'DM Mono',monospace;font-size:9px;text-transform:uppercase;letter-spacing:.14em;color:var(--green);margin-bottom:6px}
.bsc-name{font-size:17px;font-weight:500;margin-bottom:3px}
.bsc-spec{font-size:12px;color:var(--t3);margin-bottom:14px;line-height:1.4}
.bsc-foot{display:flex;justify-content:space-between;align-items:center;margin-top:auto}
.bsc-price{font-family:'DM Mono',monospace;font-size:15px;font-weight:500;color:var(--green)}
.bsc-rating{font-size:11px;color:#d4a94c;letter-spacing:1px}

/* ── FEATURES (sticky-stack reveal) ── */
.feats{padding:100px 0;background:var(--bg)}
.feats-inner{position:relative}
.fr{position:sticky;display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;background:var(--bg);border-radius:24px;padding:48px;border:1px solid var(--brd);min-height:520px;will-change:transform,opacity}
.fr:nth-child(1){top:90px;z-index:1;background:var(--bg-card)}
.fr:nth-child(2){top:120px;z-index:2;margin-top:60px;background:var(--sage-s);border-color:var(--sage)}
.fr:nth-child(3){top:150px;z-index:3;margin-top:60px;background:var(--dark);border-color:var(--dark2);color:var(--ti)}
.fr:nth-child(3) .fl{color:var(--green-b)}
.fr:nth-child(3) .fh{color:var(--ti)}
.fr:nth-child(3) .fd{color:var(--ti2)}
.fr:nth-child(3) .fk{color:var(--green-b)}
.fr.rev .ft{order:2}.fr.rev .fv{order:1}
.fl{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.15em;color:var(--green);margin-bottom:18px;display:inline-flex;align-items:center;gap:10px}
.fl::before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor;animation:pulse-dot 2s ease-in-out infinite}
@keyframes pulse-dot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.8)}}
.fh{font-family:'Instrument Serif',serif;font-size:clamp(32px,3.6vw,46px);font-weight:400;line-height:1.08;letter-spacing:-.02em;margin-bottom:20px}
.fd{font-size:15px;line-height:1.7;color:var(--t2);margin-bottom:32px;max-width:440px}
.fk{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:var(--green);text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:gap .35s cubic-bezier(.16,1,.3,1);position:relative}
.fk::after{content:'';position:absolute;left:0;bottom:-3px;width:0;height:1px;background:currentColor;transition:width .4s cubic-bezier(.16,1,.3,1)}
.fk:hover{gap:14px}
.fk:hover::after{width:calc(100% - 24px)}
.fv{position:relative;overflow:hidden;border-radius:18px;height:440px;background:var(--bg2)}
.fv img{width:100%;height:100%;object-fit:cover;transition:transform 1.4s cubic-bezier(.16,1,.3,1)}
.fr:hover .fv img{transform:scale(1.04)}

/* ── PRODUCTS ── */
.prods{padding:100px 0;background:var(--bg2)}
.ph{text-align:center;margin-bottom:48px}
.ph h2{font-family:'Instrument Serif',serif;font-size:clamp(30px,3.5vw,48px);font-weight:400;letter-spacing:-.02em;margin-bottom:14px}
.ph p{font-size:15px;color:var(--t2);max-width:480px;margin:0 auto;line-height:1.6}
.pg{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.pc{background:var(--bg);border:1px solid var(--brd);border-radius:var(--r);overflow:hidden;transition:all .4s;cursor:pointer}
.pc:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(26,30,28,.08);border-color:var(--brd2)}
.pci{height:200px;background:linear-gradient(145deg,var(--sage-s),var(--bg2));display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden}
.pci .ic{font-size:52px;opacity:.12}
.pb{position:absolute;top:12px;left:12px;background:var(--green-b);color:white;font-family:'DM Mono',monospace;font-size:9px;text-transform:uppercase;letter-spacing:.08em;padding:4px 10px;border-radius:6px}
.pcb{padding:20px}
.pcc{font-family:'DM Mono',monospace;font-size:9px;text-transform:uppercase;letter-spacing:.14em;color:var(--green);margin-bottom:6px}
.pcn{font-size:16px;font-weight:500;margin-bottom:2px}
.pcs{font-size:12px;color:var(--t3);margin-bottom:14px}
.pcf{display:flex;justify-content:space-between;align-items:center}
.pcp{font-family:'DM Mono',monospace;font-size:15px;font-weight:500;color:var(--green)}
.pcr{font-size:12px;color:#d4a94c}

/* ── STATS ── */
.stats{padding:100px 0}
.sc-row{display:grid;grid-template-columns:2fr 1fr 1fr;gap:16px}
.sc{background:var(--bg-card);border:1px solid var(--brd);border-radius:var(--r);padding:40px;display:flex;flex-direction:column;justify-content:center}
.sc-t{font-family:'Instrument Serif',serif;font-size:26px;font-weight:400;line-height:1.3;margin-bottom:10px}
.sc-s{font-size:14px;color:var(--t2);line-height:1.6}
.sn{background:var(--dark);border-radius:var(--r);padding:36px;display:flex;flex-direction:column;justify-content:center;color:var(--ti)}
.snv{display:flex;align-items:baseline;gap:4px;margin-bottom:10px}
.snv .b{font-family:'Instrument Serif',serif;font-size:56px;font-weight:400;line-height:1}
.snv .s{font-family:'Instrument Serif',serif;font-size:28px;color:var(--green-b)}
.snd{font-size:13px;color:var(--ti2);line-height:1.5}

/* ── FEATURED STACK ── */
.fs{padding:100px 0;background:var(--dark);color:var(--ti);overflow:hidden;position:relative}
.fs::before{content:'';position:absolute;top:-150px;right:-200px;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(82,183,136,.15) 0%,transparent 60%);pointer-events:none;z-index:0}
.fs-i{display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;position:relative;z-index:1}
.fs-l{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.15em;color:var(--green-b);margin-bottom:18px;display:inline-flex;align-items:center;gap:8px;padding:6px 14px;background:rgba(82,183,136,.1);border:1px solid rgba(82,183,136,.25);border-radius:100px}
.fs-h{font-family:'Instrument Serif',serif;font-size:clamp(36px,4vw,52px);font-weight:400;line-height:1.05;letter-spacing:-.02em;margin-bottom:18px}
.fs-h em{font-style:italic;color:var(--green-b)}
.fs-d{font-size:15px;color:var(--ti2);line-height:1.7;margin-bottom:28px;max-width:460px}
.fs-list{list-style:none;margin-bottom:32px;padding:0}
.fs-list li{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.08);font-size:14px;color:var(--ti)}
.fs-list li:last-child{border-bottom:none}
.fs-list li svg{width:18px;height:18px;color:var(--green-b);flex-shrink:0}
.fs-price{display:flex;align-items:baseline;gap:14px;margin-bottom:8px;flex-wrap:wrap}
.fs-price .now{font-family:'Instrument Serif',serif;font-size:48px;font-weight:400;color:var(--ti);line-height:1}
.fs-price .unit{font-family:'DM Mono',monospace;font-size:12px;text-transform:uppercase;letter-spacing:.1em;color:var(--ti2)}
.fs-cta{font-family:'DM Mono',monospace;font-size:12px;text-transform:uppercase;letter-spacing:.1em;background:var(--green-b);color:var(--dark);padding:16px 32px;border-radius:var(--rx);text-decoration:none;display:inline-flex;align-items:center;gap:10px;font-weight:500;transition:transform .35s cubic-bezier(.16,1,.3,1),background .3s,box-shadow .35s;--mx:0px;--my:0px;margin-top:24px}
.fs-cta:hover{background:#6cc99a;transform:translate(var(--mx),calc(var(--my) - 2px));box-shadow:0 12px 30px rgba(82,183,136,.3)}
.fs-stock{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:var(--ti2);margin-top:14px;display:flex;align-items:center;gap:8px}
.fs-stock::before{content:'';width:6px;height:6px;border-radius:50%;background:var(--green-b)}
@keyframes fs-pulse{0%,100%{opacity:1}50%{opacity:.4}}
.fs-v{position:relative;height:480px;border-radius:24px;overflow:hidden;background:linear-gradient(145deg,#2a3530,#1a1e1c);border:1px solid rgba(255,255,255,.06)}
.fs-v img.fs-v-img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0;z-index:0}
.fs-v::before{content:'KLOW';position:absolute;top:32px;left:32px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.2em;color:var(--ti2);z-index:2}
.fs-v::after{content:'';position:absolute;inset:60px;background:radial-gradient(circle,rgba(82,183,136,.18) 0%,transparent 60%);border-radius:50%}
.fs-v-glyph{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:240px;color:rgba(82,183,136,.1);font-family:'Instrument Serif',serif;font-style:italic;line-height:1;transition:transform 1.2s cubic-bezier(.16,1,.3,1)}
.fs-v:hover .fs-v-glyph{transform:scale(1.06)}
.fs-v-tag{position:absolute;top:32px;right:32px;background:var(--green-b);color:var(--dark);font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.1em;padding:6px 14px;border-radius:100px;font-weight:500;z-index:2}
.fs-v-meta{position:absolute;bottom:32px;left:32px;right:32px;display:flex;justify-content:space-between;align-items:center;color:var(--ti2);font-family:'DM Mono',monospace;font-size:11px;letter-spacing:.05em;z-index:2}
.fs-v-meta .pur{color:var(--green-b)}

/* Old BIG QUOTE rules retained for safety (not used) */
.bq{padding:100px 0;background:var(--dark);color:var(--ti)}
.bqi{display:grid;grid-template-columns:1.2fr 1fr;gap:80px;align-items:center}
.bql{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.15em;color:var(--ti2);margin-bottom:24px;display:flex;align-items:center;gap:10px}
.bql .ln{width:20px;height:1px;background:var(--ti2)}
.bqt{font-family:'Instrument Serif',serif;font-size:clamp(22px,2.6vw,32px);font-weight:400;line-height:1.35;margin-bottom:20px}
.bqt em{font-style:italic;color:var(--green-b)}
.bqs{font-size:15px;color:var(--ti2);line-height:1.7;margin-bottom:32px}
.bqa{display:flex;align-items:center;gap:14px}
.bqav{width:48px;height:48px;border-radius:12px;background:var(--dark2);border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:600;color:var(--green-b)}
.bqn{font-size:15px;font-weight:500}
.bqr{font-size:12px;color:var(--ti2)}
.bqv{border-radius:var(--r);overflow:hidden;height:400px;position:relative;background:linear-gradient(135deg,#2d3a35,#1a1e1c)}

/* ── TESTIMONIALS ── */
.tst{padding:80px 0}
.tst-l{text-align:center;font-size:14px;color:var(--t2);margin-bottom:36px;font-weight:500}
.ts-mask{position:relative;mask-image:linear-gradient(to right,transparent 0,#000 5%,#000 95%,transparent 100%);-webkit-mask-image:linear-gradient(to right,transparent 0,#000 5%,#000 95%,transparent 100%);overflow:hidden}
.ts{display:flex;gap:16px;width:max-content;animation:ts-scroll 60s linear infinite;will-change:transform}
.ts-mask:hover .ts{animation-play-state:paused}
@keyframes ts-scroll{from{transform:translateX(0)}to{transform:translateX(calc(-50% - 8px))}}
.tc{flex:0 0 340px;background:var(--bg-card);border:1px solid var(--brd);border-radius:var(--r);padding:28px;display:flex;flex-direction:column;gap:20px;transition:transform .3s,border-color .3s}
.tc:hover{transform:translateY(-3px);border-color:var(--brd2)}
.tq{font-size:14px;line-height:1.7;color:var(--t2);flex:1}
.tb{display:flex;align-items:center;gap:12px}
.tav{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;flex-shrink:0}
.a1{background:var(--sage);color:var(--green)}.a2{background:var(--navy);color:white}.a3{background:#d4a94c;color:white}.a4{background:#8b5e3c;color:white}.a5{background:var(--green-l);color:white}
.tn{font-size:13px;font-weight:600}
.tr{font-size:11px;color:var(--t3)}
@media(prefers-reduced-motion:reduce){.ts{animation:none}}

/* ── FAQ ── */
.faq{padding:100px 0;background:var(--bg2)}
.faq-t{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:48px}
.faq-h{font-family:'Instrument Serif',serif;font-size:clamp(30px,3.5vw,44px);font-weight:400;letter-spacing:-.02em}
.faq-c{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:var(--green);text-decoration:none;border:1px solid var(--sage);padding:10px 22px;border-radius:var(--rp);transition:all .3s}
.faq-c:hover{background:var(--sage-s)}
.fi{border-bottom:1px solid var(--brd);cursor:pointer}
.fiq{display:flex;justify-content:space-between;align-items:center;padding:24px 0;font-size:17px;font-weight:500;transition:color .3s}
.fiq:hover{color:var(--green)}
.fia{font-size:20px;color:var(--t3);transition:transform .3s}
.fi.on .fia{transform:rotate(45deg);color:var(--green)}
.fians{max-height:0;overflow:hidden;transition:max-height .4s ease}
.fi.on .fians{max-height:200px;padding-bottom:24px}
.fians p{font-size:14px;color:var(--t2);line-height:1.7;max-width:680px}

/* ── CHAT BUBBLES ── */
.chat-bubbles{position:absolute;top:22%;left:18%;right:18%;bottom:22%;display:flex;flex-direction:column;justify-content:center;gap:8px;z-index:2}
.chat-b{display:flex;align-items:center;gap:5px;opacity:0;transform:translateY(6px);transition:opacity .4s ease,transform .4s ease;max-width:80%}
.chat-b.show{opacity:1;transform:translateY(0)}
.cb1{align-self:flex-start}
.cb2{flex-direction:row-reverse;align-self:flex-end}
.cb3{align-self:flex-start}
.cb4{flex-direction:row-reverse;align-self:flex-end}
.cb-dot{width:18px;height:18px;border-radius:50%;background:var(--green-b);flex-shrink:0;display:flex;align-items:center;justify-content:center;color:white}
.cb-dot::after{content:'NV';font-family:'DM Mono',monospace;font-size:5px;letter-spacing:.02em}
.cb-dot-u{background:var(--navy)}
.cb-dot-u::after{content:'DR'}
.chat-b span{padding:6px 10px;border-radius:8px;font-size:9px;line-height:1.3;box-shadow:0 2px 8px rgba(0,0,0,.06)}
.cb2 span,.cb4 span{background:rgba(27,42,74,.9);color:white}
.cb1 span,.cb3 span{background:rgba(255,255,255,.9);color:#1a1e1c}

/* ── CTA ── */
.cta{padding:100px 0;text-align:center}
.cta-l{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.15em;color:var(--t3);margin-bottom:16px}
.cta h2{font-family:'Instrument Serif',serif;font-size:clamp(36px,4.5vw,64px);font-weight:400;letter-spacing:-.03em;margin-bottom:32px;line-height:1.08}
.cta h2 em{font-style:italic;color:var(--green)}

/* ── FOOTER ── */
footer{border-top:1px solid var(--brd);padding:40px 0}
.foot{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}
.flinks{display:flex;gap:24px}
.flinks a{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--t3);text-decoration:none;transition:color .3s}
.flinks a:hover{color:var(--text)}
.fcopy{font-size:12px;color:var(--t3)}

/* ── ANIM ── */
.an{opacity:0;transform:translateY(36px);transition:opacity .9s cubic-bezier(.16,1,.3,1),transform .9s cubic-bezier(.16,1,.3,1)}
.an.v{opacity:1;transform:translateY(0)}

/* Stats — counter idle animation */
.snv .b{display:inline-block;transition:transform .6s cubic-bezier(.34,1.56,.64,1)}
.sn:hover .snv .b{transform:translateY(-4px) scale(1.05)}
.sc-t,.snd{transition:opacity .4s}

/* Hero — gradient pulse + button magnetic */
.hero h1{animation:hero-fade-up 1.2s cubic-bezier(.16,1,.3,1) both}
.hero-s{animation:hero-fade-up 1.2s .15s cubic-bezier(.16,1,.3,1) both}
.hero-btns{animation:hero-fade-up 1.2s .3s cubic-bezier(.16,1,.3,1) both}
.hero-p{animation:hero-fade-up 1.2s .45s cubic-bezier(.16,1,.3,1) both}
@keyframes hero-fade-up{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}

/* Buttons — premium hover */
.bd{position:relative;overflow:hidden;transition:transform .35s cubic-bezier(.16,1,.3,1),background .35s,box-shadow .35s;--mx:0px;--my:0px}
.bd::before{content:'';position:absolute;inset:0;background:linear-gradient(120deg,transparent 30%,rgba(82,183,136,.25) 50%,transparent 70%);transform:translateX(-100%);transition:transform .8s cubic-bezier(.16,1,.3,1)}
.bd:hover{transform:translate(var(--mx),calc(var(--my) - 2px));box-shadow:0 12px 30px rgba(26,30,28,.18)}
.bd:hover::before{transform:translateX(100%)}
.bd:active{transform:translate(var(--mx),var(--my))}
.bo{position:relative;overflow:hidden;transition:all .35s cubic-bezier(.16,1,.3,1);--mx:0px;--my:0px;z-index:1}
.bo::after{content:'';position:absolute;inset:0;background:var(--dark);transform:translateY(101%);transition:transform .4s cubic-bezier(.16,1,.3,1);z-index:-1}
.bo:hover{color:var(--ti);border-color:var(--dark);transform:translate(var(--mx),var(--my))}
.bo:hover::after{transform:translateY(0)}

/* Bestseller arrows — magnetic + bounce */
.bs-nav button{transition:transform .35s cubic-bezier(.16,1,.3,1),background .3s,color .3s,border-color .3s;--mx:0px;--my:0px}
.bs-nav button:hover:not(:disabled){transform:translate(calc(var(--mx) + 0px),calc(var(--my) - 2px)) scale(1.08)}
.bs-nav button:active:not(:disabled){transform:scale(.95)}

/* Card hovers — premium lift */
.cc{transition:transform .45s cubic-bezier(.16,1,.3,1),background .35s,border-color .35s,box-shadow .45s}
.cc:hover{transform:translateY(-5px);box-shadow:0 20px 50px rgba(45,106,79,.12)}
.bsc{transition:transform .45s cubic-bezier(.16,1,.3,1),box-shadow .45s,border-color .35s}
.bsc:hover{transform:translateY(-6px);box-shadow:0 24px 50px rgba(26,30,28,.10)}
.hc{transition:transform .35s cubic-bezier(.16,1,.3,1),background .3s,border-color .3s,box-shadow .35s}
.hc:hover{transform:translateY(-3px) translateX(2px);box-shadow:0 12px 28px rgba(26,30,28,.06)}

/* Promo banner shimmer */
.promo{position:relative;overflow:hidden}
.promo::before{content:'';position:absolute;inset:0;background:linear-gradient(120deg,transparent 30%,rgba(255,255,255,.5) 50%,transparent 70%);transform:translateX(-100%);animation:shimmer 4s ease-in-out infinite;pointer-events:none}
@keyframes shimmer{0%,40%{transform:translateX(-100%)}60%,100%{transform:translateX(100%)}}

/* Chat dots — gentle pulse */
.cb-dot{animation:dot-float 3s ease-in-out infinite}
.chat-b.cb2 .cb-dot,.chat-b.cb4 .cb-dot{animation-delay:1.5s}
@keyframes dot-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-3px)}}

/* Hero background — handled via JS parallax */

/* FAQ — smoother accordion */
.fians{transition:max-height .5s cubic-bezier(.16,1,.3,1),padding-bottom .4s}

/* Number counter base */
[data-counter]{display:inline-block}

/* ── PRODUCT SCROLLERS (lower section, horizontal cards) ── */
.exp{padding:100px 0;background:var(--bg)}
.exp-head{text-align:center;max-width:620px;margin:0 auto 56px}
.exp-head .l{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.15em;color:var(--green);margin-bottom:14px}
.exp-head h2{font-family:'Instrument Serif',serif;font-size:clamp(32px,3.8vw,48px);font-weight:400;letter-spacing:-.02em;line-height:1.08;margin-bottom:16px}
.exp-head h2 em{font-style:italic;color:var(--green)}
.exp-head p{font-size:15px;color:var(--t2);line-height:1.6}

.scrwrap{margin-bottom:56px}
.scrwrap:last-child{margin-bottom:0}
.scr-head{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:20px;gap:20px;padding:0 32px;max-width:1240px;margin-left:auto;margin-right:auto}
.scr-head .l{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.15em;color:var(--green);margin-bottom:6px}
.scr-head h3{font-family:'Instrument Serif',serif;font-size:clamp(22px,2.4vw,30px);font-weight:400;letter-spacing:-.01em;line-height:1.15}
.scr-head h3 em{font-style:italic}
.scr-head .vall{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--green);text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:gap .3s;white-space:nowrap}
.scr-head .vall:hover{gap:10px}
.scr-track{display:flex;gap:14px;overflow-x:auto;padding:6px 32px 16px;scroll-snap-type:x mandatory;scroll-padding:32px;-ms-overflow-style:none;scrollbar-width:none;max-width:1240px;margin:0 auto}
.scr-track::-webkit-scrollbar{display:none}

/* Horizontal card style — image left, content right */
.hc{flex:0 0 380px;scroll-snap-align:start;background:var(--bg-card);border:1px solid var(--brd);border-radius:14px;text-decoration:none;color:var(--text);transition:all .3s;display:flex;overflow:hidden;min-height:140px}
.hc:hover{transform:translateY(-2px);border-color:var(--sage-d);background:var(--sage-s)}
.hc-img{flex:0 0 130px;background:linear-gradient(135deg,var(--sage-s),var(--bg2));display:flex;align-items:center;justify-content:center;position:relative;border-right:1px solid var(--brd);overflow:hidden}
.hc-img img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0;z-index:0}
.hc:hover .hc-img{border-color:var(--sage)}
.hc-img .ic{font-size:42px;opacity:.18;color:var(--green);transition:opacity .3s}
.hc:hover .hc-img .ic{opacity:.3}
.hc-tag{position:absolute;top:10px;left:10px;background:var(--green-b);color:white;font-family:'DM Mono',monospace;font-size:8px;text-transform:uppercase;letter-spacing:.08em;padding:3px 8px;border-radius:4px;font-weight:500}
.hc-tag.alt{background:var(--dark);color:var(--ti)}
.hc-tag.warn{background:#d4a94c;color:white}
.hc-body{flex:1;padding:18px 20px;display:flex;flex-direction:column;justify-content:space-between;min-width:0}
.hc-name{font-size:16px;font-weight:500;margin-bottom:3px;letter-spacing:-.005em}
.hc-spec{font-size:11px;color:var(--t3);font-family:'DM Mono',monospace;letter-spacing:.04em;margin-bottom:10px}
.hc-foot{display:flex;justify-content:space-between;align-items:center;margin-top:auto}
.hc-price{font-family:'DM Mono',monospace;font-size:14px;font-weight:500;color:var(--green)}
.hc-stock{font-family:'DM Mono',monospace;font-size:9px;text-transform:uppercase;letter-spacing:.1em;color:var(--t3)}
.hc-stock.low{color:#b8860b}


@media(max-width:1024px){
.fr{position:relative;top:0!important;grid-template-columns:1fr;gap:40px;margin-top:24px!important;padding:36px 28px;min-height:auto;opacity:.4;transform:translateY(40px) scale(.96);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1);box-shadow:0 8px 24px rgba(26,30,28,.04)}
.fr.in-view{opacity:1;transform:translateY(0) scale(1);box-shadow:0 20px 50px rgba(26,30,28,.10)}
.fr:nth-child(1){margin-top:0!important}
.fv{height:340px}
.pg{grid-template-columns:repeat(2,1fr)}
.sc-row{grid-template-columns:1fr}
.bqi{grid-template-columns:1fr;gap:40px}
.fs-i{grid-template-columns:1fr;gap:40px}
.fs::before{display:none}
.fs-v{height:380px}
.cgrid{grid-template-columns:repeat(3,1fr);gap:12px}
.cc{padding:26px 22px;min-height:200px}
.cc-name{font-size:22px}
}
@media(max-width:768px){
.container{padding:0 20px}nav{padding:0 20px}.nc{display:none}
.pg{grid-template-columns:1fr}
.cgrid{grid-template-columns:1fr;gap:10px}

/* Top 3 goal cards — compact pill style on mobile */
.cc{padding:18px 22px;min-height:0;flex-direction:row;align-items:center;justify-content:space-between;gap:14px}
.cc > div:first-child{display:flex;align-items:center;gap:14px;flex:1;min-width:0}
.cc-icon{margin-bottom:0;width:36px;height:36px;flex-shrink:0}
.cc-icon svg{width:18px;height:18px}
.cc-name{font-size:18px;margin-bottom:0;line-height:1.2}
.cc-desc{display:none}
.cc-arrow{margin-top:0;flex-shrink:0;font-size:0;width:32px;height:32px;border-radius:50%;background:rgba(45,106,79,.1);justify-content:center;color:var(--green)}
.cc-arrow::after{content:'→';font-size:14px;display:block}
.cc:hover{transform:translateY(-2px)}

.cats-head{flex-direction:column;align-items:flex-start;margin-bottom:20px}
.cats-head h2{font-size:28px}
.cats-head p{font-size:13px;max-width:none}

.bs-head{padding:0 20px;flex-direction:column;align-items:flex-start}
.bs-track{padding:8px 20px 24px;scroll-padding:20px}
.bsc{flex:0 0 240px}

/* Lower section header — tighter, less padding */
.exp{padding:56px 0}
.exp-head{margin:0 auto 32px;padding:0 24px;max-width:none}
.exp-head .l{font-size:10px;margin-bottom:10px}
.exp-head h2{font-size:30px;margin-bottom:12px;text-wrap:balance}
.exp-head p{font-size:13px;line-height:1.5;text-wrap:balance}

/* Lower scroller rows — compact label + view-all on same line */
.scrwrap{margin-bottom:32px}
.scr-head{padding:0 20px;flex-direction:row;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px}
.scr-head > div:first-child{flex:1;min-width:0}
.scr-head .l{font-size:11px;margin-bottom:0;letter-spacing:.12em}
.scr-head h3{display:none}
.scr-head .vall{font-size:10px}
.scr-track{padding:4px 20px 16px;scroll-padding:20px;gap:12px}
.hc{flex:0 0 300px;min-height:120px}
.hc-img{flex:0 0 100px}
.hc-body{padding:14px 16px}
.hc-name{font-size:15px}
.hc-spec{font-size:10px}

.faq-t{flex-direction:column;gap:16px}
.foot{flex-direction:column;text-align:center}
.hero{min-height:70vh;padding:60px 0 40px}
.hero-bg{background-position:25% bottom !important;background-size:cover !important}
.hero-bg::after{background:linear-gradient(to bottom,rgba(242,240,235,.95) 0%,rgba(242,240,235,.85) 40%,rgba(242,240,235,.3) 70%,rgba(242,240,235,1) 100%) !important}
.feats{padding:60px 0}
.fr{padding:28px 22px}
.fv{height:280px}
.fs{padding:60px 0}
.fs-h{font-size:32px}
.fs-d{font-size:14px}
.fs-price .now{font-size:38px}
.fs-cta{width:100%;justify-content:center}
.fs-i > div:first-child{order:2}
.fs-v{order:1;height:300px;border-radius:18px}
.fs-v-glyph{font-size:160px}
.fs-v::before,.fs-v-tag,.fs-v-meta{font-size:9px}
.fs-v::before{top:20px;left:20px}
.fs-v-tag{top:20px;right:20px;padding:5px 12px}
.fs-v-meta{bottom:20px;left:20px;right:20px}
}

</style>
</head>
<body>

<div class="announce"><strong>10% off</strong> all orders with code <strong>NATTY</strong> at checkout</div>

<nav><div class="nav-i">
<a href="https://nattyvision.com" class="nl"><?php echo nv_get_logo(38); ?></a>
<a href="<?php echo esc_url( wc_get_page_permalink('myaccount') ); ?>" class="nr"><?php echo is_user_logged_in() ? 'My Account' : 'Login'; ?></a>
</div></nav>

<section class="hero">
<div class="hero-bg"></div>
<div class="container"><div class="hero-c">
<h1>99% Purity Peptides <em>Delivered</em> To Your Door</h1>
<p class="hero-s">Scientifically engineered peptides designed to amplify natural growth hormone signaling — supporting accelerated muscle development, enhanced fat metabolism, and faster injury recovery.</p>
<div class="hero-btns"><a href="https://nattyvision.com/shop" class="bd">Learn More</a><a href="https://nattyvision.com/shop" class="bo">Explore the Compounds</a></div>
<p class="hero-p"><strong>+8k</strong> Happy researchers</p>
</div></div>
</section>

<section class="cats an"><div class="container">
<div class="promo">
<span class="pn">10% off</span>
<span class="ps">All orders with code NATTY</span>
<a href="https://nattyvision.com/shop" class="pa">Shop now →</a>
</div>
<div class="cats-head">
<h2>Find your <em>protocol</em></h2>
<p>Browse compounds by research focus — every category links to verified, purity-tested peptides.</p>
</div>
<div class="cgrid">
<a href="<?php echo esc_url( nv_shop_cat_url('weight-loss') ); ?>" class="cc">
<div><div class="cc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg></div><div class="cc-name">Weight Loss</div><div class="cc-desc">Modern weight management with the latest GLP-1 compounds. Curb cravings and reset your metabolism.</div></div>
<div class="cc-arrow">Shop now →</div>
</a>
<a href="<?php echo esc_url( nv_shop_cat_url('energy') ); ?>" class="cc">
<div><div class="cc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg></div><div class="cc-name">Energy</div><div class="cc-desc">More energy, faster recovery, and cellular-level support for daily performance.</div></div>
<div class="cc-arrow">Shop now →</div>
</a>
<a href="<?php echo esc_url( nv_shop_cat_url('stacks') ); ?>" class="cc">
<div><div class="cc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 7l10 5 10-5-10-5z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg></div><div class="cc-name">Stacks</div><div class="cc-desc">Curated peptide combinations for compound effects. Save vs. buying single vials separately.</div></div>
<div class="cc-arrow">Shop now →</div>
</a>
</div>
</div></section>

<section class="bs an" id="bestsellers">
<div class="bs-head">
<div>
<div class="l">Most Researched</div>
<h2>Researcher <em>favourites</em></h2>
</div>
<p>The compounds our customers reorder most. All third-party tested at 99%+ purity.</p>
<div class="bs-nav"><button id="bsPrev" aria-label="Previous">‹</button><button id="bsNext" aria-label="Next">›</button></div>
</div>
<div class="bs-track" id="bsTrack">
<a href="<?php echo esc_url( nv_product_url('Retatrutide') ); ?>" class="bsc"><div class="bsc-img"><?php $img = nv_product_img('Retatrutide'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="Retatrutide"><?php else: ?><div class="ic">⬡</div><?php endif; ?><div class="bsc-tag">New</div></div><div class="bsc-body"><div class="bsc-cat">Weight Loss</div><div class="bsc-name">Retatrutide</div><div class="bsc-spec">10mg vial · 99.4%</div><div class="bsc-foot"><div class="bsc-price"><?php echo esc_html( nv_product_price("Retatrutide") ); ?></div><div class="bsc-rating">★★★★★</div></div></div></a>
<a href="<?php echo esc_url( nv_product_url('Tirzepatide') ); ?>" class="bsc"><div class="bsc-img"><?php $img = nv_product_img('Tirzepatide'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="Tirzepatide"><?php else: ?><div class="ic">⬡</div><?php endif; ?><div class="bsc-tag alt">Top Seller</div></div><div class="bsc-body"><div class="bsc-cat">Weight Loss</div><div class="bsc-name">Tirzepatide</div><div class="bsc-spec">10mg vial · 99.3%</div><div class="bsc-foot"><div class="bsc-price"><?php echo esc_html( nv_product_price("Tirzepatide") ); ?></div><div class="bsc-rating">★★★★★</div></div></div></a>
<a href="<?php echo esc_url( nv_product_url('Semaglutide') ); ?>" class="bsc"><div class="bsc-img"><?php $img = nv_product_img('Semaglutide'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="Semaglutide"><?php else: ?><div class="ic">⬡</div><?php endif; ?></div><div class="bsc-body"><div class="bsc-cat">Weight Loss</div><div class="bsc-name">Semaglutide</div><div class="bsc-spec">10mg vial · 99.2%</div><div class="bsc-foot"><div class="bsc-price"><?php echo esc_html( nv_product_price("Semaglutide") ); ?></div><div class="bsc-rating">★★★★★</div></div></div></a>
<a href="<?php echo esc_url( nv_product_url('Tesamorelin') ); ?>" class="bsc"><div class="bsc-img"><?php $img = nv_product_img('Tesamorelin'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="Tesamorelin"><?php else: ?><div class="ic">⬡</div><?php endif; ?></div><div class="bsc-body"><div class="bsc-cat">Energy</div><div class="bsc-name">Tesamorelin</div><div class="bsc-spec">10mg vial · 99.5%</div><div class="bsc-foot"><div class="bsc-price"><?php echo esc_html( nv_product_price("Tesamorelin") ); ?></div><div class="bsc-rating">★★★★★</div></div></div></a>
<a href="<?php echo esc_url( nv_product_url('MOTS-c') ); ?>" class="bsc"><div class="bsc-img"><?php $img = nv_product_img('MOTS-c'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="MOTS-c"><?php else: ?><div class="ic">⬡</div><?php endif; ?><div class="bsc-tag">New</div></div><div class="bsc-body"><div class="bsc-cat">Energy</div><div class="bsc-name">MOTS-c</div><div class="bsc-spec">10mg vial · 99.1%</div><div class="bsc-foot"><div class="bsc-price"><?php echo esc_html( nv_product_price("MOTS-c") ); ?></div><div class="bsc-rating">★★★★☆</div></div></div></a>
<a href="<?php echo esc_url( nv_product_url('BPC-157') ); ?>" class="bsc"><div class="bsc-img"><?php $img = nv_product_img('BPC-157'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="BPC-157"><?php else: ?><div class="ic">⬡</div><?php endif; ?><div class="bsc-tag alt">Top Seller</div></div><div class="bsc-body"><div class="bsc-cat">Healing</div><div class="bsc-name">BPC-157</div><div class="bsc-spec">5mg vial · 99.3%</div><div class="bsc-foot"><div class="bsc-price"><?php echo esc_html( nv_product_price("BPC-157") ); ?></div><div class="bsc-rating">★★★★★</div></div></div></a>
</div>
</section>

<section class="feats" id="features"><div class="container">
<div class="feats-inner">
<div class="fr"><div class="ft"><div class="fl">Quality Control</div><h2 class="fh">Verified 99%+ Purity</h2><p class="fd">Every batch undergoes rigorous HPLC and MS testing by independent third-party laboratories to guarantee zero fillers and exact dosing for precise research outcomes.</p><a href="https://nattyvision.com/coa-library" class="fk">View Lab Reports →</a></div><div class="fv"><img src="<?php echo plugins_url('images/molecule.png', __FILE__); ?>" alt="99%+ purity molecular verification"></div></div>
<div class="fr"><div class="ft"><div class="fl">Fast Fulfillment</div><h2 class="fh">Climate-Controlled Shipping</h2><p class="fd">Your research shouldn't wait. Orders are processed daily and shipped directly from our secure facilities to ensure your materials arrive fully viable and ready for study.</p><a href="https://nattyvision.com/shipping" class="fk">Shipping Details →</a></div><div class="fv"><img src="<?php echo plugins_url('images/shipping.png', __FILE__); ?>" alt="Active order tracking"></div></div>
<div class="fr"><div class="ft"><div class="fl">Full Transparency</div><h2 class="fh">Verifiable Batch Tracking</h2><p class="fd">Never guess what you're working with. Up-to-date Certificates of Analysis (COAs) are provided for every single batch, ensuring complete visibility on compound identity and purity.</p><a href="https://nattyvision.com/coa-library" class="fk">Browse All COAs →</a></div><div class="fv"><img src="<?php echo plugins_url('images/purity.png', __FILE__); ?>" alt="Batch purity verification"></div></div>
</div>
</div></section>

<section class="stats an" id="results"><div class="container">
<div class="sc-row">
<div class="sc"><div class="sc-t">The choice of thousands</div><div class="sc-s">Over 8,000 researchers trust Natty Vision for premium-grade peptides, SARMs, and research compounds delivered across Canada.</div></div>
<div class="sn"><div class="snv"><span class="b">+4</span><span class="s">x</span></div><div class="snd">Average reorder rate compared to industry average</div></div>
<div class="sn"><div class="snv"><span class="b">99</span><span class="s">%</span></div><div class="snd">Of batches exceed advertised purity thresholds</div></div>
</div></div></section>

<section class="exp an" id="explore">
<div class="exp-head">
<div class="l">Shop the Catalog</div>
<h2>More research-grade <em>compounds</em></h2>
<p>Curated rows from our most popular categories. All third-party tested at 99%+ purity.</p>
</div>

<div class="scrwrap">
<div class="scr-head">
<div><div class="l">Healing</div></div>
<a href="<?php echo esc_url( nv_shop_cat_url('healing') ); ?>" class="vall">View all →</a>
</div>
<div class="scr-track">
<a href="<?php echo esc_url( nv_product_url('BPC-157') ); ?>" class="hc"><div class="hc-img"><?php $img = nv_product_img('BPC-157'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="BPC-157"><?php else: ?><div class="ic">⬡</div><?php endif; ?><div class="hc-tag alt">Top Seller</div></div><div class="hc-body"><div><div class="hc-name">BPC-157</div><div class="hc-spec">5mg vial · 99.3%</div></div><div class="hc-foot"><div class="hc-price"><?php echo esc_html( nv_product_price("BPC-157") ); ?></div><div class="hc-stock">In stock</div></div></div></a>
<a href="<?php echo esc_url( nv_product_url('TB-500') ); ?>" class="hc"><div class="hc-img"><?php $img = nv_product_img('TB-500'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="TB-500"><?php else: ?><div class="ic">⬡</div><?php endif; ?></div><div class="hc-body"><div><div class="hc-name">TB-500</div><div class="hc-spec">5mg vial · 99.1%</div></div><div class="hc-foot"><div class="hc-price"><?php echo esc_html( nv_product_price("TB-500") ); ?></div><div class="hc-stock">In stock</div></div></div></a>
<a href="<?php echo esc_url( nv_product_url('Tesamorelin') ); ?>" class="hc"><div class="hc-img"><?php $img = nv_product_img('Tesamorelin'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="Tesamorelin"><?php else: ?><div class="ic">⬡</div><?php endif; ?></div><div class="hc-body"><div><div class="hc-name">Tesamorelin</div><div class="hc-spec">10mg vial · 99.5%</div></div><div class="hc-foot"><div class="hc-price"><?php echo esc_html( nv_product_price("Tesamorelin") ); ?></div><div class="hc-stock">In stock</div></div></div></a>
<a href="<?php echo esc_url( nv_product_url('KPV') ); ?>" class="hc"><div class="hc-img"><?php $img = nv_product_img('KPV'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="KPV"><?php else: ?><div class="ic">⬡</div><?php endif; ?></div><div class="hc-body"><div><div class="hc-name">KPV</div><div class="hc-spec">10mg vial · 99.0%</div></div><div class="hc-foot"><div class="hc-price"><?php echo esc_html( nv_product_price("KPV") ); ?></div><div class="hc-stock">In stock</div></div></div></a>
</div>
</div>

<div class="scrwrap">
<div class="scr-head">
<div><div class="l">Skin</div></div>
<a href="<?php echo esc_url( nv_shop_cat_url('skin') ); ?>" class="vall">View all →</a>
</div>
<div class="scr-track">
<a href="<?php echo esc_url( nv_product_url('GHK-Cu') ); ?>" class="hc"><div class="hc-img"><?php $img = nv_product_img('GHK-Cu'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="GHK-Cu"><?php else: ?><div class="ic">⬡</div><?php endif; ?></div><div class="hc-body"><div><div class="hc-name">GHK-Cu</div><div class="hc-spec">50mg vial · 99.2%</div></div><div class="hc-foot"><div class="hc-price"><?php echo esc_html( nv_product_price("GHK-Cu") ); ?></div><div class="hc-stock">In stock</div></div></div></a>
<a href="<?php echo esc_url( nv_product_url('Melanotan 2') ); ?>" class="hc"><div class="hc-img"><?php $img = nv_product_img('Melanotan 2'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="Melanotan II"><?php else: ?><div class="ic">⬡</div><?php endif; ?></div><div class="hc-body"><div><div class="hc-name">Melanotan II</div><div class="hc-spec">10mg vial · 99.0%</div></div><div class="hc-foot"><div class="hc-price"><?php echo esc_html( nv_product_price("Melanotan 2") ); ?></div><div class="hc-stock">In stock</div></div></div></a>
</div>
</div>

<div class="scrwrap">
<div class="scr-head">
<div><div class="l">Brain</div></div>
<a href="<?php echo esc_url( nv_shop_cat_url('brain') ); ?>" class="vall">View all →</a>
</div>
<div class="scr-track">
<a href="<?php echo esc_url( nv_product_url('Semax') ); ?>" class="hc"><div class="hc-img"><?php $img = nv_product_img('Semax'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="Semax"><?php else: ?><div class="ic">⬡</div><?php endif; ?></div><div class="hc-body"><div><div class="hc-name">Semax</div><div class="hc-spec">5mg vial · 99.1%</div></div><div class="hc-foot"><div class="hc-price"><?php echo esc_html( nv_product_price("Semax") ); ?></div><div class="hc-stock">In stock</div></div></div></a>
<a href="<?php echo esc_url( nv_product_url('Selank') ); ?>" class="hc"><div class="hc-img"><?php $img = nv_product_img('Selank'); if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="Selank"><?php else: ?><div class="ic">⬡</div><?php endif; ?></div><div class="hc-body"><div><div class="hc-name">Selank</div><div class="hc-spec">5mg vial · 99.0%</div></div><div class="hc-foot"><div class="hc-price"><?php echo esc_html( nv_product_price("Selank") ); ?></div><div class="hc-stock">In stock</div></div></div></a>
</div>
</div>

</section>

<section class="fs an" id="featured-stack"><div class="container"><div class="fs-i">
<div>
<div class="fs-l">★ Featured Blend</div>
<h2 class="fs-h">The KLOW <em>Blend</em></h2>
<p class="fs-d">A four-peptide GH support blend in a single vial. Pre-mixed and pre-dosed — no math, no mixing, no fuss.</p>
<ul class="fs-list">
<li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> CJC-1295 + Ipamorelin + GHRP-2 + GHRP-6</li>
<li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> 80mg per vial &mdash; lyophilized</li>
<li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> Verified at 99.2%+ purity</li>
<li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg> One-step protocol &mdash; skip the math</li>
</ul>
<div class="fs-price">
<span class="now"><?php echo esc_html( nv_product_price("KLOW Blend") ); ?></span>
<span class="unit">per vial</span>
<div class="fs-stock">In stock</div>
</div>
<a href="<?php echo esc_url( nv_product_url('KLOW Blend') ); ?>" class="fs-cta">Add to cart &rarr;</a>

</div>
<div class="fs-v">
<?php $klow_img = nv_product_img('KLOW Blend', 'large'); if ($klow_img): ?>
<img src="<?php echo esc_url($klow_img); ?>" alt="KLOW Blend" class="fs-v-img">
<?php else: ?>
<div class="fs-v-glyph">K</div>
<?php endif; ?>
<div class="fs-v-tag">Blend</div>
<div class="fs-v-meta"><span>BBGK80 &middot; 80mg</span><span class="pur">99.2%+ purity</span></div>
</div>
</div></div></section>

<section class="tst an"><div class="container">
<div class="tst-l">Real stories from real researchers</div>
<div class="ts-mask">
<div class="ts">
<div class="tc"><div class="tq">"Fastest shipping I've experienced from a Canadian peptide supplier. BPC-157 quality is outstanding — came with full COA."</div><div class="tb"><div class="tav a1">MK</div><div><div class="tn">Marcus K.</div><div class="tr">Independent Researcher</div></div></div></div>
<div class="tc"><div class="tq">"The transparency with lab reports sets Natty Vision apart. Purity here is consistently top-tier. Highly recommend."</div><div class="tb"><div class="tav a2">SL</div><div><div class="tn">Sarah L.</div><div class="tr">PhD Candidate</div></div></div></div>
<div class="tc"><div class="tq">"Excellent product range. Customer support was incredibly helpful when I had questions about dosing protocols."</div><div class="tb"><div class="tav a3">JR</div><div><div class="tn">James R.</div><div class="tr">Lab Technician</div></div></div></div>
<div class="tc"><div class="tq">"Switched from a US supplier and the cold chain shipping alone made it worth it. Product arrived in perfect condition."</div><div class="tb"><div class="tav a4">AT</div><div><div class="tn">Alex T.</div><div class="tr">Biotech Founder</div></div></div></div>
<div class="tc"><div class="tq">"Best peptide supplier in Canada. The COAs are legit, pricing is fair, and the BPC+TB combo is exactly what we needed."</div><div class="tb"><div class="tav a5">NK</div><div><div class="tn">Nina K.</div><div class="tr">Research Associate</div></div></div></div>
<div class="tc" aria-hidden="true"><div class="tq">"Fastest shipping I've experienced from a Canadian peptide supplier. BPC-157 quality is outstanding — came with full COA."</div><div class="tb"><div class="tav a1">MK</div><div><div class="tn">Marcus K.</div><div class="tr">Independent Researcher</div></div></div></div>
<div class="tc" aria-hidden="true"><div class="tq">"The transparency with lab reports sets Natty Vision apart. Purity here is consistently top-tier. Highly recommend."</div><div class="tb"><div class="tav a2">SL</div><div><div class="tn">Sarah L.</div><div class="tr">PhD Candidate</div></div></div></div>
<div class="tc" aria-hidden="true"><div class="tq">"Excellent product range. Customer support was incredibly helpful when I had questions about dosing protocols."</div><div class="tb"><div class="tav a3">JR</div><div><div class="tn">James R.</div><div class="tr">Lab Technician</div></div></div></div>
<div class="tc" aria-hidden="true"><div class="tq">"Switched from a US supplier and the cold chain shipping alone made it worth it. Product arrived in perfect condition."</div><div class="tb"><div class="tav a4">AT</div><div><div class="tn">Alex T.</div><div class="tr">Biotech Founder</div></div></div></div>
<div class="tc" aria-hidden="true"><div class="tq">"Best peptide supplier in Canada. The COAs are legit, pricing is fair, and the BPC+TB combo is exactly what we needed."</div><div class="tb"><div class="tav a5">NK</div><div><div class="tn">Nina K.</div><div class="tr">Research Associate</div></div></div></div>
</div>
</div>
</div></div></section>

<section class="faq an" id="faq"><div class="container">
<div class="faq-t"><h2 class="faq-h">Everything you need to know</h2><a href="mailto:support@nattyvision.com" class="faq-c">Contact Us</a></div>
<div>
<div class="fi on"><div class="fiq" onclick="tf(this)">What purity level can I expect?<span class="fia">+</span></div><div class="fians"><p>All our peptides are tested by independent third-party labs and consistently achieve 99%+ purity. Every product ships with a Certificate of Analysis so you can verify the exact purity of your specific batch.</p></div></div>
<div class="fi"><div class="fiq" onclick="tf(this)">How fast is shipping?<span class="fia">+</span></div><div class="fians"><p>We ship same-day for orders placed before 2 PM EST. Standard delivery is 2–4 business days across Canada with cold chain packaging for temperature-sensitive products.</p></div></div>
<div class="fi"><div class="fiq" onclick="tf(this)">Do you provide Certificates of Analysis?<span class="fia">+</span></div><div class="fians"><p>Yes — every single batch. Our COAs include HPLC purity data, mass spectrometry confirmation, and appearance testing. Available on each product page and included with your shipment.</p></div></div>
<div class="fi"><div class="fiq" onclick="tf(this)">What payment methods do you accept?<span class="fia">+</span></div><div class="fians"><p>We accept Visa, Mastercard, American Express, Interac e-Transfer, and cryptocurrency. All payments are processed through encrypted, PCI-compliant systems.</p></div></div>
<div class="fi"><div class="fiq" onclick="tf(this)">What is your return policy?<span class="fia">+</span></div><div class="fians"><p>We offer a 30-day satisfaction guarantee. If you're not happy with your purchase, contact our team for a hassle-free return or replacement.</p></div></div>
</div></div></section>

<section class="cta an"><div class="container">
<div class="cta-l">Natty Vision</div>
<h2>From Lab to <em>Breakthrough</em></h2>
<a href="https://nattyvision.com/shop" class="bd">Shop All Peptides</a>
</div></section>

<footer><div class="container"><div class="foot">
<a href="https://nattyvision.com" style="opacity:.5"><?php echo nv_get_logo(30); ?></a>
<div class="flinks"><a href="https://nattyvision.com/shop">Shop</a><a href="https://nattyvision.com/shop">Products</a><a href="#results">Results</a><a href="#faq">FAQ</a><a href="https://nattyvision.com/privacy-policy">Privacy Policy</a></div>
<div class="fcopy">©2026 Natty Vision. All rights reserved. For research purposes only.</div>
</div></div></footer>

<script>
const o=new IntersectionObserver(e=>{e.forEach(n=>{if(n.isIntersecting)n.target.classList.add('v')})},{threshold:.06});
document.querySelectorAll('.an').forEach(e=>o.observe(e));
function tf(e){const i=e.parentElement,w=i.classList.contains('on');document.querySelectorAll('.fi').forEach(x=>x.classList.remove('on'));if(!w)i.classList.add('on')}

// Chat bubbles - stagger on scroll
const chatObs=new IntersectionObserver(e=>{e.forEach(n=>{if(n.isIntersecting){const bubbles=n.target.querySelectorAll('.chat-b');const delays=[0,1200,2400,3600];bubbles.forEach((b,i)=>{setTimeout(()=>b.classList.add('show'),delays[i])});chatObs.unobserve(n.target)}})},{threshold:.3});
const chatEl=document.querySelector('.chat-bubbles');
if(chatEl)chatObs.observe(chatEl);

// Bestseller carousel arrows
const bsTrack=document.getElementById('bsTrack');
const bsPrev=document.getElementById('bsPrev');
const bsNext=document.getElementById('bsNext');
function updateBsNav(){if(!bsTrack)return;const max=bsTrack.scrollWidth-bsTrack.clientWidth-2;bsPrev.disabled=bsTrack.scrollLeft<=2;bsNext.disabled=bsTrack.scrollLeft>=max}
if(bsTrack&&bsPrev&&bsNext){
  const step=296;
  bsPrev.addEventListener('click',()=>bsTrack.scrollBy({left:-step,behavior:'smooth'}));
  bsNext.addEventListener('click',()=>bsTrack.scrollBy({left:step,behavior:'smooth'}));
  bsTrack.addEventListener('scroll',updateBsNav,{passive:true});
  window.addEventListener('resize',updateBsNav);
  updateBsNav();
}

// Animated number counters in stats
function animateCounter(el,target,duration=1600){
  const start=performance.now();
  const isPlus=String(target).startsWith('+');
  const num=parseInt(String(target).replace(/[^0-9]/g,''));
  function frame(t){
    const p=Math.min((t-start)/duration,1);
    const eased=1-Math.pow(1-p,3); // easeOutCubic
    const v=Math.floor(num*eased);
    el.textContent=(isPlus?'+':'')+v;
    if(p<1)requestAnimationFrame(frame);else el.textContent=target;
  }
  requestAnimationFrame(frame);
}
const counterObs=new IntersectionObserver(e=>{
  e.forEach(n=>{
    if(n.isIntersecting&&!n.target.dataset.counted){
      n.target.dataset.counted='1';
      const orig=n.target.textContent.trim();
      animateCounter(n.target,orig);
    }
  });
},{threshold:.5});
document.querySelectorAll('.snv .b').forEach(el=>counterObs.observe(el));

// Magnetic effect on primary buttons (subtle, keeps CSS hover)
document.querySelectorAll('.bd,.bo,.bs-nav button,.fs-cta').forEach(btn=>{
  btn.addEventListener('mousemove',e=>{
    const r=btn.getBoundingClientRect();
    const x=(e.clientX-r.left-r.width/2)*.15;
    const y=(e.clientY-r.top-r.height/2)*.15;
    btn.style.setProperty('--mx',x+'px');
    btn.style.setProperty('--my',y+'px');
  });
  btn.addEventListener('mouseleave',()=>{
    btn.style.setProperty('--mx','0px');
    btn.style.setProperty('--my','0px');
  });
});

// Hero parallax — subtle background drift on scroll
const heroBg=document.querySelector('.hero-bg');
if(heroBg){
  window.addEventListener('scroll',()=>{
    const y=window.scrollY;
    if(y<800)heroBg.style.transform=`translateY(${y*.25}px)`;
  },{passive:true});
}

// Feature rows — desktop: sticky stack scale-fade. Mobile: scroll-in animation.
const featRows=document.querySelectorAll('.feats .fr');
function isMobileFeats(){return window.innerWidth<=1024}

function updateFeaturesDesktop(){
  featRows.forEach((row,i)=>{
    const r=row.getBoundingClientRect();
    const next=featRows[i+1];
    if(next){
      const nr=next.getBoundingClientRect();
      const overlap=Math.max(0,Math.min(1,(r.bottom-nr.top+60)/200));
      const scale=1-overlap*.04;
      const opacity=1-overlap*.3;
      row.style.transform=`scale(${scale})`;
      row.style.opacity=opacity;
    }else{
      row.style.transform='';
      row.style.opacity='';
    }
  });
}
function clearFeatureStyles(){
  featRows.forEach(r=>{r.style.transform='';r.style.opacity=''});
}

// Mobile: trigger in-view class as each row enters viewport
let mobileObs=null;
function setupMobileFeatureObserver(){
  if(mobileObs)mobileObs.disconnect();
  mobileObs=new IntersectionObserver((entries)=>{
    entries.forEach(e=>{
      if(e.isIntersecting)e.target.classList.add('in-view');
      else e.target.classList.remove('in-view');
    });
  },{threshold:.25,rootMargin:'0px 0px -10% 0px'});
  featRows.forEach(r=>mobileObs.observe(r));
}
function teardownMobileFeatureObserver(){
  if(mobileObs){mobileObs.disconnect();mobileObs=null}
  featRows.forEach(r=>r.classList.remove('in-view'));
}

function activateFeatureMode(){
  if(isMobileFeats()){
    clearFeatureStyles();
    setupMobileFeatureObserver();
    window.removeEventListener('scroll',updateFeaturesDesktop);
  }else{
    teardownMobileFeatureObserver();
    window.addEventListener('scroll',updateFeaturesDesktop,{passive:true});
    updateFeaturesDesktop();
  }
}
if(featRows.length){
  activateFeatureMode();
  let resizeTimer;
  window.addEventListener('resize',()=>{
    clearTimeout(resizeTimer);
    resizeTimer=setTimeout(activateFeatureMode,150);
  });
}
</script>
</body>
</html>
