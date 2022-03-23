<?php

namespace Radarsofthouse\Reepay\Controller\Standard;

class SetCreditCard extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Radarsofthouse\Reepay\Helper\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Radarsofthouse\Reepay\Helper\Logger $logger
    ) {
        $this->_request = $request;
        $this->_checkoutSession = $checkoutSession;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->_request->getParams();
        $this->_logger->addDebug(__METHOD__, $params);

        $result = [];

        if (empty($params['cid'])) {
            $result['status'] = 'failure';
            $result['message'] = __('Empty credit card ID.');
            return $this->_resultJsonFactory->create()->setHeader(
                'Cache-Control',
                'no-store, no-cache, must-revalidate, max-age=0',
                true
            )->setData($result);
        }

        try {
            $quote = $this->_checkoutSession->getQuote();
            $quote->setReepayCreditCard($params['cid']);
            $quote->save();

            $this->_logger->addDebug('setReepayCreditCard : '.$params['cid']);

            $result['status'] = 'success';
            return $this->_resultJsonFactory->create()->setHeader(
                'Cache-Control',
                'no-store, no-cache, must-revalidate, max-age=0',
                true
            )->setData($result);
        } catch (\Exception $e) {
            $result['status'] = 'failure';
            $result['message'] = $e->getMessage();
            return $this->_resultJsonFactory->create()->setHeader(
                'Cache-Control',
                'no-store, no-cache, must-revalidate, max-age=0',
                true
            )->setData($result);
        }
    }
}
