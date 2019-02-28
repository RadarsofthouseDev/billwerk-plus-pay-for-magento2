<?php

namespace Radarsofthouse\Reepay\Block;

/**
 * Class Paymenticons
 *
 * @package Radarsofthouse\Reepay\Block
 */
class Paymenticons extends \Magento\Framework\View\Element\Template
{
    protected $scopeConfig;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }

    /**
     * get payment icons for credit card payment
     *
     * @return array $paymentIcons
     */
    public function getPaymentIcons()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $paymentIconsConfig = $this->scopeConfig->getValue('payment/reepay_payment/payment_icons', $storeScope);
        $paymentIcons = explode(',', $paymentIconsConfig);

        return $paymentIcons;
    }

    /**
     * get Viabill payment icon
     *
     * @return array $paymentIcon
     */
    public function getViabillPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_viabill/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['viabill'];
        }

        return $paymentIcon;
    }

    /**
     * get mobile pay payment icon
     *
     * @return array $paymentIcon
     */
    public function getMobilepayPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_mobilepay/show_icon', $storeScope);
        
        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['mobilepay'];
        }

        return $paymentIcon;
    }
}
