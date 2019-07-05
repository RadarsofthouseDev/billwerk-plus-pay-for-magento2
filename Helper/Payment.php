<?php

namespace Radarsofthouse\Reepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Payment
 *
 * @package Radarsofthouse\Reepay\Helper
 */
class Payment extends AbstractHelper
{
    protected $_resolver;
    protected $_urlInterface;
    protected $_reepaySessionHelper;
    protected $_reepayHelper;
    protected $_storeManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context  $context
     * @param \Magento\Framework\Locale\Resolver $resolver
     * @param \Radarsofthouse\Reepay\Helper\Session $reepaySessionHelper
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Locale\Resolver $resolver,
        \Radarsofthouse\Reepay\Helper\Session $reepaySessionHelper,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_resolver = $resolver;
        $this->_urlInterface = $urlInterface;
        $this->_reepaySessionHelper = $reepaySessionHelper;
        $this->_reepayHelper = $reepayHelper;
        $this->_storeManager = $storeManager;

    }

    /**
     * create reepay session
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string $paymentTransactionId
     */
    public function createReepaySession($order)
    {
        $apiKey = $this->_reepayHelper->getApiKey($order->getStoreId());
        $customer = $this->_reepayHelper->getCustomerData($order);
        $billingAddress = $this->_reepayHelper->getOrderBillingAddress($order);
        $shippingAddress = $this->_reepayHelper->getOrderShippingAddress($order);
        $orderLines = $this->_reepayHelper->getOrderLines($order);
        $paymentMethods = $this->_reepayHelper->getPaymentMethods($order);

        $orderData = [
            'handle' => $order->getIncrementId(),
            'currency' => $order->getOrderCurrencyCode(),
            'order_lines' => $orderLines,
            'billing_address' => $billingAddress,
            'shipping_address' => $shippingAddress,
        ];

        $settle = false;
        $autoCaptureConfig = $this->_reepayHelper->getConfig('auto_capture', $order->getStoreId());
        if ($autoCaptureConfig == 1) {
            $settle = true;
        }

        $localMapping = [
            'da_DK' => 'da_DK',
            'sv_SE' => 'sv_SE',
            'nb_NO' => 'no_NO',
            'nn_NO' => 'no_NO',
            'en_AU' => 'en_GB',
            'en_CA' => 'en_GB',
            'en_IE' => 'en_GB',
            'en_NZ' => 'en_GB',
            'en_GB' => 'en_GB',
            'en_US' => 'en_GB',
            'de_AT' => 'de_DE',
            'de_DE' => 'de_DE',
            'de_CH' => 'de_DE',
            'fr_CA' => 'fr_FR',
            'fr_FR' => 'fr_FR',
            'es_AR' => 'es_ES',
            'es_CL' => 'es_ES',
            'es_CO' => 'es_ES',
            'es_CR' => 'es_ES',
            'es_MX' => 'es_ES',
            'es_PA' => 'es_ES',
            'es_PE' => 'es_ES',
            'es_ES' => 'es_ES',
            'es_VE' => 'es_ES',
            'nl_NL' => 'nl_NL',
            'pl_PL' => 'pl_PL',
        ];
        
        $options = [];
        
        if (!empty($localMapping[$this->_resolver->getLocale()])) {
            $options['locale'] = $localMapping[$this->_resolver->getLocale()];
        }

        $options['accept_url'] = $this->_storeManager->getStore($order->getStoreId())->getBaseUrl().'reepay/standard/accept';
        $options['cancel_url'] = $this->_storeManager->getStore($order->getStoreId())->getBaseUrl().'reepay/standard/cancel';

        $res = $this->_reepaySessionHelper->chargeCreateWithNewCustomer(
            $apiKey,
            $customer,
            $orderData,
            $paymentMethods,
            $settle,
            $options
        );

        $paymentTransactionId = $res['id'];

        return $paymentTransactionId;
    }


}
