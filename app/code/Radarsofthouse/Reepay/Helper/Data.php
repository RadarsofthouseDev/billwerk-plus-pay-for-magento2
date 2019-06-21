<?php

namespace Radarsofthouse\Reepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @package Radarsofthouse\Reepay\Helper
 */
class Data extends AbstractHelper
{
    const CONFIG_PATH = 'payment/reepay_payment/';

    protected $_storeManager;
    protected $_resolver;
    protected $_scopeConfig;
    protected $_reepayStatus;
    protected $_transactionBuilder;
    protected $_priceHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context  $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\Resolver $resolver
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Radarsofthouse\Reepay\Model\Status $reepayStatus
     * @param \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder
     * @param \Magento\Framework\Exception $exception
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\Resolver $resolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Radarsofthouse\Reepay\Model\Status $reepayStatus,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_resolver = $resolver;
        $this->_scopeConfig = $scopeConfig;
        $this->_reepayStatus = $reepayStatus;
        $this->_transactionBuilder = $transactionBuilder;
        $this->_priceHelper = $priceHelper;
    }

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->_scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::CONFIG_PATH . $code, $storeId);
    }

    /**
     * get private api key from backend configuration
     *
     * @param integer $storeId
     * @return string $apiKey
     */
    public function getApiKey($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        
        $apiKey = null;
        $testModeConfig = $this->getConfig('api_key_type', $storeId);
        if ($testModeConfig == 1) {
            $apiKey = $this->getConfig('private_key', $storeId);
        } else {
            $apiKey = $this->getConfig('private_key_test', $storeId);
        }

        return $apiKey;
    }

    /**
     * set reepay payment state to radarsofthouse_reepay_status
     *
     * @param  $payment
     * @param string $state
     * @return void
     */
    public function setReepayPaymentState($payment, $state)
    {
        $_additionalInfo = $payment->getAdditionalInformation();
        $_additionalInfo['raw_details_info']['state'] = $state;
        $payment->setAdditionalInformation($_additionalInfo);
        $payment->save();
        
        $orderId = $payment->getOrder()->getIncrementId();
        $reepayStatus = $this->_reepayStatus->load($orderId, 'order_id');
        if ($reepayStatus->getStatusId()) {
            $reepayStatus->setStatus($state);
            $reepayStatus->save();
        }
    }

    /**
     * update reepay payment data to radarsofthouse_reepay_status
     *
     * @param string $orderId
     * @param array $data
     * @return void
     */
    public function updateReepayPaymentData($orderId, $data)
    {
        $reepayStatus = $this->_reepayStatus->load($orderId, 'order_id');
        if ($reepayStatus->getStatusId()) {
            if (!empty($data['status'])) {
                $reepayStatus->setStatus($data['status']);
            }
            if (!empty($data['first_name'])) {
                $reepayStatus->setFirstName($data['first_name']);
            }
            if (!empty($data['last_name'])) {
                $reepayStatus->setLastName($data['last_name']);
            }
            if (!empty($data['email'])) {
                $reepayStatus->setEmail($data['email']);
            }
            if (!empty($data['token'])) {
                $reepayStatus->setToken($data['token']);
            }
            if (!empty($data['masked_card_number'])) {
                $reepayStatus->setMaskedCardNumber($data['masked_card_number']);
            }
            if (!empty($data['fingerprint'])) {
                $reepayStatus->setFingerprint($data['fingerprint']);
            }
            if (!empty($data['card_type'])) {
                $reepayStatus->setCardType($data['card_type']);
            }
            if (!empty($data['error'])) {
                $reepayStatus->setError($data['error']);
            }
            if (!empty($data['error_state'])) {
                $reepayStatus->setErrorState($data['error_state']);
            }

            $reepayStatus->save();
        }
    }

    /**
     * get customer data from order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array (customer data)
     */
    public function getCustomerData($order)
    {
        $testModeConfig = $this->getConfig('api_key_type', $order->getStoreId());
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
     * get billing address from order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array (billing address data)
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
     * @return array (shipping address data)
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
     * get order lines data from order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array $orderLines
     */
    public function getOrderLines($order)
    {
        $orderTotalDue = $order->getTotalDue() * 100;
        $total = 0;
        $orderLines = [];

        // products
        $orderitems = $order->getAllVisibleItems();
        foreach ($orderitems as $orderitem) {
            $amount = $orderitem->getPriceInclTax() * 100;
            $line = [
                'ordertext' => $orderitem->getProduct()->getName(),
                'amount' => (int)round($amount),
                'quantity' => (int)$orderitem->getQtyOrdered(),
                'vat' => $orderitem->getTaxPercent()/100,
                'amount_incl_vat' => "true",
            ];
            $orderLines[] = $line;
            $total = $total + (int)round($amount*$orderitem->getQtyOrdered());
        }
        
        /*
        // tax
        $taxAmount = ($order->getTaxAmount() * 100);
        if ($taxAmount != 0) {
            $line = [
                'ordertext' => __('Tax.'),
                'amount' => (int)$taxAmount,
                'quantity' => 1,
            ];
            $orderLines[] = $line;
            $total = $total + $taxAmount;
        }
        */

        // shipping
        $shippingAmount = ($order->getShippingInclTax() * 100);
        if ($shippingAmount != 0) {
            $line = [
                'ordertext' => $order->getShippingDescription(),
                'amount' => (int)$shippingAmount,
                'quantity' => 1,
            ];
            if ($order->getShippingTaxAmount() > 0) {
                $line['vat'] = $order->getShippingTaxAmount()/$order->getShippingAmount();
                $line['amount_incl_vat'] = "true";
            } else {
                $line['vat'] = 0;
                $line['amount_incl_vat'] = "true";
            }
            $orderLines[] = $line;
            $total = $total + (int)$shippingAmount;
        }

        // discount
        $discountAmount = ($order->getDiscountAmount() * 100);
        if ($discountAmount != 0) {
            $line = [
                'ordertext' => $order->getDiscountDescription(),
                'amount' => (int)$discountAmount,
                'quantity' => 1,
                'vat' => 0,
                'amount_incl_vat' => "true",
            ];
            $orderLines[] = $line;
            $total = $total + (int)$discountAmount;
        }

        // other
        if ((int)$total != (int)$orderTotalDue) {
            if ((int)($orderTotalDue - $total) > 0) {
                $line = [
                    'ordertext' => __('Other'),
                    'amount' => (int)($orderTotalDue - $total),
                    'quantity' => 1,
                    'vat' => 0,
                    'amount_incl_vat' => "true",
                ];
                $orderLines[] = $line;
            }
        }

        
        return $orderLines;
    }

    /**
     * get allowwed payment from backend configuration
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array $paymentMethods
     */
    public function getPaymentMethods($order)
    {
        $paymentMethods = [];
        if ($order->getPayment()->getMethodInstance()->getCode() == 'reepay_viabill') {
            $paymentMethods[] = 'viabill';
        } elseif ($order->getPayment()->getMethodInstance()->getCode() == 'reepay_mobilepay') {
            $paymentMethods[] = 'mobilepay';
        } else {
            $allowwedPaymentConfig = $this->getConfig('allowwed_payment', $order->getStoreId());
            $paymentMethods = explode(',', $allowwedPaymentConfig);
        }

        return $paymentMethods;
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

        return $paymentData;
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

            $transaction = $this->_transactionBuilder->setPayment($payment)
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

            
            $orderStatusAfterPayment = $this->getConfig('order_status_after_payment', $order->getStoreId());
            if (!empty($orderStatusAfterPayment)) {
                $totalDue = $this->_priceHelper->currency($order->getTotalDue(), true, false);

                $order->setState($orderStatusAfterPayment, true);
                $order->setStatus($orderStatusAfterPayment);
                $order->addStatusToHistory($order->getStatus(), 'Reepay : The authorized amount is '.$totalDue);
                $order->save();
            }

            return  $transaction->getTransactionId();
        } catch (Exception $e) {
            throw new \Magento\framework\Exception\PaymentException(__('addTransactionToOrder() Exception : '.$e->getMessage()));

            return;
        }
    }

    /**
     * Prepare capture transaction data
     *
     * @param array $transactionData
     * @return array $transactionData
     */
    public function prepareCaptureTransactionData($transactionData)
    {
        if (isset($transactionData['amount'])) {
            $transactionData['amount'] = $this->convertAmount($transactionData['amount']);
        }

        if (isset($transactionData['card_transaction'])) {
            $cardTransaction = $transactionData['card_transaction'];
            unset($transactionData['card_transaction']);
            $transactionData['card_transaction_ref_transaction'] = $cardTransaction['ref_transaction'];
            $transactionData['card_transaction_fingerprint'] = $cardTransaction['fingerprint'];
            $transactionData['card_transaction_card_type'] = $cardTransaction['card_type'];
            $transactionData['card_transaction_exp_date'] = $cardTransaction['exp_date'];
            $transactionData['card_transaction_masked_card'] = $cardTransaction['masked_card'];
        }

        return $transactionData;
    }

    /**
     * Create capture transaction
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array $transactionData
     * @return int (Magento Transaction ID)
     */
    public function addCaptureTransactionToOrder($order, $transactionData = [])
    {
        try {
            // prepare transaction data
            $transactionData = $this->prepareCaptureTransactionData($transactionData);

            //get payment object from order object
            $payment = $order->getPayment();
            $payment->setLastTransId($transactionData['id']);
            $payment->setTransactionId($transactionData['id']);
            $payment->setAdditionalInformation(
                [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $transactionData]
            );
            
            $formatedPrice = $order->getBaseCurrency()->formatTxt($transactionData['amount']);
            $message = __('Reepay : Captured amount of %1 by Reepay webhook.', $formatedPrice);
            
            $transaction = $this->_transactionBuilder->setPayment($payment)
                ->setOrder($order)
                ->setTransactionId($transactionData['id'])
                ->setAdditionalInformation(
                    [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $transactionData]
            )
            ->setFailSafe(true)
            ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);
 
            $payment->addTransactionCommentsToOrder(
                $transaction,
                $message
            );
            $payment->setParentTransactionId(null);
            $payment->save();
            $order->save();
 
            return  $transaction->save()->getTransactionId();
        } catch (Exception $e) {
            throw new \Magento\framework\Exception\PaymentException(__('addCaptureTransactionToOrder() Exception : '.$e->getMessage()));

            return;
        }
    }

    /**
     * Prepare refund transaction data
     *
     * @param array $transactionData
     * @return array $transactionData
     */
    public function prepareRefundTransactionData($transactionData)
    {
        if (isset($transactionData['amount'])) {
            $transactionData['amount'] = $this->convertAmount($transactionData['amount']);
        }

        if (isset($transactionData['card_transaction'])) {
            $cardTransaction = $transactionData['card_transaction'];
            unset($transactionData['card_transaction']);
            $transactionData['card_transaction_ref_transaction'] = $cardTransaction['ref_transaction'];
        }

        return $transactionData;
    }

    /**
     * Create refund transaction
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array $transactionData
     * @return int (Magento Transaction ID)
     */
    public function addRefundTransactionToOrder($order, $transactionData = [])
    {
        try {
            // prepare transaction data
            $transactionData = $this->prepareRefundTransactionData($transactionData);

            //get payment object from order object
            $payment = $order->getPayment();
            $payment->setLastTransId($transactionData['id']);
            $payment->setTransactionId($transactionData['id']);
            $payment->setAdditionalInformation(
                [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $transactionData]
            );
            
            $formatedPrice = $order->getBaseCurrency()->formatTxt($transactionData['amount']);
            $message = __('Reepay : Refunded amount of %1 by Reepay webhook.', $formatedPrice);
            
            $transaction = $this->_transactionBuilder->setPayment($payment)
                ->setOrder($order)
                ->setTransactionId($transactionData['id'])
                ->setAdditionalInformation(
                    [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $transactionData]
            )
            ->setFailSafe(true)
            ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND);
 
            $payment->addTransactionCommentsToOrder(
                $transaction,
                $message
            );
            $payment->setParentTransactionId(null);
            $payment->save();
            $order->save();
 
            return  $transaction->save()->getTransactionId();
        } catch (Exception $e) {
            throw new \Magento\framework\Exception\PaymentException(__('addRefundTransactionToOrder() Exception : '.$e->getMessage()));

            return;
        }
    }

    /**
     * Convert integer amount to 2 decimal places
     *
     * @param int $amount
     * @return float
     */
    public function convertAmount($amount)
    {
        return number_format((float)($amount/100), 2, '.', '');
    }
}
