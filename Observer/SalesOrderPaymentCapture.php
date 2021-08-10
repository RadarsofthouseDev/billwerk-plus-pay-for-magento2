<?php

namespace Radarsofthouse\Reepay\Observer;

/**
 * Class SalesOrderPaymentCapture observer 'sales_order_payment_capture' event
 *
 * @package Radarsofthouse\Reepay\Observer
 */
class SalesOrderPaymentCapture implements \Magento\Framework\Event\ObserverInterface
{
    protected $reepayHelper;
    protected $logger;
    protected $messageManager;
    protected $reepayCharge;

    public function __construct(
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Radarsofthouse\Reepay\Helper\Logger $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Radarsofthouse\Reepay\Helper\Charge $reepayCharge
    ) {
        $this->reepayHelper = $reepayHelper;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->reepayCharge = $reepayCharge;
    }

    /**
     * sales_order_payment_capture observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $payment = $observer->getPayment();
        $invoice = $observer->getInvoice();
        $paymentMethod = $payment->getMethod();
        if ($this->reepayHelper->isReepayPaymentMethod($paymentMethod)) {
            if ($paymentMethod === 'reepay_swish') {
                return;
            }

            $order = $payment->getOrder();

            $apiKey = $this->reepayHelper->getApiKey($order->getStoreId());
            $reepay_charge = $this->reepayCharge->get(
                $apiKey,
                $order->getIncrementId()
            );

            if( $this->reepayHelper->getConfig('auto_capture', $order->getStoreId()) ){
                if (!empty($reepay_charge)) {
                    if ($reepay_charge['state'] == 'settled') {
                        $this->logger->addDebug("auto capture is enabled : skip to settle again");
                        return;
                    }
                }
            }

            $amount = $invoice->getGrandTotal();
            $originalAmount  = $order->getGrandTotal();

            if ($amount > $order->getGrandTotal()) {
                $amount = $order->getGrandTotal();
            }

            if ($amount != $originalAmount) {
                $this->logger->addDebug("Change capture amount from {$amount} to {$originalAmount} for order" . $order->getIncrementId());
            }

            $this->logger->addDebug(__METHOD__, ['capture : ' . $order->getIncrementId() . ', amount : ' . $amount]);

            $options = [];
            
            if( $this->reepayHelper->getConfig('send_order_line', $order->getStoreId()) ){
                $options['order_lines'] = $this->reepayHelper->getOrderLinesFromInvoice($invoice);
            }else{
                $options['amount'] = $amount*100;
            }

            $charge = $this->reepayCharge->settle(
                $apiKey,
                $order->getIncrementId(),
                $options
            );

            if (!empty($charge)) {
                if (isset($charge["error"])) {
                    $this->logger->addDebug("settle error : ", $charge);
                    $error_message = $charge["error"];
                    if( isset($charge["message"]) ){
                        $error_message = $charge["error"]." : ".$charge["message"];
                    }
                    throw new \Magento\Framework\Exception\LocalizedException(__($error_message));
                    return;
                }

                if ($charge['state'] == 'settled') {
                    $_payment = $order->getPayment();
                    $this->reepayHelper->setReepayPaymentState($_payment, 'settled');
                    $order->save();

                    $this->logger->addDebug('settled : ' . $order->getIncrementId());

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

                    $this->logger->addDebug('set capture transaction data');
                }
            } else {
                $this->logger->addDebug("Empty settle response from Reepay");
                $this->messageManager->addError("Empty settle response from Reepay");
                throw new \Magento\Framework\Exception\LocalizedException(__("Empty settle response from Reepay"));
            }
        }
    }
}
