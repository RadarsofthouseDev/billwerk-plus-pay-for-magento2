<?php

namespace Radarsofthouse\Reepay\Observer;

/**
 * Class SalesOrderPaymentRefund observer 'sales_order_payment_refund' event
 *
 * @package Radarsofthouse\Reepay\Observer
 */
class SalesOrderPaymentRefund implements \Magento\Framework\Event\ObserverInterface
{

    protected $reepayHelper;
    protected $logger;
    protected $messageManager;
    protected $reepayRefund;

    public function __construct(
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Radarsofthouse\Reepay\Helper\Logger $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Radarsofthouse\Reepay\Helper\Refund $reepayRefund
    ) {
        $this->reepayHelper = $reepayHelper;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->reepayRefund = $reepayRefund;
    }

    /**
     * sales_order_payment_place_start observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $payment = $observer->getPayment();
        $creditmemo = $observer->getCreditmemo();
        
        if ( $payment->getMethod() == 'reepay_payment' 
            || $payment->getMethod() == 'reepay_viabill'
            || $payment->getMethod() == 'reepay_mobilepay'
            || $payment->getMethod() == 'reepay_applepay'
            || $payment->getMethod() == 'reepay_paypal'
            || $payment->getMethod() == 'reepay_klarnapaynow'
            || $payment->getMethod() == 'reepay_klarnapaylater'
            || $payment->getMethod() == 'reepay_swish'
            || $payment->getMethod() == 'reepay_resurs'
            || $payment->getMethod() == 'reepay_forbrugsforeningen'
        ) {
            
            $order = $payment->getOrder();
            $amount = $creditmemo->getGrandTotal();

            $this->logger->addDebug(__METHOD__, ['refund : '.$order->getIncrementId().', amount : '.$amount]);

            $creditmemos = $order->getCreditmemosCollection();

            $options = [];
            $options['invoice'] = $order->getIncrementId();
            $options['key'] = count($creditmemos);
            $options['amount'] = $amount*100;
            $options['ordertext'] = "refund";

            $apiKey = $this->reepayHelper->getApiKey($order->getStoreId());

            $refund = $this->reepayRefund->create(
                $apiKey,
                $options
            );

            if (!empty($refund)) {

                if( isset($refund["error"]) ){
                    $this->logger->addDebug("refund error : ",$refund);
                    $this->messageManager->addError($refund["error"]);
                    throw new \Magento\Framework\Exception\LocalizedException($refund["error"]);
                    return;
                }

                if ($refund['state'] == 'refunded') {
                    $_payment = $order->getPayment();
                    $this->reepayHelper->setReepayPaymentState($_payment, 'refunded');
                    $order->save();

                    // separate transactions for partial refund
                    $payment->setIsTransactionClosed(false);
                    $payment->setTransactionId($refund['transaction']);
                    $transactionData = [
                        'invoice' => $refund['invoice'],
                        'transaction' => $refund['transaction'],
                        'state' => $refund['state'],
                        'amount' => $this->reepayHelper->convertAmount($refund['amount']),
                        'type' => $refund['type'],
                        'currency' => $refund['currency'],
                        'created' => $refund['created']
                    ];

                    $payment->setTransactionAdditionalInfo(
                        \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
                        $transactionData
                    );

                    $this->logger->addDebug("set refund transaction data");
                }
            }else{
                $this->logger->addDebug("Empty refund response from Reepay");
                $this->messageManager->addError("Empty refund response from Reepay");
                throw new \Magento\Framework\Exception\LocalizedException("Empty refund response from Reepay");
                return;
            }

        }
    }
}
