/**
 * Saudi Riyal Currency Converter Module
 * 
 * @category  zone99
 * @package   zone99_SARSymbol
 * @author    Saudi Riyal Currency Extension
 * @copyright Copyright (c) 2024
 */

define([
    'jquery',
    'domReady!'
], function($) {
    'use strict';

    /**
     * Saudi Riyal Currency Converter
     */
    var SaudiRiyalConverter = {
        
        /**
         * Configuration
         */
        config: {
            sarSymbols: ['﷼', 'SAR', 'ر.س'],
            customSymbol: '<span class="saudi-riyal-symbol">&#xE900;</span>',
            priceSelectors: [
                '.price',
                '.price-box',
                '.price-wrapper',
                '.product-price',
                '.cart-summary',
                '.checkout-summary',
                '.minicart',
                '.totals',
                '.amount'
            ],
            containerClass: 'saudi-riyal-currency',
            // Patterns to clean up periods and RTL marks after symbols
            cleanupPatterns: [
                /\.‏/g,           // Period followed by RTL mark
                /\s*‏\s*/g,       // RTL marks with optional spaces
                /(\s*\.\s*)+$/g   // Trailing periods with spaces
            ]
        },

        /**
         * Initialize converter
         */
        init: function() {
            this.convertSymbols();
            this.bindEvents();
            this.setupMutationObserver();
        },

        /**
         * Convert SAR symbols to custom symbols
         */
        convertSymbols: function() {
            var self = this;
            
            $(self.config.priceSelectors.join(', ')).each(function() {
                self.replaceSymbolsInElement(this);
                $(this).addClass(self.config.containerClass);
            });
            
            // Additional cleanup pass to remove any remaining periods after symbols
            self.cleanupPeriodsAfterSymbols();
        },

        /**
         * Replace symbols in a specific element
         */
        replaceSymbolsInElement: function(element) {
            var self = this;
            var $element = $(element);
            
            // Skip if already processed
            if ($element.data('sar-processed')) {
                return;
            }
            
            var html = $element.html();
            var originalHtml = html;
            
            // Replace each SAR symbol with variations including periods
            self.config.sarSymbols.forEach(function(symbol) {
                var patterns = [
                    symbol + '.‏',      // Symbol with period and RTL mark
                    symbol + '.',       // Symbol with period
                    symbol + '&nbsp;.', // Symbol with non-breaking space and period
                    symbol + ' .',      // Symbol with space and period
                    symbol              // Just the symbol
                ];
                
                patterns.forEach(function(pattern) {
                    var regex = new RegExp(self.escapeRegExp(pattern), 'g');
                    html = html.replace(regex, self.config.customSymbol);
                });
            });
            
            // Clean up any remaining unwanted characters after symbol replacement
            self.config.cleanupPatterns.forEach(function(pattern) {
                html = html.replace(pattern, '');
            });
            
            // Clean up periods immediately after our custom symbol spans - target the exact pattern
            html = html.replace(
                /<span class="saudi-riyal-symbol">&#xE900;<\/span>\.‏?/g,
                self.config.customSymbol
            );
            
            // Also handle any variation of periods after the symbol
            html = html.replace(
                /<span class="saudi-riyal-symbol">[^<]*<\/span>\s*\.+\s*‏?/g,
                self.config.customSymbol
            );
            
            // Update only if changes were made
            if (html !== originalHtml) {
                $element.html(html);
                $element.addClass(self.config.containerClass);
                $element.data('sar-processed', true);
            }
        },

        /**
         * Escape special regex characters
         */
        escapeRegExp: function(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        },

        /**
         * Bind events for dynamic content
         */
        bindEvents: function() {
            var self = this;
            
            // Handle AJAX content updates
            $(document).on('contentUpdated', function() {
                setTimeout(function() {
                    self.convertSymbols();
                }, 100);
            });
            
            // Handle price updates
            $(document).on('updatePrice', function() {
                setTimeout(function() {
                    self.convertSymbols();
                }, 100);
            });
            
            // Handle cart updates
            $(document).on('ajaxComplete', function(event, xhr, settings) {
                if (settings.url && self.isCartRelatedUrl(settings.url)) {
                    setTimeout(function() {
                        self.convertSymbols();
                    }, 500);
                }
            });
        },

        /**
         * Check if URL is cart-related
         */
        isCartRelatedUrl: function(url) {
            var cartUrls = [
                'checkout/cart',
                'checkout/sidebar',
                'customer/section/load'
            ];
            
            return cartUrls.some(function(cartUrl) {
                return url.indexOf(cartUrl) !== -1;
            });
        },

        /**
         * Setup mutation observer for dynamic content
         */
        setupMutationObserver: function() {
            var self = this;
            
            if (!window.MutationObserver) {
                return;
            }
            
            var observer = new MutationObserver(function(mutations) {
                var shouldConvert = false;
                
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        for (var i = 0; i < mutation.addedNodes.length; i++) {
                            var node = mutation.addedNodes[i];
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                // Check if added node contains price elements
                                var hasPriceElements = self.config.priceSelectors.some(function(selector) {
                                    return $(node).find(selector).length > 0 || 
                                           $(node).is(selector);
                                });
                                
                                if (hasPriceElements) {
                                    shouldConvert = true;
                                    break;
                                }
                            }
                        }
                    }
                });
                
                if (shouldConvert) {
                    setTimeout(function() {
                        self.convertSymbols();
                    }, 100);
                }
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        },

        /**
         * Manual conversion for specific elements
         */
        convertElement: function(element) {
            this.replaceSymbolsInElement(element);
        },

        /**
         * Clean up periods that appear after custom symbols
         */
        cleanupPeriodsAfterSymbols: function() {
            var self = this;
            
            // Find all elements containing our custom symbol
            $('span.saudi-riyal-symbol').each(function() {
                var $symbolSpan = $(this);
                var $parent = $symbolSpan.parent();
                
                // Get the parent's HTML and clean it up
                var html = $parent.html();
                var originalHtml = html;
                
                // Remove periods that appear immediately after our symbol spans
                html = html.replace(
                    /<span class="saudi-riyal-symbol">&#xE900;<\/span>\.‏?/g,
                    '<span class="saudi-riyal-symbol">&#xE900;</span>'
                );
                
                // Remove periods with RTL marks
                html = html.replace(
                    /<span class="saudi-riyal-symbol">&#xE900;<\/span>\s*\.\s*‏/g,
                    '<span class="saudi-riyal-symbol">&#xE900;</span>'
                );
                
                // Remove just periods after the span
                html = html.replace(
                    /<span class="saudi-riyal-symbol">&#xE900;<\/span>\s*\./g,
                    '<span class="saudi-riyal-symbol">&#xE900;</span>'
                );
                
                if (html !== originalHtml) {
                    $parent.html(html);
                }
            });
        },

        /**
         * Get custom symbol HTML
         */
        getCustomSymbol: function() {
            return this.config.customSymbol;
        }
    };

    // Auto-initialize when DOM is ready
    $(function() {
        SaudiRiyalConverter.init();
    });

    // Return module for manual usage
    return SaudiRiyalConverter;
}); 