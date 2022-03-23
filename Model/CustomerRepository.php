<?php

namespace Radarsofthouse\Reepay\Model;

use Radarsofthouse\Reepay\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Radarsofthouse\Reepay\Api\Data\CustomerSearchResultsInterfaceFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Radarsofthouse\Reepay\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Radarsofthouse\Reepay\Model\ResourceModel\Customer as ResourceCustomer;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Radarsofthouse\Reepay\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\DataObjectHelper;

class CustomerRepository implements CustomerRepositoryInterface
{

    /**
     * @var \Magento\Customer\Model\Data\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Radarsofthouse\Reepay\Model\ResourceModel\Customer
     */
    protected $resource;
    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var \Radarsofthouse\Reepay\Api\Data\CustomerSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Radarsofthouse\Reepay\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Radarsofthouse\Reepay\Api\Data\CustomerInterfaceFactory
     */
    protected $dataCustomerFactory;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * Constructor
     *
     * @param ResourceCustomer $resource
     * @param CustomerFactory $customerFactory
     * @param CustomerInterfaceFactory $dataCustomerFactory
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param CustomerSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCustomer $resource,
        CustomerFactory $customerFactory,
        CustomerInterfaceFactory $dataCustomerFactory,
        CustomerCollectionFactory $customerCollectionFactory,
        CustomerSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->customerFactory = $customerFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCustomerFactory = $dataCustomerFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * Save Customer
     *
     * @param \Radarsofthouse\Reepay\Api\Data\CustomerInterface $customer
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Radarsofthouse\Reepay\Api\Data\CustomerInterface $customer
    ) {
        /* if (empty($customer->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $customer->setStoreId($storeId);
        } */

        $customerData = $this->extensibleDataObjectConverter->toNestedArray(
            $customer,
            [],
            \Radarsofthouse\Reepay\Api\Data\CustomerInterface::class
        );

        $customerModel = $this->customerFactory->create()->setData($customerData);

        try {
            $this->resource->save($customerModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the customer: %1',
                $exception->getMessage()
            ));
        }
        return $customerModel->getDataModel();
    }

    /**
     * Retrieve Customer
     *
     * @param string $customerId
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException | \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($customerId)
    {
        $customer = $this->customerFactory->create();
        $this->resource->load($customer, $customerId);
        if (!$customer->getId()) {
            throw new NoSuchEntityException(__('Customer with id "%1" does not exist.', $customerId));
        }
        return $customer->getDataModel();
    }

    /**
     * Retrieve Customer
     *
     * @param string $customerId
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException | \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByMagentoCustomerId($customerId)
    {
        $customer = $this->customerFactory->create();
        $this->resource->load($customer, $customerId, 'magento_customer_id');
        if (!$customer->getId()) {
            throw new NoSuchEntityException(__('Customer with id "%1" does not exist.', $customerId));
        }
        return $customer->getDataModel();
    }

    /**
     * Retrieve Customer
     *
     * @param string $customerEmail
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByMagentoCustomerEmail($customerEmail)
    {
        $customer = $this->customerFactory->create();
        $this->resource->load($customer, $customerEmail, 'magento_email');
        if (!$customer->getId()) {
            throw new NoSuchEntityException(__('Customer with email "%1" does not exist.', $customerEmail));
        }
        return $customer->getDataModel();
    }

    /**
     * Retrieve Customer matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->customerCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Radarsofthouse\Reepay\Api\Data\CustomerInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete Customer
     *
     * @param \Radarsofthouse\Reepay\Api\Data\CustomerInterface $customer
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Radarsofthouse\Reepay\Api\Data\CustomerInterface $customer
    ) {
        try {
            $customerModel = $this->customerFactory->create();
            $this->resource->load($customerModel, $customer->getCustomerId());
            $this->resource->delete($customerModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Customer: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * Delete Customer by ID
     *
     * @param string $customerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($customerId)
    {
        return $this->delete($this->getById($customerId));
    }
}
