<?php
declare(strict_types=1);

namespace Radarsofthouse\Reepay\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface SessionRepositoryInterface
{

    /**
     * Save Session
     * @param \Radarsofthouse\Reepay\Api\Data\SessionInterface $session
     * @return \Radarsofthouse\Reepay\Api\Data\SessionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Radarsofthouse\Reepay\Api\Data\SessionInterface $session
    );

    /**
     * Retrieve Session
     * @param string $sessionId
     * @return \Radarsofthouse\Reepay\Api\Data\SessionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($sessionId);

    /**
     * Retrieve Session by orderNumber
     * @param string $orderNumber
     * @return \Radarsofthouse\Reepay\Api\Data\SessionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListByOrderNumber($orderNumber);

    /**
     * Retrieve Session matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Radarsofthouse\Reepay\Api\Data\SessionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Session
     * @param \Radarsofthouse\Reepay\Api\Data\SessionInterface $session
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Radarsofthouse\Reepay\Api\Data\SessionInterface $session
    );

    /**
     * Delete Session by ID
     * @param string $sessionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($sessionId);
}
