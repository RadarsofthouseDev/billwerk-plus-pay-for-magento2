<?php

namespace Radarsofthouse\Reepay\Model\Config\Source;

class AutoCancelStatus implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $orderConfig;
    /**
     * @var Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory
     */
    protected $statusCollectionFactory;

    /**
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
     */
    public function __construct(
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
    ) {
        $this->orderConfig = $orderConfig;
        $this->statusCollectionFactory = $statusCollectionFactory;
    }

    /**
     * Return order status
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $excludedStates = ['canceled', 'closed', 'complete', 'holded', 'payment_review'];

        $statusCollection = $this->statusCollectionFactory->create();
        $statusLabels = [];
        foreach ($statusCollection as $status) {
            $statusLabels[$status->getStatus()] = $status->getLabel();
        }

        // Get all states and exclude specific ones
        $orderStates = $this->orderConfig->getStates();
        foreach ($orderStates as $state => $stateLabel) {
            if (in_array($state, $excludedStates)) {
                continue; // Skip excluded states
            }
            $statuses = $this->orderConfig->getStateStatuses($state);
            foreach ($statuses as $key  => $status) {
                $label = isset($statusLabels[$key]) ? $statusLabels[$key] : ucfirst($status);
                $options[] = [
                    'value' => $key,
                    'label' => $label
                ];
            }
        }

        return $options;
    }
}
