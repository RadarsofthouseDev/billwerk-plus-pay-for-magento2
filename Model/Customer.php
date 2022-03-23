<?php

namespace Radarsofthouse\Reepay\Model;

use Radarsofthouse\Reepay\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Radarsofthouse\Reepay\Api\Data\CustomerInterface;

class Customer extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Radarsofthouse\Reepay\Api\Data\CustomerInterfaceFactory
     */
    protected $customerDataFactory;

    /**
     * @var string
     */
    protected $_eventPrefix = 'radarsofthouse_reepay_customer';

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Radarsofthouse\Reepay\Model\ResourceModel\Customer $resource
     * @param \Radarsofthouse\Reepay\Model\ResourceModel\Customer\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CustomerInterfaceFactory $customerDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Radarsofthouse\Reepay\Model\ResourceModel\Customer $resource,
        \Radarsofthouse\Reepay\Model\ResourceModel\Customer\Collection $resourceCollection,
        array $data = []
    ) {
        $this->customerDataFactory = $customerDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve customer model with customer data
     *
     * @return CustomerInterface
     */
    public function getDataModel()
    {
        $customerData = $this->getData();

        $customerDataObject = $this->customerDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $customerDataObject,
            $customerData,
            CustomerInterface::class
        );

        return $customerDataObject;
    }
}
