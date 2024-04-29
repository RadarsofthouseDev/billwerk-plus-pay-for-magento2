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
     * Get AnyDay payment icon
     *
     * @return array $paymentIcon
     */
    public function getAnydayPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_anyday/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['anyday'];
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
     * Get Klarna Direct Bank Transfer payment icon
     *
     * @return array $paymentIcon
     */
    public function getKlarnaDirectBankTransferPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_klarnadirectbanktransfer/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['klarna-direct-bank-transfer'];
        }

        return $paymentIcon;
    }

    /**
     * Get Klarna Direct Debit payment icon
     *
     * @return array $paymentIcon
     */
    public function getKlarnaDirectDebitPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_klarnadirectdebit/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['klarna-direct-debit'];
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
     * Get Vipps payment icon
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

    /**
     * Get iDEAL payment icon
     *
     * @return array $paymentIcon
     */
    public function getIdealPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_ideal/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['ideal'];
        }

        return $paymentIcon;
    }

    /**
     * Get BLIK payment icon
     *
     * @return array $paymentIcon
     */
    public function getBlikPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_blik/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['blik_oc'];
        }

        return $paymentIcon;
    }

    /**
     * Get Przelewy24 (P24) payment icon
     *
     * @return array $paymentIcon
     */
    public function getP24PaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_p24/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['p24'];
        }

        return $paymentIcon;
    }

    /**
     * Get Verkkopankki payment icon
     *
     * @return array $paymentIcon
     */
    public function getVerkkopankkiPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_verkkopankki/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['verkkopankki'];
        }

        return $paymentIcon;
    }

    /**
     * Get giropay payment icon
     *
     * @return array $paymentIcon
     */
    public function getGiropayPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_giropay/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['giropay'];
        }

        return $paymentIcon;
    }

    /**
     * Get SEPA Direct Debit payment icon
     *
     * @return array $paymentIcon
     */
    public function getSepaPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_sepa/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['sepa'];
        }

        return $paymentIcon;
    }

    /**
     * Get Bancontact payment icon
     *
     * @return array $paymentIcon
     */
    public function getBancontactPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_bancontact/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['bancontact'];
        }

        return $paymentIcon;
    }

    /**
     * Get Santander payment icon
     *
     * @return array $paymentIcon
     */
    public function getSantanderPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_santander/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['santander'];
        }

        return $paymentIcon;
    }

    /**
     * Get EPS payment icon
     *
     * @return array $paymentIcon
     */
    public function getEpsPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_eps/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['eps'];
        }

        return $paymentIcon;
    }

    /**
     * Get MB Way payment icon
     *
     * @return array $paymentIcon
     */
    public function getMbWayPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_mbway/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['mb-way'];
        }

        return $paymentIcon;
    }

    /**
     * Get Multibanco payment icon
     *
     * @return array $paymentIcon
     */
    public function getMultibancoPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_multibanco/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['multibanco'];
        }

        return $paymentIcon;
    }

    /**
     * Get mBank payment icon
     *
     * @return array $paymentIcon
     */
    public function getMybankPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_mybank/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['mybank'];
        }

        return $paymentIcon;
    }

    /**
     * Get Payconiq payment icon
     *
     * @return array $paymentIcon
     */
    public function getPayconiqPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_payconiq/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['payconiq'];
        }

        return $paymentIcon;
    }

    /**
     * Get Paysafecard payment icon
     *
     * @return array $paymentIcon
     */
    public function getPaysafecardPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_paysafecard/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['paysafecard'];
        }

        return $paymentIcon;
    }

    /**
     * Get Paysera payment icon
     *
     * @return array $paymentIcon
     */
    public function getPayseraPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_paysera/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['paysera'];
        }

        return $paymentIcon;
    }

    /**
     * Get PostFinance payment icon
     *
     * @return array $paymentIcon
     */
    public function getPostfinancePaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_postfinance/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['postfinance'];
        }

        return $paymentIcon;
    }

    /**
     * Get Satisfy payment icon
     *
     * @return array $paymentIcon
     */
    public function getSatispayPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_satispay/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['satispay'];
        }

        return $paymentIcon;
    }

    /**
     * Get Trustly payment icon
     *
     * @return array $paymentIcon
     */
    public function getTrustlyPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_trustly/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['trustly'];
        }

        return $paymentIcon;
    }

    /**
     * Get WeChat Pay payment icon
     *
     * @return array $paymentIcon
     */
    public function getWechatpayPaymentIcon()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $showIcon = $this->scopeConfig->getValue('payment/reepay_wechatpay/show_icon', $storeScope);

        $paymentIcon = [];
        if ($showIcon) {
            $paymentIcon = ['wechatpay'];
        }

        return $paymentIcon;
    }
}
