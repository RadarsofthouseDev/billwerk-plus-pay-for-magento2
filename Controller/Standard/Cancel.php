<?php

namespace Radarsofthouse\Reepay\Controller\Standard;

class Cancel extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    private $orderInterface;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Session
     */
    private $reepaySession;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Logger
     */
    private $logger;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Data
     */
    protected $reepayHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Sales\Api\Data\TransactionSearchResultInterfaceFactory
     */
    protected $transactionSearchResultInterfaceFactory;

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
     * @param \Magento\Sales\Api\Data\TransactionSearchResultInterfaceFactory $transactionSearchResultInterfaceFactory
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
        \Radarsofthouse\Reepay\Helper\Logger $logger,
        \Magento\Sales\Api\Data\TransactionSearchResultInterfaceFactory $transactionSearchResultInterfaceFactory
    ) {
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
        $this->transactionSearchResultInterfaceFactory = $transactionSearchResultInterfaceFactory;

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

            $transactions = $this->transactionSearchResultInterfaceFactory->create()->addOrderIdFilter($order->getId());

            // don't allowed the cancelation if already have transactions (payment is paid)
            if( count($transactions->getItems()) == 0 ){
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
            }else{
                $this->logger->addDebug('The payment is done : ignore cancellation for order ' . $orderId);
            }
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
     * Redirect
     *
     * @param string $path
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function redirect($path)
    {
        $resultPage = $this->resultRedirectFactory->create()->setPath($path);
        $resultPage->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        return $resultPage;
    }
}
