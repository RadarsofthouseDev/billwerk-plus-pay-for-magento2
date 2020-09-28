<?php

namespace Radarsofthouse\Reepay\Observer;

/**
 * Class SendPaymentLink
 *
 * @package Radarsofthouse\Reepay\Observer
 */
class SendPaymentLink implements \Magento\Framework\Event\ObserverInterface
{
    protected $_reepayCharge;
    protected $_reepaySession;
    protected $_logger;
    protected $_state;
    protected $_scopeConfig;
    protected $_reepayHelper;
    protected $_reepayEmail;
    protected $_reepayPayment;
    protected $_messageManager;

    /**
     * Constructor
     *
     * @param \Radarsofthouse\Reepay\Helper\Charge $reepayCharge
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Radarsofthouse\Reepay\Helper\Session $reepaySession
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     * @param \Radarsofthouse\Reepay\Helper\Payment $reepayPayment
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Radarsofthouse\Reepay\Helper\Charge $reepayCharge,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Radarsofthouse\Reepay\Helper\Session $reepaySession,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Radarsofthouse\Reepay\Helper\Email $reepayEmail,
        \Radarsofthouse\Reepay\Helper\Payment $reepayPayment,
        \Radarsofthouse\Reepay\Helper\Logger $logger,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_reepayCharge = $reepayCharge;
        $this->_scopeConfig = $scopeConfig;
        $this->_reepaySession = $reepaySession;
        $this->_reepayHelper = $reepayHelper;
        $this->_reepayEmail = $reepayEmail;
        $this->_reepayPayment = $reepayPayment;
        $this->_logger = $logger;
        $this->_state = $state;
        $this->_messageManager = $messageManager;

    }

    /**
     * checkout_submit_all_after observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData('order');
        $this->_logger->addDebug(__METHOD__, [$order->getIncrementId()]);

        if ($this->_state->getAreaCode() == 'adminhtml') {
            $orderPaymentMethod = $order->getPayment()->getMethodInstance()->getCode();
            $this->_logger->addDebug(__METHOD__, [$orderPaymentMethod]);
            if($this->_reepayHelper->isReepayPaymentMethod($orderPaymentMethod)) {
                try {
                    $sessionId = $this->_reepayPayment->createReepaySession($order);

                    if (empty($sessionId)) {
                        $this->_logger->addDebug("Cannot create Reepay payment session");
                        $this->_messageManager->addError(__('Cannot create Reepay payment session'));

                        return;
                    }
                    $this->_logger->addDebug("SEND EMAIL");
                    $this->_reepayEmail->sendPaymentLinkEmail($order, $sessionId);




                } catch (Exception $e) {
                    $this->_logger->addError(__METHOD__." Exception : ".$e->getMessage());
                    $this->_messageManager->addException($e, $e->getMessage());
                }

            }
        }
    }
}
