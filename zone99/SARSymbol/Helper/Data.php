<?php
/**
 * zone99_SARSymbol Helper Data
 * 
 * @category  zone99
 * @package   zone99_SARSymbol
 * @author    Saudi Riyal Currency Extension
 * @copyright Copyright (c) 2024
 */

namespace zone99\SARSymbol\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Data extends AbstractHelper
{
    /**
     * Custom Saudi Riyal Symbol Unicode
     */
    const CUSTOM_SAR_SYMBOL = '&#xE900;';
    
    /**
     * Standard SAR Symbols to Replace
     */
    const STANDARD_SAR_SYMBOLS = ['﷼', 'SAR', 'ر.س'];
    
    /**
     * SAR Currency Code
     */
    const SAR_CURRENCY_CODE = 'SAR';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Constructor
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param CurrencyFactory $currencyFactory
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context);
    }

    /**
     * Check if module is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            'currency/saudi_riyal/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if current currency is SAR
     *
     * @return bool
     */
    public function isSaudiRiyal()
    {
        try {
            $currentCurrency = $this->storeManager->getStore()->getCurrentCurrencyCode();
            return $currentCurrency === self::SAR_CURRENCY_CODE;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Replace standard SAR symbols with custom symbol
     *
     * @param string $text
     * @return string
     */
    public function replaceSymbol($text)
    {
        if (!$this->isSaudiRiyal() || empty($text)) {
            return $text;
        }

        // Replace each standard SAR symbol with custom symbol wrapped in span
        foreach (self::STANDARD_SAR_SYMBOLS as $standardSymbol) {
            // Also handle symbols followed by periods or other punctuation
            $patterns = [
                $standardSymbol . '.',
                $standardSymbol . '.‏',
                $standardSymbol . '&nbsp;.',
                $standardSymbol . ' .',
                $standardSymbol
            ];
            
            foreach ($patterns as $pattern) {
                $text = str_replace(
                    $pattern,
                    $this->getCustomSymbolHtml(),
                    $text
                );
            }
        }

        return $text;
    }

    /**
     * Get custom symbol HTML with proper CSS class
     *
     * @return string
     */
    public function getCustomSymbolHtml()
    {
        return '<span class="saudi-riyal-symbol">' . self::CUSTOM_SAR_SYMBOL . '</span>';
    }

    /**
     * Format currency with custom SAR symbol
     *
     * @param float $amount
     * @param bool $includeContainer
     * @param int|null $precision
     * @return string
     */
    public function formatCurrency($amount, $includeContainer = true, $precision = null)
    {
        if (!$this->isSaudiRiyal()) {
            return $this->priceCurrency->format($amount, $includeContainer, $precision);
        }

        // Get formatted price without currency symbol
        $formattedPrice = $this->priceCurrency->format(
            $amount,
            $includeContainer,
            $precision,
            null,
            null
        );

        // Replace standard symbols with custom symbol
        return $this->replaceSymbol($formattedPrice);
    }

    /**
     * Get currency symbol for SAR
     *
     * @param string $currencyCode
     * @return string
     */
    public function getCurrencySymbol($currencyCode = null)
    {
        if (!$currencyCode) {
            $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        }

        if ($currencyCode === self::SAR_CURRENCY_CODE) {
            return $this->getCustomSymbolHtml();
        }

        // Return default symbol for other currencies
        $currency = $this->currencyFactory->create()->load($currencyCode);
        return $currency->getCurrencySymbol();
    }

    /**
     * Convert price text to use custom SAR symbol
     *
     * @param string $priceHtml
     * @return string
     */
    public function convertPriceHtml($priceHtml)
    {
        if (!$this->isSaudiRiyal() || empty($priceHtml)) {
            return $priceHtml;
        }

        // Use regex to find and replace currency symbols while preserving HTML structure
        $patterns = [
            '/﷼\.?\s*‏?/',        // Handle SAR symbol with optional period and RTL mark
            '/SAR\.?\s*‏?/',       // Handle SAR text with optional period and RTL mark
            '/ر\.س\.?\s*‏?/',      // Handle Arabic abbreviation with optional period and RTL mark
            '/﷼/',
            '/SAR\s*/',
            '/ر\.س\s*/'
        ];

        foreach ($patterns as $pattern) {
            $priceHtml = preg_replace($pattern, $this->getCustomSymbolHtml(), $priceHtml);
        }

        // Clean up any remaining periods immediately after our custom symbol
        $priceHtml = preg_replace(
            '/<span class="saudi-riyal-symbol">[^<]*<\/span>\.+\s*‏?/',
            $this->getCustomSymbolHtml(),
            $priceHtml
        );
        
        // Handle empty symbol spans with periods (like in user's example)
        $priceHtml = preg_replace(
            '/<span class="saudi-riyal-symbol"><\/span>\.‏?/',
            $this->getCustomSymbolHtml(),
            $priceHtml
        );
        
        // Ensure all symbol spans have content
        $priceHtml = preg_replace(
            '/<span class="saudi-riyal-symbol"><\/span>/',
            $this->getCustomSymbolHtml(),
            $priceHtml
        );

        return $priceHtml;
    }

    /**
     * Add CSS class to price containers for SAR currency
     *
     * @param string $originalClass
     * @return string
     */
    public function addSaudiRiyalClass($originalClass = '')
    {
        if (!$this->isSaudiRiyal()) {
            return $originalClass;
        }

        $additionalClass = 'saudi-riyal-currency';
        
        if (empty($originalClass)) {
            return $additionalClass;
        }

        return $originalClass . ' ' . $additionalClass;
    }
} 