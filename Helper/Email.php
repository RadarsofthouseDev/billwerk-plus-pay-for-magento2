<?php

namespace Radarsofthouse\Reepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Email
 *
 * @package Radarsofthouse\Reepay\Helper
 */
class Email extends AbstractHelper
{
    protected $_transportBuilder;
    protected $_reepayHelper;
    protected $_scopeConfig;
    protected $_logger;
    protected $_storeManager;
    protected $_orderInterface;
    protected $_orderSender;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param Data $reepayHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Radarsofthouse\Reepay\Helper\Logger $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
    ) {
        parent::__construct($context);
        $this->_transportBuilder = $transportBuilder;
        $this->_reepayHelper = $reepayHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
        $this->_orderInterface = $orderInterface;
        $this->_orderSender = $orderSender;
    }

    public function sendPaymentLinkEmail($order, $paymentTransactionId)
    {
        $emailTemplateId = $this->_reepayHelper->getConfig('payment_link', $order->getStoreId());
        $this->_logger->addDebug('$emailTemplateId : ' . $emailTemplateId);

        if (empty($emailTemplateId)) {
            $emailTemplateId = "payment_reepay_payment_payment_link";
        }

        $senderName = $this->_scopeConfig->getValue(
            'trans_email/ident_sales/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $order->getStoreId()
        );

        $senderEmail = $this->_scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $order->getStoreId()
        );

        $this->_logger->addDebug('$senderName : ' . $senderName);
        $this->_logger->addDebug('$senderEmail : ' . $senderEmail);

        $transport = $this->_transportBuilder->setTemplateIdentifier($emailTemplateId)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $order->getStoreId(),
                    ]
                )
                ->setTemplateVars(
                    [
                        "increment_id" => $order->getIncrementId(),
                        "payment_url" => 'https://checkout.reepay.com/#/' . $paymentTransactionId
                    ]
                )
                ->setFrom(
                    [
                        'name' => $senderName,
                        'email' => $senderEmail
                    ]
                )
                ->addTo(
                    $order->getCustomerEmail(),
                    $order->getCustomerName()
                )
                ->getTransport();
        $sendResult = $transport->sendMessage();
        $this->_logger->addDebug('$sendResult');
        $this->_logger->addDebug($sendResult);
    }

    /**
     * @param $orderId
     */
    public function sendEmail($orderIncrementId)
    {
        $this->_logger->addDebug(__METHOD__, ['orderIncrementId' => $orderIncrementId]);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_orderInterface->loadByIncrementId($orderIncrementId);
        $this->_logger->addDebug('order before send email.', [
            'sub_total'=> $order->getSubtotal(),
            'surcharge_fee'=> $order->getReepaySurchargeFee(),
            'grand_total'=> $order->getGrandTotal(),
        ]);
        if ($this->_reepayHelper->getConfig('send_order_email_when_success', $order->getStoreId()) && !$order->getEmailSent()) {
            try {
                $this->_orderSender->send($order);
                $historyItem  = $order->addCommentToStatusHistory(__('Sent order confirmation email to customer'));
                $historyItem->setIsCustomerNotified(true)->save();
            } catch (\Exception $e) {
                $historyItem  = $order->addCommentToStatusHistory(__('Send order confirmation email failure: %s', $e->getMessage()));
                $historyItem->setIsCustomerNotified(false)->save();
            }
        }
    }
}
