<?php
declare(strict_types=1);

namespace Radarsofthouse\Reepay\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Radarsofthouse\Reepay\Api\Data\SessionInterface;
use Radarsofthouse\Reepay\Api\Data\SessionInterfaceFactory;
use Radarsofthouse\Reepay\Api\Data\SessionSearchResultsInterfaceFactory;
use Radarsofthouse\Reepay\Api\SessionRepositoryInterface;
use Radarsofthouse\Reepay\Model\ResourceModel\Session as ResourceSession;
use Radarsofthouse\Reepay\Model\ResourceModel\Session\CollectionFactory as SessionCollectionFactory;

class SessionRepository implements SessionRepositoryInterface
{

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Session
     */
    protected $searchResultsFactory;

    /**
     * @var SessionCollectionFactory
     */
    protected $sessionCollectionFactory;

    /**
     * @var ResourceSession
     */
    protected $resource;

    /**
     * @var SessionInterfaceFactory
     */
    protected $sessionFactory;


    /**
     * @param ResourceSession $resource
     * @param SessionInterfaceFactory $sessionFactory
     * @param SessionCollectionFactory $sessionCollectionFactory
     * @param SessionSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceSession $resource,
        SessionInterfaceFactory $sessionFactory,
        SessionCollectionFactory $sessionCollectionFactory,
        SessionSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->sessionFactory = $sessionFactory;
        $this->sessionCollectionFactory = $sessionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(SessionInterface $session)
    {
        try {
            $this->resource->save($session);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the session: %1',
                $exception->getMessage()
            ));
        }
        return $session;
    }

    /**
     * @inheritDoc
     */
    public function get($sessionId)
    {
        $session = $this->sessionFactory->create();
        $this->resource->load($session, $sessionId);
        if (!$session->getId()) {
            throw new NoSuchEntityException(__('Session with id "%1" does not exist.', $sessionId));
        }
        return $session;
    }

    /**
     * @inheritDoc
     */
    public function getListByOrderNumber($orderNumber)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $searchCriteriaBuilder = $objectManager->create(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('order_number', $orderNumber, 'eq')->create();
        return $this->getList($searchCriteria);
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->sessionCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(SessionInterface $session)
    {
        try {
            $sessionModel = $this->sessionFactory->create();
            $this->resource->load($sessionModel, $session->getSessionId());
            $this->resource->delete($sessionModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Session: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($sessionId)
    {
        return $this->delete($this->get($sessionId));
    }
}
