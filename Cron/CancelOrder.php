<?php

declare(strict_types=1);

namespace Radarsofthouse\Reepay\Cron;

class CancelOrder
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var \Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory
     */
    protected $orderStatusHistoryFactory;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;
    /**
     * @var \Radarsofthouse\Reepay\Helper\Data
     */
    protected $helper;
    /**
     * @var \Radarsofthouse\Reepay\Helper\Logger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory $orderStatusHistoryFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Radarsofthouse\Reepay\Helper\Data $helper
     * @param \Radarsofthouse\Reepay\Helper\Logger
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory $orderStatusHistoryFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Radarsofthouse\Reepay\Helper\Data $helper,
        \Radarsofthouse\Reepay\Helper\Logger $logger
    ) {
        $this->storeManager = $storeManager;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderStatusHistoryFactory = $orderStatusHistoryFactory;
        $this->orderRepository = $orderRepository;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute(): void
    {
        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $storeId = $store->getId();
            $storeName = $store->getName();
            $isEnabled = $this->helper->getConfig('auto_cancel_unpaid_order', $storeId);
            if (!$isEnabled) {
                $this->logger->addInfo("Cronjob CancelOrder disabled for store $storeId-$storeName");
                continue;
            }
            $statuses = $this->helper->getConfig('auto_cancel_unpaid_order_status', $storeId);
            $minutes = $this->helper->getConfig('auto_cancel_unpaid_order_after', $storeId);
            $orderIds = $this->getOrders($storeId, $statuses, $minutes);
            if (!$orderIds) {
                $this->logger->addInfo("No orders found for store $storeId-$storeName");
                continue;
            }
            foreach ($orderIds as $orderId) {
                try {
                    $order = $this->orderRepository->get($orderId);
                    if ($order->canCancel()) {
                        $order->cancel();

                        $statusHistory = $this->orderStatusHistoryFactory->create();
                        $statusHistory->setComment("Frisbii Pay: auto cancel unpaid order after $minutes minutes");
                        $statusHistory->setEntityName(\Magento\Sales\Model\Order::STATUS_HISTORIES);
                        $statusHistory->setIsVisibleOnFront(false);
                        $statusHistory->setStatus($order->getStatus());

                        $order->addStatusHistory($statusHistory);
                        $this->orderRepository->save($order);
                        $this->logger->addInfo("Cancel order $orderId.");
                        continue;
                    }
                    $this->logger->addInfo("Cannot cancel order $orderId.");
                } catch (\Throwable $e) {
                    $this->logger->addError("Cannot cancel order $orderId: " . $e->getMessage());
                }
            }
        }
    }

    /**
     *  Get oders be canceled.
     *
     * @param $status
     * @param $minutes
     * @return array|null
     */
    private function getOrders($storeId, $status, $minutes): ?array
    {
        $orderIds = null;
        $currentTime = date('Y-m-d H:i:s', strtotime("-$minutes mins"));
        $collection = $this->orderCollectionFactory->create()
            ->addAttributeToFilter('store_id', $storeId)
            ->addAttributeToFilter('status', ['in' => $status])
            ->addAttributeToFilter('created_at', ['lteq' => $currentTime]);
        if ($collection->count() > 0) {
            /** @var \Magento\Sales\Model\Order $order */
            foreach ($collection->getItems() as $order) {
                $method = $order->getPayment()->getMethod();
                if ($this->helper->isReepayPaymentMethod($method)) {
                    $orderIds[] = $order->getId();
                }
            }
        }
        return $orderIds;
    }
}
