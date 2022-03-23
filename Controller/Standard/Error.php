<?php

namespace Radarsofthouse\Reepay\Controller\Standard;

class Error extends \Magento\Framework\App\Action\Action
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
     * @var \Radarsofthouse\Reepay\Helper\Charge
     */
    private $reepayCharge;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Session
     */
    private $reepaySession;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Sales\Api\Data\OrderInterface $orderInterface
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Radarsofthouse\Reepay\Helper\Charge $reepayCharge
     * @param \Radarsofthouse\Reepay\Helper\Session $reepaySession
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Radarsofthouse\Reepay\Helper\Charge $reepayCharge,
        \Radarsofthouse\Reepay\Helper\Session $reepaySession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        $this->orderInterface = $orderInterface;
        $this->orderManagement = $orderManagement;
        $this->checkoutSession = $checkoutSession;
        $this->url = $context->getUrl();
        $this->scopeConfig = $scopeConfig;
        $this->reepayCharge = $reepayCharge;
        $this->reepaySession = $reepaySession;
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        //// start Accept controller

        $params = $this->request->getParams('');
        $orderId = $params['invoice'];
        $id = $params['id'];
        $_isAjax = 0;
        if (isset($params['_isAjax'])) {
            $_isAjax = 1;
        }
        
        if (empty($params['invoice']) || empty($params['id'])) {
            return;
        }
        /*
                if ($params['error'] && $params['error'] == 'error.session.INVOICE_ALREADY_PAID') {
                    if ($_isAjax == 1) {
                        $result = [
                            'status' => 'success',
                            'redirect_url' => $this->url->getUrl('checkout/onepage/success'),
                        ];

                        return  $this->resultJsonFactory->create()->setData($result);
                    } else {
                        //// 'reepay/standard/accept : redirect to success page'
                        $this->_redirect('checkout/onepage/success');
                    }
                }
                */
    }
}
