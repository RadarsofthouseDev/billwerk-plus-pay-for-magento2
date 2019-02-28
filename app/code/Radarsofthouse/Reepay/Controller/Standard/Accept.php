<?php

namespace Radarsofthouse\Reepay\Controller\Standard;

/**
 * Class Accept
 *
 * @package Radarsofthouse\Reepay\Controller\Standard
 */
class Accept extends \Magento\Framework\App\Action\Action
{
    private $orderInterface;
    private $resultPageFactory;
    private $reepayCharge;
    private $reepaySession;
    private $logger;
    protected $request;
    protected $orderManagement;
    protected $checkoutSession;
    protected $url;
    protected $scopeConfig;
    protected $resultJsonFactory;
    protected $reepayHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Sales\Api\Data\OrderInterface $orderInterface
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Radarsofthouse\Reepay\Helper\Charge $reepayCharge
     * @param \Radarsofthouse\Reepay\Helper\Session $reepaySession
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Radarsofthouse\Reepay\Helper\Charge $reepayCharge,
        \Radarsofthouse\Reepay\Helper\Session $reepaySession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Radarsofthouse\Reepay\Helper\Logger $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        $this->orderInterface = $orderInterface;
        $this->orderManagement = $orderManagement;
        $this->checkoutSession = $checkoutSession;
        $this->url = $context->getUrl();
        $this->scopeConfig = $scopeConfig;
        $this->reepayCharge = $reepayCharge;
        $this->reepaySession = $reepaySession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->reepayHelper = $reepayHelper;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->request->getParams('');
        $orderId = $params['invoice'];
        $id = $params['id'];
        $isAjax = 0;
        if (isset($params['_isAjax'])) {
            $isAjax = 1;
        }
        
        $this->logger->addDebug(__METHOD__, $params);

        if (empty($params['invoice']) || empty($params['id'])) {
            return;
        }

        $order = $this->orderInterface->loadByIncrementId($orderId);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reepayStatusModel = $objectManager->create('Radarsofthouse\Reepay\Model\Status');
        $reepayStatus = $reepayStatusModel->load($orderId, 'order_id');
        if ($reepayStatus->getStatusId()) {
            if ($reepayStatus->getStatus() != 'created') {
                $this->logger->addDebug('order : '.$orderId.' has been accepted already');
                $this->_redirect('checkout/onepage/success');
            }
        }
        
        $apiKey = $this->reepayHelper->getApiKey($order->getStoreId());
        $chargeRes = $this->reepayCharge->get(
            $apiKey,
            $orderId
        );

        // update Reepay payment data
        $data = [
            'order_id' => $orderId,
            'first_name' => $order->getBillingAddress()->getFirstname(),
            'last_name' => $order->getBillingAddress()->getLastname(),
            'email' => $order->getCustomerEmail(),
            'token' => $params['id'],
            'masked_card_number' => $chargeRes['source']['masked_card'],
            'fingerprint' => $chargeRes['source']['fingerprint'],
            'card_type' => $chargeRes['source']['card_type'],
            'status' => $chargeRes['state'],
        ];
        $this->reepayHelper->updateReepayPaymentData($orderId, $data);
        $this->logger->addDebug('updateReepayPaymentData', $data);

        $this->addTransactionToOrder($order, $chargeRes);

        // delete reepay session
        $sessionRes = $this->reepaySession->delete(
            $apiKey,
            $id
        );

        // unset reepay session id on checkout session
        /*
        if ($this->checkoutSession->getReepaySessionID() && $this->checkoutSession->getReepaySessionOrder()) {
            $this->checkoutSession->unsReepaySessionID();
            $this->checkoutSession->unsReepaySessionOrder();
        }
        */
        
        /*
        $orderEmailSender = $objectManager->create('Magento\Sales\Model\Order\Email\Sender\OrderSender');
        $sendEmailAfterPayment = $this->scopeConfig->getValue("payment/reepay_payment/send_email_after_payment", $storeScope);
        if ($sendEmailAfterPayment) {
            $orderEmailSender->send($order);
        }
        */

        if ($isAjax == 1) {
            $result = [
                'status' => 'success',
                'redirect_url' => $this->url->getUrl('checkout/onepage/success'),
            ];

            return  $this->resultJsonFactory->create()->setData($result);
        } else {
            $this->logger->addDebug('Redirect to checkout/onepage/success');
            $this->_redirect('checkout/onepage/success');
        }
    }

    /**
     * add transaction to order
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array $paymentData
     * @return string Transaction Id
     */
    public function addTransactionToOrder($order, $paymentData = [])
    {
        try {
            $this->logger->addDebug(__METHOD__);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $paymentData = $this->preparePaymentData($paymentData);

            $state = '';
            $isClosed = 0;
            if ($paymentData['state'] == 'authorized') {
                $state = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH;
                $isClosed = 0;
            } elseif ($paymentData['state'] == 'settled') {
                $state = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE;
                $isClosed = 1;
            }

            $payment = $order->getPayment();
            $payment->setLastTransId($paymentData['transaction']);
            $payment->setTransactionId($paymentData['transaction']);
            $payment->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData]);

            $formatedPrice = $order->getBaseCurrency()->formatTxt($order->getGrandTotal());

            $transactionBuilder = $objectManager->create('Magento\Sales\Model\Order\Payment\Transaction\Builder');
            $transaction = $transactionBuilder->setPayment($payment)
                ->setOrder($order)
                ->setTransactionId($paymentData['transaction'])
                ->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $paymentData])
                ->setFailSafe(true)
                ->build($state)
                ->setIsClosed($isClosed);

            // Add transaction to payment
            $payment->addTransactionCommentsToOrder($transaction, __('The authorized amount is %1.', $formatedPrice));
            $payment->setParentTransactionId(null);

            // Save payment, transaction and order
            $payment->save();
            $order->save();
            $transaction->save();

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
            $orderStatusAfterPayment = $this->scopeConfig->getValue('payment/reepay_payment/order_status_after_payment', $storeScope);
            if (!empty($orderStatusAfterPayment)) {
                $grandTotal = $objectManager->create('Magento\Framework\Pricing\Helper\Data')->currency($order->getGrandTotal(), true, false);

                $order->setState($orderStatusAfterPayment, true);
                $order->setStatus($orderStatusAfterPayment);
                $order->addStatusToHistory($order->getStatus(), 'Reepay : The authorized amount is '.$grandTotal);
                $order->save();

                $this->logger->addDebug('Change order status after payment'.$order->getIncrementId().' to '.$orderStatusAfterPayment);
            }

            $this->logger->addDebug('Transaction ID : '.$transaction->getTransactionId());

            return  $transaction->getTransactionId();
        } catch (Exception $e) {
            $this->logger->addError('addTransactionToOrder Exception : '.$e->getMessage());
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        }
    }

    /**
     * prepare payment data
     *
     * @param array $paymentData
     * @return array $paymentData
     */
    public function preparePaymentData($paymentData)
    {
        if (isset($paymentData['order_lines'])) {
            unset($paymentData['order_lines']);
        }

        if (isset($paymentData['billing_address'])) {
            unset($paymentData['billing_address']);
        }

        if (isset($paymentData['shipping_address'])) {
            unset($paymentData['shipping_address']);
        }

        if (isset($paymentData['source'])) {
            $source = $paymentData['source'];
            unset($paymentData['source']);
            $paymentData['source_type'] = $source['type'];
            $paymentData['source_fingerprint'] = $source['fingerprint'];
            $paymentData['source_card_type'] = $source['card_type'];
            $paymentData['source_exp_date'] = $source['exp_date'];
            $paymentData['source_masked_card'] = $source['masked_card'];
        }

        if (isset($paymentData['amount']) && $paymentData['amount'] > 0) {
            $paymentData['amount'] = $paymentData['amount'] / 100;
        }

        if (isset($paymentData['refunded_amount']) && $paymentData['refunded_amount'] > 0) {
            $paymentData['refunded_amount'] = $paymentData['refunded_amount'] / 100;
        }

        if (isset($paymentData['authorized_amount']) && $paymentData['authorized_amount'] > 0) {
            $paymentData['authorized_amount'] = $paymentData['authorized_amount'] / 100;
        }
        $this->logger->addDebug('$paymentData : ', $paymentData);

        return $paymentData;
    }
}
