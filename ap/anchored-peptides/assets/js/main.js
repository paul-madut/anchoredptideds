/* Anchored Peptides — front-end interactions */
(function () {
    'use strict';
    document.addEventListener('DOMContentLoaded', function () {

        /* ---- Product tabs ---- */
        var tabBtns = document.querySelectorAll('.ap-tabs-nav button');
        tabBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                tabBtns.forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');
                document.querySelectorAll('.ap-tab-panel').forEach(function (p) { p.classList.remove('active'); });
                var panel = document.getElementById('ap-tab-' + btn.dataset.tab);
                if (panel) panel.classList.add('active');
            });
        });

        /* ---- FAQ accordion ---- */
        document.querySelectorAll('.ap-acc-head').forEach(function (head) {
            head.addEventListener('click', function () {
                head.parentElement.classList.toggle('open');
            });
        });

        /* ---- Variant (SIZE) pills ---- */
        var pills      = document.querySelectorAll('.ap-variant-pill');
        var priceEl    = document.querySelector('[data-ap-price]');
        var activeEl   = document.querySelector('[data-ap-active]');
        var stockWrap  = document.querySelector('[data-ap-stock]');
        var stockText  = document.querySelector('[data-ap-stock-text]');
        var block      = document.querySelector('.ap-variant-block');
        var unit       = (block && block.getAttribute('data-ap-unit')) || 'mg';

        function money(n) { return '$' + parseFloat(n).toFixed(2); }

        function setStock(state) {
            if (!stockWrap) return;
            stockWrap.classList.remove('low', 'out');
            var txt = 'In stock';
            if (state === 'out') { stockWrap.classList.add('out'); txt = 'Out of stock'; }
            else if (state === 'low') { stockWrap.classList.add('low'); txt = 'Only a few left — selling fast'; }
            if (stockText) stockText.textContent = txt;
        }

        // Sync WooCommerce's hidden variations form so add-to-cart uses the right variation.
        function syncWooVariation(variationId, attributes) {
            var form = document.querySelector('form.variations_form');
            if (!form) return;
            try {
                var idInput = form.querySelector('input[name="variation_id"]');
                if (idInput) idInput.value = variationId || '';
                if (attributes) {
                    Object.keys(attributes).forEach(function (key) {
                        var sel = form.querySelector('[name="' + key + '"]');
                        if (sel) { sel.value = attributes[key]; sel.dispatchEvent(new Event('change', { bubbles: true })); }
                    });
                }
                if (window.jQuery) { window.jQuery(form).trigger('woocommerce_variation_select_change'); window.jQuery(form).trigger('check_variations'); }
            } catch (e) {}
        }

        pills.forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (btn.hasAttribute('disabled')) return;
                pills.forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');

                var mg = btn.dataset.mg, price = btn.dataset.price, stock = btn.dataset.stock;
                if (priceEl && price) priceEl.textContent = money(price);
                if (activeEl && mg) activeEl.textContent = mg + unit;
                setStock(stock);

                var attrs = {};
                try { attrs = JSON.parse(btn.dataset.attributes || '{}'); } catch (e) {}
                syncWooVariation(btn.dataset.variationId, attrs);

                // Reflect price on the add-to-cart button if present.
                var cartBtn = document.querySelector('.ap-cart-row .single_add_to_cart_button, .ap-cart-row button[type="submit"]');
                if (cartBtn && price) {
                    var base = cartBtn.getAttribute('data-label') || 'Add to Cart';
                    cartBtn.setAttribute('data-label', base);
                }
            });
        });

        // Ensure the default-active pill syncs Woo on load.
        var active = document.querySelector('.ap-variant-pill.active');
        if (active) { setTimeout(function () { active.click(); }, 250); }

        /* ---- Quantity stepper (if theme renders one) ---- */
        document.querySelectorAll('.ap-qty').forEach(function (wrap) {
            var input = wrap.querySelector('input');
            var btns  = wrap.querySelectorAll('button');
            if (!input || btns.length < 2) return;
            btns[0].addEventListener('click', function () { input.value = Math.max(1, (parseInt(input.value, 10) || 1) - 1); input.dispatchEvent(new Event('change', { bubbles: true })); });
            btns[1].addEventListener('click', function () { input.value = (parseInt(input.value, 10) || 1) + 1; input.dispatchEvent(new Event('change', { bubbles: true })); });
        });
    });
})();
