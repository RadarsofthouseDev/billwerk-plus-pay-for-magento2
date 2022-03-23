<?php

namespace Radarsofthouse\Reepay\Block;

class Paymenticons extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
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
     * Get payment icons for credit card payment
     *
     * @return array $paymentIcons
     */
    public function getPaymentIcons()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $paymentIconsConfig = $this->scopeConfig->getValue('payment/reepay_payment/payment_icons', $storeScope);
        
        if (empty($paymentIconsConfig)) {
            return [];
        }

        $paymentIcons = explode(',', $paymentIconsConfig);

        return $paymentIcons;
    }

    /**
     * Get Viabill payment icon
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
     * Get mobile pay payment icon
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

    /**
     * Get Applepay payment icon
     *
     * @return array $paymentIcon
     */
    public function getApplepayPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_applepay/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['applepay'];
        }

        return $paymentIcon;
    }

    /**
     * Get Paypal payment icon
     *
     * @return array $paymentIcon
     */
    public function getPaypalPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_paypal/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['paypal'];
        }

        return $paymentIcon;
    }

    /**
     * Get Klarna Pay Now payment icon
     *
     * @return array $paymentIcon
     */
    public function getKlarnapaynowPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_klarnapaynow/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['klarna-pay-now'];
        }

        return $paymentIcon;
    }

    /**
     * Get Klarna Pay Later payment icon
     *
     * @return array $paymentIcon
     */
    public function getKlarnapaylaterPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_klarnapaylater/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['klarna-pay-later'];
        }

        return $paymentIcon;
    }

    /**
     * Get Klarna Slice It payment icon
     *
     * @return array $paymentIcon
     */
    public function getKlarnaSliceItPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_klarnasliceit/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['klarna-slice-it'];
        }

        return $paymentIcon;
    }

    /**
     * Get Swish payment icon
     *
     * @return array $paymentIcon
     */
    public function getSwishPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_swish/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['swish'];
        }

        return $paymentIcon;
    }

    /**
     * Get Resurs payment icon
     *
     * @return array $paymentIcon
     */
    public function getResursPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_resurs/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['resurs'];
        }

        return $paymentIcon;
    }

    /**
     * Get Resurs payment icon
     *
     * @return array $paymentIcon
     */
    public function getVippsPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_vipps/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['vipps'];
        }

        return $paymentIcon;
    }

    /**
     * Get Forbrugsforeningen payment icon
     *
     * @return array $paymentIcon
     */
    public function getForbrugsforeningenPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_forbrugsforeningen/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['forbrugsforeningen'];
        }

        return $paymentIcon;
    }

    /**
     * Get Google pay payment icon
     *
     * @return array $paymentIcon
     */
    public function getGooglepayPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_googlepay/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['googlepay'];
        }

        return $paymentIcon;
    }
}
