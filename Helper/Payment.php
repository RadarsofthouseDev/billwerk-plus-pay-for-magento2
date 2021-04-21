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
    private $_reepayCustomerHelper;
    protected $_reepayChargeHelper;
    protected $_customerRepository;
    protected $_customerSession;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Locale\Resolver $resolver
     * @param \Radarsofthouse\Reepay\Helper\Session $reepaySessionHelper
     * @param \Radarsofthouse\Reepay\Helper\Customer $reepayCustomerHelper
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Radarsofthouse\Reepay\Helper\Charge $reepayChargeHelper
     * @param \Radarsofthouse\Reepay\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Locale\Resolver $resolver,
        \Radarsofthouse\Reepay\Helper\Session $reepaySessionHelper,
        \Radarsofthouse\Reepay\Helper\Customer $reepayCustomerHelper,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Radarsofthouse\Reepay\Helper\Charge $reepayChargeHelper,
        \Radarsofthouse\Reepay\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->_resolver = $resolver;
        $this->_urlInterface = $urlInterface;
        $this->_reepaySessionHelper = $reepaySessionHelper;
        $this->_reepayCustomerHelper = $reepayCustomerHelper;
        $this->_reepayHelper = $reepayHelper;
        $this->_storeManager = $storeManager;
        $this->_reepayChargeHelper = $reepayChargeHelper;
        $this->_customerRepository = $customerRepository;
        $this->_customerSession = $customerSession;
    }

    /**
     * create reepay session
     *
     * @param \Magento\Sales\Model\Order $order
     * @param $reepayCreditCard
     * @return string $paymentTransactionId
     */
    public function createReepaySession($order, $reepayCreditCard = null)
    {
        $apiKey = $this->_reepayHelper->getApiKey($order->getStoreId());
        $customerEmail = $order->getCustomerEmail();
        $customerHandle = $this->_reepayCustomerHelper->search($apiKey,$customerEmail);
        $customer = $this->_reepayHelper->getCustomerData($order);
        $billingAddress = $this->_reepayHelper->getOrderBillingAddress($order);
        $shippingAddress = $this->_reepayHelper->getOrderShippingAddress($order);
        $paymentMethods = $this->_reepayHelper->getPaymentMethods($order);

        $orderData = [
            'handle' => $order->getIncrementId(),
            'currency' => $order->getOrderCurrencyCode(),
            'billing_address' => $billingAddress,
            'shipping_address' => $shippingAddress,
        ];

        if ($this->_reepayHelper->getConfig('send_order_line') == '1') {
            $orderData['order_lines'] = $this->_reepayHelper->getOrderLines($order);
        } else {
            $grandTotal = $order->getGrandTotal() * 100;
            $orderData['amount'] = (int)$grandTotal;
        }

        $settle = false;
        $autoCaptureConfig = $this->_reepayHelper->getConfig('auto_capture', $order->getStoreId());
        if ($autoCaptureConfig == 1 || $order->getPayment()->getMethodInstance()->getCode() == "reepay_swish" ) {
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

        $options['accept_url'] = $this->_storeManager->getStore($order->getStoreId())->getBaseUrl() . 'reepay/standard/accept';
        $options['cancel_url'] = $this->_storeManager->getStore($order->getStoreId())->getBaseUrl() . 'reepay/standard/cancel';

        $save_card_enable = $this->_reepayHelper->getConfig('save_card_enable', $order->getStoreId());
        if( $save_card_enable && $this->_customerSession->isLoggedIn() ){
            $options['recurring_optional'] = true;
        }

        if($reepayCreditCard !== null){
            $options['card_on_file'] = $reepayCreditCard;
        }

        if($customerHandle !== false){
            $res = $this->_reepaySessionHelper->chargeCreateWithExistCustomer(
                $apiKey,
                $customerHandle,
                $orderData,
                $paymentMethods,
                $settle,
                $options
            );
        }else {
            $res = $this->_reepaySessionHelper->chargeCreateWithNewCustomer(
                $apiKey,
                $customer,
                $orderData,
                $paymentMethods,
                $settle,
                $options
            );
        }

        $paymentTransactionId = $res['id'];

        return $paymentTransactionId;
    }


    /**
     * create charge
     *
     * @param \Magento\Sales\Model\Order $order
     * @param $reepayCreditCard
     * @return
     */
    public function createChargeWithExistCustomer($order, $reepayCreditCard)
    {
        $apiKey = $this->_reepayHelper->getApiKey($order->getStoreId());
        $handle = $order->getIncrementId();
        $source = $reepayCreditCard;
        $customerEmail = $order->getCustomerEmail();
        $reepayCustomer = $this->_customerRepository->getByMagentoCustomerEmail($customerEmail);
        $customerHandle = $reepayCustomer->getHandle();
        $options = [];

        if ($this->_reepayHelper->getConfig('send_order_line') == '1') {
            $options['order_lines'] = $this->_reepayHelper->getOrderLines($order);
        } else {
            $grandTotal = $order->getGrandTotal() * 100;
            $options['amount'] = (int)$grandTotal;
        }

        $billingAddress = $this->_reepayHelper->getOrderBillingAddress($order);
        $shippingAddress = $this->_reepayHelper->getOrderShippingAddress($order);
        $options['billing_address'] = $billingAddress;
        $options['shipping_address'] = $shippingAddress;

        $options['currency'] = $order->getOrderCurrencyCode();

        $settle = false;
        $autoCaptureConfig = $this->_reepayHelper->getConfig('auto_capture', $order->getStoreId());
        if ( $autoCaptureConfig == 1 ) {
            $settle = true;
        }
        $options['settle'] = $settle;
        
        $createCharge = $this->_reepayChargeHelper->createWithExistCustomer($apiKey, $handle, $source, $customerHandle, $options);

        return $createCharge;
    }
}
