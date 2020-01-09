<?php

namespace Radarsofthouse\Reepay\Controller\Standard;

/**
 * Class Accept
 *
 * @package Radarsofthouse\Reepay\Controller\Standard
 */
class Accept extends \Magento\Framework\App\Action\Action
{
    protected $_orderInterface;
    protected $_reepayCharge;
    protected $_reepaySession;
    protected $_logger;
    protected $_request;
    protected $_url;
    protected $_resultJsonFactory;
    protected $_reepayHelper;
    protected $_reepayStatus;
    protected $_priceHelper;
    protected $_orderSender;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Sales\Api\Data\OrderInterface $orderInterface
     * @param \Radarsofthouse\Reepay\Helper\Charge $reepayCharge
     * @param \Radarsofthouse\Reepay\Helper\Session $reepaySession
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     * @param \Radarsofthouse\Reepay\Model\Status $reepayStatus
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Radarsofthouse\Reepay\Helper\Charge $reepayCharge,
        \Radarsofthouse\Reepay\Helper\Session $reepaySession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Radarsofthouse\Reepay\Helper\Logger $logger,
        \Radarsofthouse\Reepay\Model\Status $reepayStatus,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
    ) {
        $this->_request = $request;
        $this->_orderInterface = $orderInterface;
        $this->_url = $context->getUrl();
        $this->_reepayCharge = $reepayCharge;
        $this->_reepaySession = $reepaySession;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_reepayHelper = $reepayHelper;
        $this->_logger = $logger;
        $this->_reepayStatus = $reepayStatus;
        $this->_priceHelper = $priceHelper;
        $this->_orderSender = $orderSender;

        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->_request->getParams('');
        $orderId = $params['invoice'];
        $id = $params['id'];
        $isAjax = 0;
        if (isset($params['_isAjax'])) {
            $isAjax = 1;
        }
        
        $this->_logger->addDebug(__METHOD__, $params);

        if (empty($params['invoice']) || empty($params['id'])) {
            return;
        }

        $order = $this->_orderInterface->loadByIncrementId($orderId);
        $apiKey = $this->_reepayHelper->getApiKey($order->getStoreId());

        $reepayStatus = $this->_reepayStatus->load($orderId, 'order_id');
        if ($reepayStatus->getStatusId()) {
            $this->_logger->addDebug('order : '.$orderId.' has been accepted already');
            
            if( $this->_reepayHelper->getConfig('send_order_email_when_success', $order->getStoreId() ) ){
                if (!$order->getEmailSent()) {
                    try {
                        $this->_orderSender->send($order);
                        $order->addStatusHistoryComment(__('Sent order confirmation email to customer'))
                            ->setIsCustomerNotified(true)
                            ->save();
                    } catch (\Exception $e) {
                        $order->addStatusHistoryComment(__('Send order confirmation email failure: %s', $e->getMessage()))
                            ->setIsCustomerNotified(false)
                            ->save();
                    }
                }
            }

            // delete reepay session
            $sessionRes = $this->_reepaySession->delete(
                $apiKey,
                $id
            );
            

            if ($isAjax == 1) {
                $result = [];
                $result['status'] = 'success';
                if (!empty($order->getRemoteIp())) {
                    // place online
                    $result['redirect_url'] = $this->_url->getUrl('checkout/onepage/success');
                } else {
                    // place by admin
                    $result['redirect_url'] = $this->_url->getUrl('reepay/standard/success');
                }

                return  $this->_resultJsonFactory->create()->setData($result);
            } else {
                $this->_logger->addDebug('Redirect to checkout/onepage/success');
                if (!empty($order->getRemoteIp())) {
                    // place online
                    $this->_redirect('checkout/onepage/success');
                } else {
                    // place by admin
                    $this->_redirect('reepay/standard/success');
                }
            }
        }
        
        $chargeRes = $this->_reepayCharge->get(
            $apiKey,
            $orderId
        );

        // add Reepay payment data
        $data = [
            'order_id' => $orderId,
            'first_name' => $order->getBillingAddress()->getFirstname(),
            'last_name' => $order->getBillingAddress()->getLastname(),
            'email' => $order->getCustomerEmail(),
            'token' => $params['id'],
            'masked_card_number' => isset($chargeRes['source']['masked_card']) ? $chargeRes['source']['masked_card'] : '',
            'fingerprint' => isset($chargeRes['source']['fingerprint']) ? $chargeRes['source']['fingerprint'] : '',
            'card_type' => isset($chargeRes['source']['card_type']) ? $chargeRes['source']['card_type'] : '',
            'status' => $chargeRes['state'],
        ];
        $newReepayStatus = $this->_reepayStatus;
        $newReepayStatus->setData($data);
        $newReepayStatus->save();

        $this->_reepayHelper->addTransactionToOrder($order, $chargeRes);

        if ($this->_reepayHelper->getConfig('send_order_email_when_success', $order->getStoreId())) {
            if (!$order->getEmailSent()) {
                try {
                    $this->_orderSender->send($order);
                    $order->addStatusHistoryComment(__('Sent order confirmation email to customer'))
                        ->setIsCustomerNotified(true)
                        ->save();
                } catch (\Exception $e) {
                    $order->addStatusHistoryComment(__('Send order confirmation email failure: %s', $e->getMessage()))
                        ->setIsCustomerNotified(false)
                        ->save();
                }
            }
        }
        

        // delete reepay session
        $sessionRes = $this->_reepaySession->delete(
            $apiKey,
            $id
        );

        if ($isAjax == 1) {
            $result = [];
            $result['status'] = 'success';
            if (!empty($order->getRemoteIp())) {
                // place online
                $result['redirect_url'] = $this->_url->getUrl('checkout/onepage/success');
            } else {
                // place by admin
                $result['redirect_url'] = $this->_url->getUrl('reepay/standard/success');
            }

            return  $this->_resultJsonFactory->create()->setData($result);
        } else {
            $this->_logger->addDebug('Redirect to checkout/onepage/success');
            if (!empty($order->getRemoteIp())) {
                // place online
                $this->_redirect('checkout/onepage/success');
            } else {
                // place by admin
                $this->_redirect('reepay/standard/success');
            }
        }
    }
}
