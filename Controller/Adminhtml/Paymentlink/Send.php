<?php

namespace Radarsofthouse\Reepay\Controller\Adminhtml\Paymentlink;

class Send extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Payment
     */
    protected $reepayPayment;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Email
     */
    protected $reepayEmail;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Logger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Radarsofthouse\Reepay\Helper\Payment $reepayPayment
     * @param \Radarsofthouse\Reepay\Helper\Email $reepayEmail
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Radarsofthouse\Reepay\Helper\Payment $reepayPayment,
        \Radarsofthouse\Reepay\Helper\Email $reepayEmail,
        \Radarsofthouse\Reepay\Helper\Logger $logger
    ) {
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->backendUrl = $backendUrl;
        $this->messageManager = $messageManager;
        $this->orderRepository = $orderRepository;
        $this->reepayPayment = $reepayPayment;
        $this->reepayEmail = $reepayEmail;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute
     */
    public function execute()
    {
        $order_id = $this->request->getParam("order_id");
        $order = $this->orderRepository->get($order_id);

        $this->logger->addDebug(__METHOD__, [$order->getIncrementId()]);

        try {
            $sessionId = $this->reepayPayment->createReepaySession($order);

            if (empty($sessionId)) {
                $this->logger->addDebug("Cannot create Reepay payment session");
                $this->messageManager->addError(__('Cannot create Reepay payment session'));

                return;
            }

            $this->reepayEmail->sendPaymentLinkEmail($order, $sessionId);

            $this->messageManager->addSuccess(__("Payment link email has been sent to the customer."));
        } catch (\Exception $e) {
            $this->logger->addError(__METHOD__." Exception : ".$e->getMessage());
            $this->messageManager->addException($e, $e->getMessage());
        }

        $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $redirectUrl = $this->backendUrl->getUrl('sales/order/view/', ['order_id' => $order_id]);
        $redirect->setUrl($redirectUrl);

        return $redirect;
    }
}
