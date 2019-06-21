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

    protected $_resultPageFactory;
    protected $_reepayHelper;
    protected $_reepayPayment;
    protected $_messageManager;
    protected $_logger;
    protected $_reepayStatus;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     * @param \Radarsofthouse\Reepay\Model\Status $reepayStatus
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Radarsofthouse\Reepay\Helper\Payment $reepayPayment,
        \Radarsofthouse\Reepay\Helper\Logger $logger,
        \Radarsofthouse\Reepay\Model\Status $reepayStatus
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_reepayHelper = $reepayHelper;
        $this->_reepayPayment = $reepayPayment;
        $this->_messageManager = $context->getMessageManager();
        $this->_logger = $logger;
        $this->_reepayStatus = $reepayStatus;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $this->_logger->addDebug(__METHOD__, []);

            if (!$this->_objectManager->get(\Magento\Checkout\Model\Session\SuccessValidator::class)->isValid()) {
                return $this->_resultPageFactory->create()->setPath('checkout/cart');
            }

            // get order ID from onepage checkout session
            $checkoutOnepageSession = $this->_objectManager->get(\Magento\Checkout\Model\Type\Onepage::class)->getCheckout();
            $orderId = $checkoutOnepageSession->getLastOrderId();
            $order = $this->_objectManager->create('\Magento\Sales\Model\Order')->load($orderId);

            if (!$order->getId()) {
                return $this->_resultPageFactory->create()->setPath('checkout/cart');
            }

            $paymentTransactionId = null;
            $paymentTransactionId = $this->_reepayPayment->createReepaySession($order);

            $this->_logger->addDebug('$paymentTransactionId : '.$paymentTransactionId);

            // render reepay/standard/redirect
            $pageTitleConfig = $this->_reepayHelper->getConfig('title', $order->getStoreId());
            $resultPage = $this->_resultPageFactory->create();
            $resultPage->getConfig()
                ->getTitle()
                ->set($pageTitleConfig);
            
            $displayTypeConfig = $this->_reepayHelper->getConfig('display_type', $order->getStoreId());
            
            if ($order->getPayment()->getMethodInstance()->getCode() == 'reepay_viabill') {
                $this->_logger->addDebug('reepay_viabill : DISPLAY_WINDOW');

                // force viabill into payment window always
                $resultPage->getLayout()
                    ->getBlock('reepay_standard_redirect')
                    ->setTemplate('Radarsofthouse_Reepay::standard/window.phtml')
                    ->setPaymentTransactionId($paymentTransactionId);
            } elseif ($displayTypeConfig == SELF::DISPLAY_EMBEDDED) {
                $this->_logger->addDebug('DISPLAY_EMBEDDED');

                $resultPage->getLayout()
                    ->getBlock('reepay_standard_redirect')
                    ->setTemplate('Radarsofthouse_Reepay::standard/embedded.phtml')
                    ->setPaymentTransactionId($paymentTransactionId);
            } elseif ($displayTypeConfig == SELF::DISPLAY_OVERLAY) {
                $this->_logger->addDebug('DISPLAY_OVERLAY');

                $resultPage->getLayout()
                    ->getBlock('reepay_standard_redirect')
                    ->setTemplate('Radarsofthouse_Reepay::standard/overlay.phtml')
                    ->setPaymentTransactionId($paymentTransactionId);
            } elseif ($displayTypeConfig == SELF::DISPLAY_WINDOW) {
                $this->_logger->addDebug('DISPLAY_WINDOW');

                $resultPage->getLayout()
                    ->getBlock('reepay_standard_redirect')
                    ->setTemplate('Radarsofthouse_Reepay::standard/window.phtml')
                    ->setPaymentTransactionId($paymentTransactionId);
            }
            
            return $resultPage;
        } catch (\Exception $e) {
            $this->_logger->addError(__METHOD__." Exception : ".$e->getMessage());

            $this->_messageManager->addException($e, __('Something went wrong, please try again later'));
            $this->_redirect('checkout/cart');
        }
    }
}
