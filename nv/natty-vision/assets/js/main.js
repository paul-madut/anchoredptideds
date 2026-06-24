/* Natty Vision frontend JS */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        // ---- Pill tabs ----
        var tabLis = document.querySelectorAll('.nv-tabs-list li');
        tabLis.forEach(function (li) {
            li.addEventListener('click', function () {
                var tabId = li.getAttribute('data-tab');
                tabLis.forEach(function (t) { t.classList.remove('active'); });
                li.classList.add('active');
                document.querySelectorAll('.nv-tab-panel').forEach(function (p) { p.classList.remove('active'); });
                var target = document.getElementById('nv-tab-' + tabId);
                if (target) target.classList.add('active');
            });
        });

        // ---- Variant pills ----
        var variantPills = document.querySelectorAll('.nv-variant-pill');
        var priceEl     = document.querySelector('[data-nv-price]');
        var skuEl       = document.querySelector('[data-nv-sku]');
        var mgEls       = document.querySelectorAll('[data-nv-mg]');
        var activeLabel = document.querySelector('[data-nv-active-label]');
        var stockWrap   = document.querySelector('[data-nv-stock]');
        var stockText   = document.querySelector('[data-nv-stock-text]');

        var variantBlock = document.querySelector('.nv-variant-block');
        var unit         = (variantBlock && variantBlock.getAttribute('data-nv-unit')) || 'mg';

        function updateStock(state) {
            if (!stockWrap) return;
            stockWrap.classList.remove('nv-stock-in', 'nv-stock-out', 'nv-stock-low');
            if (state === 'out') {
                stockWrap.classList.add('nv-stock-out');
                if (stockText) stockText.textContent = 'Out of stock';
            } else if (state === 'low') {
                stockWrap.classList.add('nv-stock-low');
                if (stockText) stockText.textContent = 'Backorder';
            } else {
                stockWrap.classList.add('nv-stock-in');
                if (stockText) stockText.textContent = 'In stock';
            }
        }

        function selectWooVariation(variationId, attributes, $form) {
            // Set hidden variation_id input directly
            var $varInput = $form.find('input[name="variation_id"]');
            if ($varInput.length) {
                $varInput.val(variationId).trigger('change');
            }

            // Apply each attribute selector
            if (attributes && typeof attributes === 'object') {
                Object.keys(attributes).forEach(function (attrKey) {
                    var $select = $form.find('select[name="' + attrKey + '"]');
                    if ($select.length) {
                        $select.val(attributes[attrKey]).trigger('change');
                    }
                });
            }

            // Trigger Woo's "found_variation" so price/UI updates
            try {
                $form.trigger('woocommerce_variation_select_change');
                $form.trigger('check_variations');
            } catch (e) {}
        }

        variantPills.forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (btn.disabled) return;
                variantPills.forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');

                var mg = btn.getAttribute('data-mg');
                var mgLabel = btn.getAttribute('data-mg-label') || (mg + unit);
                var price = parseFloat(btn.getAttribute('data-price') || '0');
                var sku = btn.getAttribute('data-sku') || '';
                var stock = btn.getAttribute('data-stock') || 'in';
                var variationId = parseInt(btn.getAttribute('data-variation-id') || '0', 10);
                var attributesRaw = btn.getAttribute('data-attributes') || '{}';
                var attributes = {};
                try { attributes = JSON.parse(attributesRaw); } catch (e) {}

                if (priceEl && !isNaN(price) && price > 0) {
                    priceEl.textContent = '$' + price.toFixed(2);
                }
                if (skuEl) {
                    skuEl.textContent = sku ? sku + ' · ' + mgLabel : mgLabel;
                }
                mgEls.forEach(function (el) { el.textContent = mgLabel; });
                if (activeLabel) activeLabel.textContent = mgLabel + ' per vial';

                updateStock(stock);

                // Swap product image if variation has its own image
                if (variationId > 0 && window.jQuery) {
                    var $form = window.jQuery('form.variations_form');
                    if ($form.length) {
                        selectWooVariation(variationId, attributes, $form);
                        // Find variation image from Woo's variation data
                        var variations = $form.data('product_variations') || [];
                        for (var i = 0; i < variations.length; i++) {
                            if (variations[i].variation_id === variationId && variations[i].image) {
                                var imgData = variations[i].image;
                                var galleryImg = document.querySelector('.nv-gallery-card .nv-gallery-img');
                                if (!galleryImg) galleryImg = document.querySelector('.nv-gallery-card img');
                                if (galleryImg && imgData.src) {
                                    galleryImg.src = imgData.src;
                                    galleryImg.srcset = imgData.srcset || '';
                                    galleryImg.alt = imgData.alt || '';
                                }
                                break;
                            }
                        }
                    }
                }
            });
        });

        // Auto-select the first active pill on page load to ensure Woo form syncs
        var activePill = document.querySelector('.nv-variant-pill.active');
        if (activePill) {
            // Slight delay to ensure jQuery + Woo variations script has initialized
            setTimeout(function () {
                activePill.click();
                // Re-set active class since click handler clears it
                variantPills.forEach(function (b) { b.classList.remove('active'); });
                activePill.classList.add('active');
            }, 100);
        }
    });
})();
