<?php

namespace Radarsofthouse\Reepay\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Radarsofthouse\Reepay\Api\StatusRepositoryInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Radarsofthouse\Reepay\Api\Data\StatusSearchResultsInterfaceFactory;
use Radarsofthouse\Reepay\Api\Data\StatusInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Radarsofthouse\Reepay\Model\ResourceModel\Status as ResourceStatus;
use Radarsofthouse\Reepay\Model\ResourceModel\Status\CollectionFactory as StatusCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotSaveException;

class StatusRepository implements StatusRepositoryInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var \Radarsofthouse\Reepay\Model\ResourceModel\Status
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Radarsofthouse\Reepay\Model\Model\Status
     */
    protected $statusFactory;

    /**
     * @var \Radarsofthouse\Reepay\Api\Data\StatusInterfaceFactory
     */
    protected $dataStatusFactory;

    /**
     * @var \Radarsofthouse\Reepay\Api\Data\StatusSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Radarsofthouse\Reepay\Model\ResourceModel\Status\CollectionFactory
     */
    protected $statusCollectionFactory;

    /**
     * Constructor
     *
     * @param ResourceStatus $resource
     * @param StatusFactory $statusFactory
     * @param StatusInterfaceFactory $dataStatusFactory
     * @param StatusCollectionFactory $statusCollectionFactory
     * @param StatusSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceStatus $resource,
        StatusFactory $statusFactory,
        StatusInterfaceFactory $dataStatusFactory,
        StatusCollectionFactory $statusCollectionFactory,
        StatusSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->statusFactory = $statusFactory;
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataStatusFactory = $dataStatusFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * Save Status
     *
     * @param \Radarsofthouse\Reepay\Api\Data\StatusInterface $status
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Radarsofthouse\Reepay\Api\Data\StatusInterface $status
    ) {
        /* if (empty($status->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $status->setStoreId($storeId);
        } */
        
        $statusData = $this->extensibleDataObjectConverter->toNestedArray(
            $status,
            [],
            \Radarsofthouse\Reepay\Api\Data\StatusInterface::class
        );
        
        $statusModel = $this->statusFactory->create()->setData($statusData);
        
        try {
            $this->resource->save($statusModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the status: %1',
                $exception->getMessage()
            ));
        }

        return $statusModel->getDataModel();
    }

    /**
     * Retrieve Status
     *
     * @param string $statusId
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($statusId)
    {
        $status = $this->statusFactory->create();
        $this->resource->load($status, $statusId);
        if (!$status->getId()) {
            throw new NoSuchEntityException(__('Status with id "%1" does not exist.', $statusId));
        }

        return $status->getDataModel();
    }

    /**
     * Retrieve Status matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Radarsofthouse\Reepay\Api\Data\StatusSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->statusCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Radarsofthouse\Reepay\Api\Data\StatusInterface::class
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
     * Delete Status
     *
     * @param \Radarsofthouse\Reepay\Api\Data\StatusInterface $status
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Radarsofthouse\Reepay\Api\Data\StatusInterface $status
    ) {
        try {
            $statusModel = $this->statusFactory->create();
            $this->resource->load($statusModel, $status->getStatusId());
            $this->resource->delete($statusModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Status: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * Delete Status by ID
     *
     * @param string $statusId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($statusId)
    {
        return $this->delete($this->getById($statusId));
    }
}
