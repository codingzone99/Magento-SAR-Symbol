<?php
/**
 * zone99_SARSymbol Block Currency
 * 
 * @category  zone99
 * @package   zone99_SARSymbol
 * @author    Saudi Riyal Currency Extension
 * @copyright Copyright (c) 2024
 */

namespace zone99\SARSymbol\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use zone99\SARSymbol\Helper\Data as CurrencyHelper;

class Currency extends Template
{
    /**
     * @var CurrencyHelper
     */
    protected $currencyHelper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param CurrencyHelper $currencyHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CurrencyHelper $currencyHelper,
        array $data = []
    ) {
        $this->currencyHelper = $currencyHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get currency helper
     *
     * @return CurrencyHelper
     */
    public function getCurrencyHelper()
    {
        return $this->currencyHelper;
    }

    /**
     * Check if current currency is SAR
     *
     * @return bool
     */
    public function isSaudiRiyal()
    {
        return $this->currencyHelper->isSaudiRiyal();
    }

    /**
     * Get custom SAR symbol HTML
     *
     * @return string
     */
    public function getCustomSymbolHtml()
    {
        return $this->currencyHelper->getCustomSymbolHtml();
    }

    /**
     * Convert price HTML with custom symbols
     *
     * @param string $priceHtml
     * @return string
     */
    public function convertPriceHtml($priceHtml)
    {
        return $this->currencyHelper->convertPriceHtml($priceHtml);
    }

    /**
     * Add Saudi Riyal CSS class
     *
     * @param string $originalClass
     * @return string
     */
    public function addSaudiRiyalClass($originalClass = '')
    {
        return $this->currencyHelper->addSaudiRiyalClass($originalClass);
    }

    /**
     * Should render block
     *
     * @return bool
     */
    protected function _toHtml()
    {
        // Only render if SAR currency is active
        if (!$this->isSaudiRiyal()) {
            return '';
        }
        
        return parent::_toHtml();
    }
} 