<?php

namespace Radarsofthouse\Reepay\Observer;

class SalesOrderPaymentCapture implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Radarsofthouse\Reepay\Helper\Data
     */
    protected $reepayHelper;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Charge
     */
    protected $reepayCharge;

    /**
     * @var \\Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Constructor
     *
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Radarsofthouse\Reepay\Helper\Charge $reepayCharge
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Radarsofthouse\Reepay\Helper\Logger $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Radarsofthouse\Reepay\Helper\Charge $reepayCharge,
        \Magento\Framework\Registry $registry
    ) {
        $this->reepayHelper = $reepayHelper;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->reepayCharge = $reepayCharge;
        $this->registry = $registry;
    }

    /**
     * Observe sales_order_payment_capture
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
            if ($payment->getMethodInstance()->isAutoCapture()) {
                $this->logger->addDebug("Skip settle request to Reepay for the 'auto_capture' payment.");
                return;
            }

            $order = $payment->getOrder();

            $apiKey = $this->reepayHelper->getApiKey($order->getStoreId());
            $reepay_charge = $this->reepayCharge->get(
                $apiKey,
                $order->getIncrementId()
            );

            if ($this->reepayHelper->getConfig('auto_capture', $order->getStoreId())) {
                if (!empty($reepay_charge)) {
                    if ($reepay_charge['state'] == 'settled') {
                        $this->logger->addDebug("auto capture is enabled : skip to settle again");
                        return;
                    }
                }
            }

            $amount = $invoice->getGrandTotal();

            if (isset($reepay_charge['authorized_amount']) && $reepay_charge['authorized_amount'] > 0) {
                $tmp_amount = $amount;
                $authorized_amount  = $reepay_charge['authorized_amount'];
                
                if ($this->reepayHelper->toInt($amount * 100) > $authorized_amount) {
                    $amount = $authorized_amount/100;
                }
                if ($amount != $tmp_amount) {
                    $this->logger->addDebug(
                        "Change capture amount from {$tmp_amount} to {$amount} for order" . $order->getIncrementId()
                    );
                }
            }

            $this->logger->addDebug(
                __METHOD__,
                ['capture : ' . $order->getIncrementId() . ', amount : ' . $amount]
            );

            $options = [];
            
            if ($this->reepayHelper->getConfig('send_order_line', $order->getStoreId())) {
                $options['order_lines'] = $this->reepayHelper->getOrderLinesFromInvoice($invoice);
            } else {
                $_amount = $amount * 100;
                $options['amount'] = $this->reepayHelper->toInt($_amount);
            }

            $charge = null;
            if( $this->registry->registry('is_reepay_settled_webhook') == 1 ){
                // When invoice created from the settled webhook then don't do the settle request to Reepay
                $this->logger->addDebug("Skip settle request to Reepay when invoice is created from Reepay settled webhook");
                $charge = $reepay_charge;
            }else{
                $charge = $this->reepayCharge->settle(
                    $apiKey,
                    $order->getIncrementId(),
                    $options
                );
            }

            if (!empty($charge)) {
                if (isset($charge["error"])) {
                    $this->logger->addDebug("settle error : ", $charge);
                    $error_message = $charge["error"];
                    if (isset($charge["message"])) {
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
