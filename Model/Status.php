<?php

namespace Radarsofthouse\Reepay\Model;

use Magento\Framework\Api\DataObjectHelper;
use Radarsofthouse\Reepay\Api\Data\StatusInterfaceFactory;
use Radarsofthouse\Reepay\Api\Data\StatusInterface;

/**
 * Class Status
 *
 * @package Radarsofthouse\Reepay\Model
 */
class Status extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'radarsofthouse_reepay_status';
    protected $statusDataFactory;

    protected $dataObjectHelper;


    /**
     * constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param StatusInterfaceFactory $statusDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Radarsofthouse\Reepay\Model\ResourceModel\Status $resource
     * @param \Radarsofthouse\Reepay\Model\ResourceModel\Status\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        StatusInterfaceFactory $statusDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Radarsofthouse\Reepay\Model\ResourceModel\Status $resource,
        \Radarsofthouse\Reepay\Model\ResourceModel\Status\Collection $resourceCollection,
        array $data = []
    ) {
        $this->statusDataFactory = $statusDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve status model with status data
     *
     * @return StatusInterface
     */
    public function getDataModel()
    {
        $statusData = $this->getData();
        
        $statusDataObject = $this->statusDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $statusDataObject,
            $statusData,
            StatusInterface::class
        );
        
        return $statusDataObject;
    }
}
