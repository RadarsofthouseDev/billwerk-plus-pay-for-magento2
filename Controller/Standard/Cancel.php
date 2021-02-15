<?php

namespace Radarsofthouse\Reepay\Controller\Standard;

/**
 * Class Cancel
 *
 * @package Radarsofthouse\Reepay\Controller\Standard
 */
class Cancel extends \Magento\Framework\App\Action\Action
{
    private $orderInterface;
    private $resultPageFactory;
    private $reepaySession;
    private $logger;
    protected $request;
    protected $orderManagement;
    protected $checkoutSession;
    protected $url;
    protected $reepayHelper;
    protected $scopeConfig;
    protected $resultJsonFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Sales\Api\Data\OrderInterface $orderInterface
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Radarsofthouse\Reepay\Helper\Session $reepaySession
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Radarsofthouse\Reepay\Helper\Session $reepaySession,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Radarsofthouse\Reepay\Helper\Logger $logger
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        $this->orderInterface = $orderInterface;
        $this->checkoutSession = $checkoutSession;
        $this->url = $context->getUrl();
        $this->reepaySession = $reepaySession;
        $this->reepayHelper = $reepayHelper;
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
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
        $params = $this->request->getParams();
        $this->logger->addDebug(__METHOD__, $params);
        if (!empty($params['error'])) {
            if ($params['error'] === "error.session.SESSION_DELETED") {
                return $this->redirect('checkout/cart');
            }
        }
        if (empty($params['invoice']) || empty($params['id'])) {
            return $this->redirect('checkout/cart');
        }

        $orderId = $params['invoice'];
        $id = $params['id'];
        $isAjax = 0;
        if (isset($params['_isAjax'])) {
            $isAjax = 1;
        }
        $order = $this->orderInterface->loadByIncrementId($orderId);
        $cancelConfig = $this->reepayHelper->getConfig('cancel_order_after_payment_cancel', $order->getStoreId());

        if ($cancelConfig && $order->canCancel()) {
            $order->cancel();
            $order->addStatusHistoryComment('Reepay : order have been cancelled by payment page');
            $order->save();
            $this->logger->addDebug('Cancelled order : ' . $orderId);
            $apiKey = $this->reepayHelper->getApiKey($order->getStoreId());
            $payment = $order->getPayment();
            $this->reepayHelper->setReepayPaymentState($payment, 'cancelled');
            // delete reepay session
            $sessionRes = $this->reepaySession->delete(
                $apiKey,
                $id
            );
            $this->checkoutSession->restoreQuote();
            $this->checkoutSession->unsLastQuoteId()
                ->unsLastSuccessQuoteId()
                ->unsLastOrderId()
                ->unsLastRealOrderId();
        }


        // unset reepay session id on checkout session
        /*
        if ($this->checkoutSession->getReepaySessionID() && $this->checkoutSession->getReepaySessionOrder()) {
            $this->checkoutSession->unsReepaySessionID();
            $this->checkoutSession->unsReepaySessionOrder();
        }
        */

        if ($isAjax === 1) {
            $result = [
                'status' => 'success',
                'redirect_url' => $this->url->getUrl('checkout/cart'),
            ];
            return $this->resultJsonFactory->create()
                ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true)
                ->setData($result);
        }
        if (!$cancelConfig) {
            return $this->redirect('/');
        }
        return $this->redirect('checkout/cart');
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function redirect($path)
    {
        $resultPage = $this->resultRedirectFactory->create()->setPath($path);
        $resultPage->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        return $resultPage;
    }
}
