<?php

namespace Radarsofthouse\Reepay\Controller\Standard;

/**
 * Class Redirect
 *
 * @package Radarsofthouse\Reepay\Controller\Standard
 */
class Redirect extends \Magento\Framework\App\Action\Action
{
    const DISPLAY_EMBEDDED = 1;
    const DISPLAY_OVERLAY = 2;
    const DISPLAY_WINDOW = 3;

    private $resultPageFactory;
    private $orderInterface;
    private $storeManagerInterface;
    private $resolver;
    private $reepaySession;
    private $logger;
    protected $url;
    protected $scopeConfig;
    protected $checkoutSession;
    protected $reepayHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Sales\Api\Data\OrderInterface $orderInterface
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\Locale\Resolver $resolver
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Radarsofthouse\Reepay\Helper\Session $reepaySession
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Locale\Resolver $resolver,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Radarsofthouse\Reepay\Helper\Session $reepaySession,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Radarsofthouse\Reepay\Helper\Logger $logger
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->orderInterface = $orderInterface;
        $this->scopeConfig = $scopeConfig;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->resolver = $resolver;
        $this->checkoutSession = $checkoutSession;
        $this->reepaySession = $reepaySession;
        $this->url = $context->getUrl();
        $this->reepayHelper = $reepayHelper;
        $this->logger = $logger;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $this->logger->addDebug(__METHOD__);

            if (!$this->_objectManager->get(\Magento\Checkout\Model\Session\SuccessValidator::class)->isValid()) {
                return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;

            // get order ID from onepage checkout session
            $checkoutOnepageSession = $this->_objectManager->get(\Magento\Checkout\Model\Type\Onepage::class)->getCheckout();
            $orderId = $checkoutOnepageSession->getLastOrderId();
            $order = $this->_objectManager->create('\Magento\Sales\Model\Order')->load($orderId);

            if (!$order->getId()) {
                return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }


            $paymentTransactionId = null;
            
            $this->logger->addDebug('Create Reepay session');

            $paymentTransactionId = $this->createReepaySession($order);

            $this->logger->addDebug('$paymentTransactionId : '.$paymentTransactionId);
            
            if (!empty($paymentTransactionId)) {
                $reepayStatusModel = $this->_objectManager->create('Radarsofthouse\Reepay\Model\Status');
                $reepayStatus = $reepayStatusModel->load($order->getIncrementId(), 'order_id');
                if ($reepayStatus->getStatusId()) {
                    // update TransactionId
                    $data = [
                        'token' => $paymentTransactionId,
                    ];
                    $this->reepayHelper->updateReepayPaymentData($order->getIncrementId(), $data);
                    $this->logger->addDebug('update token : '.$order->getIncrementId().' => '.$paymentTransactionId);
                } else {
                    // create reepay status (radarsofthouse_reepay_status)
                    $data = [
                        'order_id' => $orderId,
                        'first_name' => $order->getBillingAddress()->getFirstname(),
                        'last_name' => $order->getBillingAddress()->getLastname(),
                        'email' => $order->getCustomerEmail(),
                        'token' => $paymentTransactionId,
                        'status' => 'created',
                    ];

                    $newReepayStatus = $this->_objectManager->create('Radarsofthouse\Reepay\Model\Status');
                    $newReepayStatus->setData($data);
                    $newReepayStatus->save();
                    $this->logger->addDebug('created Radarsofthouse\Reepay\Model\Status', $data);
                }
            }

            // render reepay/standard/redirect
            $pageTitleConfig = $this->scopeConfig->getValue('payment/reepay_payment/title', $storeScope);
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()
                ->getTitle()
                ->set($pageTitleConfig);
            
            $displayTypeConfig = $this->scopeConfig->getValue('payment/reepay_payment/display_type', $storeScope);
            
            if ($order->getPayment()->getMethodInstance()->getCode() == 'reepay_viabill') {
                // force viabill into payment window always
                $this->logger->addDebug('Render Viabill DISPLAY_WINDOW : '.$paymentTransactionId);

                $resultPage->getLayout()
                    ->getBlock('reepay_standard_redirect')
                    ->setTemplate('Radarsofthouse_Reepay::standard/window.phtml')
                    ->setPaymentTransactionId($paymentTransactionId);
            } elseif ($displayTypeConfig == SELF::DISPLAY_EMBEDDED) {
                $this->logger->addDebug('Render DISPLAY_EMBEDDED : '.$paymentTransactionId);

                $resultPage->getLayout()
                    ->getBlock('reepay_standard_redirect')
                    ->setTemplate('Radarsofthouse_Reepay::standard/embedded.phtml')
                    ->setPaymentTransactionId($paymentTransactionId);
            } elseif ($displayTypeConfig == SELF::DISPLAY_OVERLAY) {
                $this->logger->addDebug('Render DISPLAY_OVERLAY : '.$paymentTransactionId);

                $resultPage->getLayout()
                    ->getBlock('reepay_standard_redirect')
                    ->setTemplate('Radarsofthouse_Reepay::standard/overlay.phtml')
                    ->setPaymentTransactionId($paymentTransactionId);
            } elseif ($displayTypeConfig == SELF::DISPLAY_WINDOW) {
                $this->logger->addDebug('Render DISPLAY_WINDOW : '.$paymentTransactionId);

                $resultPage->getLayout()
                    ->getBlock('reepay_standard_redirect')
                    ->setTemplate('Radarsofthouse_Reepay::standard/window.phtml')
                    ->setPaymentTransactionId($paymentTransactionId);
            }
            
            return $resultPage;
        } catch (\Exception $e) {
            $this->logger->addDebug('reepay/standard/redirect exception : '.$e->getMessage());

            $this->messageManager->addException($e, __('Something went wrong, please try again later'));
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * create reepay session
     *
     * @param string $orderId
     * @return string $paymentTransactionId
     */
    private function createReepaySession($order)
    {
        $apiKey = $this->reepayHelper->getApiKey($order->getStoreId());

        $customer = $this->getCustomerData($order);

        $billingAddress = $this->getOrderBillingAddress($order);
        $shippingAddress = $this->getOrderShippingAddress($order);
        $orderLines = $this->getOrderLines($order);

        $orderData = [
            'handle' => $order->getIncrementId(),
            'currency' => $order->getOrderCurrencyCode(),
            'order_lines' => $orderLines,
            'billing_address' => $billingAddress,
            'shipping_address' => $shippingAddress,
        ];

        $paymentMethods = $this->getPaymentMethods($order);

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;

        $settle = false;
        $autoCaptureConfig = $this->scopeConfig->getValue('payment/reepay_payment/auto_capture', $storeScope);
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
        
        if (!empty($localMapping[$this->resolver->getLocale()])) {
            $options['locale'] = $localMapping[$this->resolver->getLocale()];
        }

        $options['accept_url'] = $this->url->getUrl('reepay/standard/accept');
        $options['cancel_url'] = $this->url->getUrl('reepay/standard/cancel');

        $res = $this->reepaySession->chargeCreateWithNewCustomer(
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

    /**
     * get order lines data from order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array $orderLines
     */
    public function getOrderLines($order)
    {
        $orderGrandTotal = (int)($order->getGrandTotal() * 100);
        $total = 0;
        $orderLines = [];

        // products
        $orderitems = $order->getAllVisibleItems();
        foreach ($orderitems as $orderitem) {
            $amount = ($orderitem->getRowTotal() * 100) / $orderitem->getQtyOrdered();
            $line = [
                'ordertext' => $orderitem->getProduct()->getName(),
                'amount' => (int)$amount,
                'quantity' => (int)$orderitem->getQtyOrdered(),
            ];
            $orderLines[] = $line;
            $total = $total + ($orderitem->getRowTotal() * 100);
        }
        
        // tax
        $taxAmount = ($order->getTaxAmount() * 100);
        if ($taxAmount != 0) {
            $line = [
                'ordertext' => $this->__('Tax.'),
                'amount' => (int)$taxAmount,
                'quantity' => 1,
            ];
            $orderLines[] = $line;
            $total = $total + $taxAmount;
        }

        // shipping
        $shippingAmount = ($order->getShippingAmount() * 100);
        if ($shippingAmount != 0) {
            $line = [
                'ordertext' => $order->getShippingDescription(),
                'amount' => (int)$shippingAmount,
                'quantity' => 1,
            ];
            $orderLines[] = $line;
            $total = $total + $shippingAmount;
        }

        // discount
        $discountAmount = ($order->getDiscountAmount() * 100);
        if ($discountAmount != 0) {
            $line = [
                'ordertext' => $order->getDiscountDescription(),
                'amount' => (int)$discountAmount,
                'quantity' => 1,
            ];
            $orderLines[] = $line;
            $total = $total + $discountAmount;
        }

        // other
        if ((int)$total != $orderGrandTotal) {
            $line = [
                'ordertext' => $this->__('etc.'),
                'amount' => (int)($orderGrandTotal - $total),
                'quantity' => 1,
            ];
            $orderLines[] = $line;
        }
        
        return $orderLines;
    }

    /**
     * get billing address from order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array billing address data
     */
    public function getOrderBillingAddress($order)
    {
        $address1 = $order->getBillingAddress()->getStreet(1);
        $address2 = $order->getBillingAddress()->getStreet(2);

        $vatId = '';
        if (!empty($order->getBillingAddress()->getVatId())) {
            $vatId = $order->getBillingAddress()->getVatId();
        }

        return [
            'company' => $order->getBillingAddress()->getCompany(),
            'vat' => $vatId,
            'attention' => '',
            'address' => $address1[0],
            'address2' => $address2[0],
            'city' => $order->getBillingAddress()->getCity(),
            'country' => $order->getBillingAddress()->getCountryId(),
            'email' => $order->getBillingAddress()->getEmail(),
            'phone' => $order->getBillingAddress()->getTelephone(),
            'first_name' => $order->getBillingAddress()->getFirstname(),
            'last_name' => $order->getBillingAddress()->getLastname(),
            'postal_code' => $order->getBillingAddress()->getPostcode(),
            'state_or_province' => $order->getBillingAddress()->getRegion(),
        ];
    }

    /**
     * get shipping address from order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array shipping address data
     */
    public function getOrderShippingAddress($order)
    {
        $address1 = $order->getShippingAddress()->getStreet(1);
        $address2 = $order->getShippingAddress()->getStreet(2);

        $vatId = '';
        if (!empty($order->getShippingAddress()->getVatId())) {
            $vatId = $order->getShippingAddress()->getVatId();
        }

        return [
            'company' => $order->getShippingAddress()->getCompany(),
            'vat' => $vatId,
            'attention' => '',
            'address' => $address1[0],
            'address2' => $address2[0],
            'city' => $order->getShippingAddress()->getCity(),
            'country' => $order->getShippingAddress()->getCountryId(),
            'email' => $order->getShippingAddress()->getEmail(),
            'phone' => $order->getShippingAddress()->getTelephone(),
            'first_name' => $order->getShippingAddress()->getFirstname(),
            'last_name' => $order->getShippingAddress()->getLastname(),
            'postal_code' => $order->getShippingAddress()->getPostcode(),
            'state_or_province' => $order->getShippingAddress()->getRegion(),
        ];
    }

    /**
     * get customer data from order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array customer data
     */
    public function getCustomerData($order)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $testModeConfig = $this->scopeConfig->getValue('payment/reepay_payment/api_key_type', $storeScope);
        $testMode = true;
        if ($testModeConfig == 1) {
            $testMode = false;
        }

        $address1 = $order->getBillingAddress()->getStreet(1);
        $address2 = $order->getBillingAddress()->getStreet(2);

        $vatId = '';
        if (!empty($order->getBillingAddress()->getVatId())) {
            $vatId = $order->getBillingAddress()->getVatId();
        }

        return [
            'handle' => $order->getBillingAddress()->getEmail(),
            'email' => $order->getBillingAddress()->getEmail(),
            'first_name' => $order->getBillingAddress()->getFirstname(),
            'last_name' => $order->getBillingAddress()->getLastname(),
            'address' => $address1[0],
            'address2' => $address2[0],
            'city' => $order->getBillingAddress()->getCity(),
            'country' => $order->getBillingAddress()->getCountryId(),
            'phone' => $order->getBillingAddress()->getTelephone(),
            'company' => $order->getBillingAddress()->getCompany(),
            'postal_code' => $order->getBillingAddress()->getPostcode(),
            'vat' => $vatId,
            'test' => $testMode,
            'generate_handle' => false,
        ];
    }

    /**
     * get allowwed payment from configuration
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array $paymentMethods
     */
    public function getPaymentMethods($order)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $paymentMethods = [];
        if ($order->getPayment()->getMethodInstance()->getCode() == 'reepay_viabill') {
            $paymentMethods[] = 'viabill';
        } elseif ($order->getPayment()->getMethodInstance()->getCode() == 'reepay_mobilepay') {
            $paymentMethods[] = 'mobilepay';
        } else {
            $allowwedPaymentConfig = $this->scopeConfig->getValue('payment/reepay_payment/allowwed_payment', $storeScope);
            $paymentMethods = explode(',', $allowwedPaymentConfig);
        }

        return $paymentMethods;
    }
}
