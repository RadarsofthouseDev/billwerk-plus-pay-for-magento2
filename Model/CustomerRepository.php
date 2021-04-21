<?php
/**
 * Copyright (c) 2021 radarsofthouse.dk
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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

    protected $customerFactory;

    protected $resource;

    protected $extensibleDataObjectConverter;
    protected $searchResultsFactory;

    private $storeManager;

    protected $dataObjectHelper;

    protected $customerCollectionFactory;

    protected $dataCustomerFactory;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;


    /**
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function deleteById($customerId)
    {
        return $this->delete($this->getById($customerId));
    }
}
