<?php

namespace Radarsofthouse\Reepay\Observer;

class SalesOrderPaymentRefund implements \Magento\Framework\Event\ObserverInterface
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
     * @var \Radarsofthouse\Reepay\Helper\Refund
     */
    protected $reepayRefund;

    /**
     * Constructor
     *
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Radarsofthouse\Reepay\Helper\Refund $reepayRefund
     */
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
     * Observe sales_order_payment_refund
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $observer->getPayment();
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getCreditmemo();
        $paymentMethod = $payment->getMethod();

        $isOnline = $creditmemo->getDoTransaction();

        if ($this->reepayHelper->isReepayPaymentMethod($paymentMethod)) {
            $order = $payment->getOrder();
            $amount = $creditmemo->getGrandTotal();

            if (!$isOnline) {
                $this->logger->addDebug(
                    __METHOD__,
                    ['offline_refund : ' . $order->getIncrementId() . ', amount : ' . $amount]
                );
                return;
            }

            $this->logger->addDebug(
                __METHOD__,
                ['online_refund : ' . $order->getIncrementId() . ', amount : ' . $amount]
            );

            $options = [];
            $options['invoice'] = $order->getIncrementId();
            $_amount = $amount * 100;
            $options['amount'] = $this->reepayHelper->toInt($_amount);
            $options['ordertext'] = "refund";

            $apiKey = $this->reepayHelper->getApiKey($order->getStoreId());

            $refund = $this->reepayRefund->create(
                $apiKey,
                $options
            );

            if (!empty($refund)) {
                if (isset($refund["error"])) {
                    $this->logger->addDebug("refund error : ", $refund);
                    $error_message = $refund["error"];
                    if (isset($refund["message"])) {
                        $error_message = $refund["error"]." : ".$refund["message"];
                    }
                    throw new \Magento\Framework\Exception\LocalizedException(__($error_message));
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
            } else {
                $this->logger->addDebug("Empty refund response from Reepay");
                $this->messageManager->addErrorMessage("Empty refund response from Reepay");
                throw new \Magento\Framework\Exception\LocalizedException(__('Empty refund response from Reepay'));
            }
        }
    }
}
