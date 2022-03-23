<?php

namespace Radarsofthouse\Reepay\Plugin\Block\Widget\Button;

use Magento\Sales\Block\Adminhtml\Order\Create;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\Toolbar as ToolbarContext;
 
class ToolbarPlugin
{

    /**
     * @var \Magento\Sales\Api\TransactionRepositoryInterface $repository
     */
    private $repository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Backend\Model\UrlInterface $backendUrl
     */
    private $backendUrl;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     */
    private $reepayHelper;

    /**
     * Constructor
     *
     * @param \Magento\Sales\Api\TransactionRepositoryInterface $repository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     */
    public function __construct(
        \Magento\Sales\Api\TransactionRepositoryInterface $repository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper
    ) {
        $this->repository = $repository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->backendUrl = $backendUrl;
        $this->reepayHelper = $reepayHelper;
    }

    /**
     * Before Push Buttons
     *
     * @param ToolbarContext $toolbar
     * @param AbstractBlock $context
     * @param ButtonList $buttonList
     * @return array
     */
    public function beforePushButtons(
        ToolbarContext $toolbar,
        AbstractBlock $context,
        ButtonList $buttonList
    ) {
        $order = false;
        $nameInLayout = $context->getNameInLayout();
        if ('sales_order_edit' == $nameInLayout) {
            $order = $context->getOrder();
        }
 
        if ($order) {
            $paymentMethod = $order->getPayment()->getMethodInstance()->getCode();
            $isReepayPaymentMethod = $this->reepayHelper->isReepayPaymentMethod($paymentMethod);
            if ($isReepayPaymentMethod) {
                $orderTransactions = $this->getTransactionByOrderId($order->getId());

                if (count($orderTransactions) <= 0) {
                    $message = __('Are you sure you want to send payment link email to customer?');
                    $url = $this->backendUrl->getUrl(
                        "radarsofthouse_reepay/paymentlink/send",
                        ['order_id' => $order->getId() ]
                    );

                    $buttonList->add(
                        'order_send_payment_link',
                        [
                            'label' => __('Send payment link'),
                            'onclick' => "confirmSetLocation('{$message}', '{$url}')",
                            'class' => 'reepay-send-payment-link',
                            'id' => 'reepay-send-payment-link',
                            'title' => __('Send payment link email to customer')
                        ]
                    );
                }
            }
        }
 
        return [$context, $buttonList];
    }

    /**
     * Get order transaction by order ID
     *
     * @param int $id
     * @return \Magento\Sales\Api\Data\TransactionInterface[]
     */
    private function getTransactionByOrderId($id)
    {
        $this->searchCriteriaBuilder->addFilter('order_id', $id);
        $list = $this->repository->getList(
            $this->searchCriteriaBuilder->create()
        );

        return $list->getItems();
    }
}
