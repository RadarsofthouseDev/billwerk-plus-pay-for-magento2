<?php

namespace Radarsofthouse\Reepay\Controller\Webhooks;

/**
 * Class Index
 *
 * @package Radarsofthouse\Reepay\Controller\Webhooks
 */


class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $jsonHelper;
    protected $logger;
    protected $invoiceHelper;
    protected $dataHelper;
    protected $order;
    protected $orderService;
    protected $orderInterface;
    protected $transaction;
    protected $invoiceService;
    protected $reepayHelper;
    protected $invoice;
    protected $creditmemoFactory;
    protected $creditmemoService;
    protected $resultJsonFactory;

    /**
     * Index constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     * @param \Radarsofthouse\Reepay\Helper\Invoice $invoiceHelper
     * @param \Radarsofthouse\Reepay\Helper\Data $dataHelper
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Model\Service\OrderService $orderService
     * @param \Magento\Sales\Api\Data\OrderInterface $orderInterface
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     * @param \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param \Magento\Sales\Model\Service\CreditmemoService $creditmemoService
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Radarsofthouse\Reepay\Helper\Logger $logger,
        \Radarsofthouse\Reepay\Helper\Invoice $invoiceHelper,
        \Radarsofthouse\Reepay\Helper\Data $dataHelper,
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        \Magento\Sales\Model\Order\Invoice $invoice,
        \Magento\Sales\Model\Service\CreditmemoService $creditmemoService,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
        $this->invoiceHelper = $invoiceHelper;
        $this->dataHelper = $dataHelper;
        $this->order = $order;
        $this->orderService = $orderService;
        $this->orderInterface = $orderInterface;
        $this->transaction = $transaction;
        $this->invoiceService = $invoiceService;
        $this->reepayHelper = $reepayHelper;
        $this->invoice = $invoice;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->creditmemoService = $creditmemoService;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);

        // CsrfAwareAction Magento2.3 compatibility
        if (interface_exists("\Magento\Framework\App\CsrfAwareActionInterface")) {
            $request = $this->getRequest();


            if ($request->isPost() && empty($request->getParam('form_key'))) {
                $formKey = $this->_objectManager->get(\Magento\Framework\Data\Form\FormKey::class);
                $request->setParam('form_key', $formKey->getFormKey());
            }
        }
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $request = $this->getRequest()->getContent();
        $receiveData = json_decode($request, true);
       
        $this->logger->addDebug(__METHOD__, $receiveData);

        try {
            if (!array_key_exists('event_type', $receiveData)) {
                throw new \Exception('This request event_type not found.', 404);
            }
            switch ($receiveData['event_type']) {
                case 'invoice_refund':
                    if (array_key_exists('subscription', $receiveData)) {
                        $response['status'] = 400;
                        $response['message'] = 'This request is not charge invoice.';
                    } else {
                        $refundResponse = $this->refund($receiveData['invoice']);
                        if (is_array($refundResponse)) {
                            if ($refundResponse['status'] == 400) {
                                $response = $refundResponse;
                            }
                        } else {
                            $response = [
                                'status' => 200,
                                'invoice' => $receiveData['invoice'],
                                'message' => 'This request is invoice_refund event.',
                            ];
                        }
                    }
                    $log['response'] = $response;

                    $this->logger->addDebug('Refund response', $log);

                    break;
                case 'invoice_settled':
                    if (array_key_exists('subscription', $receiveData)) {
                        $response['status'] = 400;
                        $response['message'] = 'This request is not charge invoice.';
                    } else {
                        $settledResponse = $this->settled($receiveData['invoice']);
                        if (is_array($settledResponse)) {
                            if ($settledResponse['status'] == 400) {
                                $response = $settledResponse;
                            }
                        } else {
                            $response = [
                                'status' => 200,
                                'invoice' => $receiveData['invoice'],
                                'message' => 'This request is invoice_settled event.',
                            ];
                        }
                    }
                    $log['response'] = $response;

                    $this->logger->addDebug('Settled response', $log);

                    break;
                case 'invoice_cancelled':
                    if (array_key_exists('subscription', $receiveData)) {
                        $response['message'] = 'This request is not charge invoice.';
                    } else {
                        $this->cancel($receiveData['invoice']);
                        $response = [
                            'invoice' => $receiveData['invoice'],
                            'message' => 'This request is invoice_cancelled event.',
                        ];
                    }
                    $log['response'] = $response;

                    $this->logger->addDebug('Cancel response', $log);

                    break;
                default:
                    $response['status'] = 400;
                    $response['message'] = 'invalid request data';
                    $log['response'] = $response;

                    $this->logger->addDebug('default', $log);

                    break;
            }

            $response['message'] = 'Magento : '.$response['message'];

            return  $this->resultJsonFactory->create()->setData($response);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $log['response_error'] = [
                'error_code' => $e->getCode(),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ];
            $this->logger->addError(__METHOD__, $log);

            throw new \Exception($e->getMessage(), (!empty($e->getCode()) ? (int)$e->getCode() : 500));
        } catch (\Exception $e) {
            $log['response_error'] = [
                'error_code' => $e->getCode(),
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ];
            $this->logger->addError(__METHOD__, $log);

            throw new \Exception($e->getMessage(), (!empty($e->getCode()) ? (int)$e->getCode() : 500));
        }
    }
    
    /**
     * Capture from Reepay
     *
     * @param string $order_id (order increment ID)
     * @return void
     */
    protected function settled($order_id)
    {
        $this->logger->addDebug(__METHOD__, [$order_id]);

        $order = $this->orderInterface->loadByIncrementId($order_id);

        try {
            if (!$order->getId()) {
                return ['status' => 400, 'message' => 'The order no longer exists.'];
            }

            if (!$order->canInvoice()) {
                //// 'Cannot create an invoice.'
                return ['status' => 400, 'message' => 'Cannot create an invoice.'];
            }

            $invoice = $this->invoiceService->prepareInvoice($order);
            if (!$invoice) {
                return ['status' => 400, 'message' => 'We can\'t save the invoice right now.'];
            }

            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->register();
            $invoice->getOrder()->setCustomerNoteNotify(false);

            $order->addStatusHistoryComment('Reepay : Transaction has been captured.', false);

            $transactionSave = $this->transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();

            $_payment = $order->getPayment();
            $this->reepayHelper->setReepayPaymentState($_payment, 'settled');
            $order->save();
        } catch (Mage_Core_Exception $e) {
            return ['status' => 400, 'message' => 'settled webhook exception : '.$e->getMessage()];
        }
    }

    /**
     * Refund from Reepay
     *
     * @param string $order_id (order increment ID)
     * @return void | array error message
     */
    protected function refund($order_id)
    {
        $this->logger->addDebug(__METHOD__, [$order_id]);

        $order = $this->orderInterface->loadByIncrementId($order_id);
        
        try {
            if (!$order->getId()) {
                return ['status' => 400, 'message' => 'The order no longer exists.'];
            }

            $invoices = $order->getInvoiceCollection();
            foreach ($invoices as $invoice) {
                $invoiceTransactionId = $order->getTransactionId();
                $invoiceincrementid = $invoice->getIncrementId();
                $invoiceobj = $this->invoice->loadByIncrementId($invoiceincrementid);
                $creditmemo = $this->creditmemoFactory->createByOrder($order);
                // Don't set invoice if you want to do offline refund
                $creditmemo->setInvoice($invoiceobj);
                $this->creditmemoService->refund($creditmemo);
            }

            $_payment = $order->getPayment();
            $this->reepayHelper->setReepayPaymentState($_payment, 'refunded');
            $order->save();
        } catch (Mage_Core_Exception $e) {
            return ['status' => 400, 'message' => 'refund webhook exception : ' . $e->getMessage()];
        }
    }

    /**
     * Cancel from Reepay
     *
     * @param string $order_id (order increment ID)
     * @return void
     */
    protected function cancel($order_id)
    {
        $this->logger->addDebug(__METHOD__, [$order_id]);

        $order = $this->orderInterface->loadByIncrementId($order_id);

        try {
            if (!$order->getId()) {
                return ['status' => 400, 'message' => 'The order no longer exists.'];
            }

            if (!$order->canCancel()) {
                return ['status' => 400, 'message' => 'Cannot cancel this order'];
            }

            $order->cancel();
            $order->addStatusHistoryComment('Reepay : order have been cancelled by Reepay webhook');
            $order->save();

            $_payment = $order->getPayment();
            $this->reepayHelper->setReepayPaymentState($_payment, 'cancelled');
            $order->save();
        } catch (Mage_Core_Exception $e) {
            return ['status' => 400, 'message' => 'cancel webhook exception : ' . $e->getMessage()];
        }
    }
}
