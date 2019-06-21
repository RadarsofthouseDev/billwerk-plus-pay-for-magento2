<?php
 
namespace Radarsofthouse\Reepay\Model;

/**
 * Class Reepaypayment
 *
 * @package Radarsofthouse\Reepay\Model
 */
class Reepaypayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = 'reepay_payment';
    protected $_isInitializeNeeded = true;
    protected $_canUseInternal = true;
    protected $_canCapture = true;
    protected $_canRefund = true;
    protected $_isGateway = true;
    protected $_canCapturePartial = true;
    protected $_canRefundInvoicePartial = true;
    
    /**
     * override capture payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if (!$this->canCapture()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The capture action is not available.'));
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reepayCharge = $objectManager->create('Radarsofthouse\Reepay\Helper\Charge');
        $reepayHelper = $objectManager->create('Radarsofthouse\Reepay\Helper\Data');
        $logger = $objectManager->create('Radarsofthouse\Reepay\Helper\Logger');

        $order = $payment->getOrder();
        $amount = $amount;

        $logger->addDebug(__METHOD__, ['capture : '.$order->getIncrementId()]);

        $orderInvoices = $order->getInvoiceCollection();

        $options = [];
        $options['key'] = count($orderInvoices);
        $options['amount'] = $amount*100;
        $options['ordertext'] = "settled";

        $apiKey = $reepayHelper->getApiKey($order->getStoreId());
        
        $charge = $reepayCharge->settle(
            $apiKey,
            $order->getIncrementId(),
            $options
        );

        if (!empty($charge)) {
            if ($charge['state'] == 'settled') {
                $_payment = $order->getPayment();
                $reepayHelper->setReepayPaymentState($_payment, 'settled');
                $order->save();

                $logger->addDebug('settled : '.$order->getIncrementId());

                
                // separate transactions for partial capture
                $payment->setIsTransactionClosed(false);
                $payment->setTransactionId($charge['transaction']);
                $transactionData = [
                    'handle' => $charge['handle'],
                    'transaction' => $charge['transaction'],
                    'state' => $charge['state'],
                    'amount' => $amount,
                    'customer' => $charge['customer'],
                    'currency' => $charge['currency'],
                    'created' => $charge['created'],
                    'authorized' => $charge['authorized'],
                    'settled' => $charge['settled']
                ];
                $payment->setTransactionAdditionalInfo(
                    \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
                    $transactionData
                );

                $logger->addDebug('set capture transaction data');
            }
        } else {
            $logger->addError('Empty charge response');
        }

        return $this;
    }

    /**
     * override refund payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if (!$this->canRefund()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The refund action is not available.'));
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reepayRefund = $objectManager->create('Radarsofthouse\Reepay\Helper\Refund');
        $reepayHelper = $objectManager->create('Radarsofthouse\Reepay\Helper\Data');
        $logger = $objectManager->create('Radarsofthouse\Reepay\Helper\Logger');
        
        $order = $payment->getOrder();
        $amount = $amount;

        $logger->addDebug(__METHOD__, ['capture : '.$order->getIncrementId()]);

        $creditmemos = $order->getCreditmemosCollection();

        $options = [];
        $options['invoice'] = $order->getIncrementId();
        $options['key'] = count($creditmemos);
        $options['amount'] = $amount*100;
        $options['ordertext'] = "refund";


        $apiKey = $reepayHelper->getApiKey($order->getStoreId());

        $refund = $reepayRefund->create(
            $apiKey,
            $options
        );
        if (!empty($refund)) {
            if ($refund['state'] == 'refunded') {
                $_payment = $order->getPayment();
                $reepayHelper->setReepayPaymentState($_payment, 'refunded');
                $order->save();

                // separate transactions for partial refund
                $payment->setIsTransactionClosed(false);
                $payment->setTransactionId($refund['transaction']);
                $transactionData = [
                    'invoice' => $refund['invoice'],
                    'transaction' => $refund['transaction'],
                    'state' => $refund['state'],
                    'amount' => $reepayHelper->convertAmount($refund['amount']),
                    'type' => $refund['type'],
                    'currency' => $refund['currency'],
                    'created' => $refund['created']
                ];

                $payment->setTransactionAdditionalInfo(
                    \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
                    $transactionData
                );

                $logger->addDebug("set refund transaction data");
            } else {
                $logger->addDebug("Refund state is not refunded");
            }
        }

        return $this;
    }
}
