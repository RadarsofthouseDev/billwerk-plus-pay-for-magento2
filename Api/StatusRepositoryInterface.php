<?php

namespace Radarsofthouse\Reepay\Api;

interface StatusRepositoryInterface
{

    /**
     * Save Status
     *
     * @param \Radarsofthouse\Reepay\Api\Data\StatusInterface $status
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Radarsofthouse\Reepay\Api\Data\StatusInterface $status
    );

    /**
     * Retrieve Status
     *
     * @param string $statusId
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($statusId);

    /**
     * Retrieve Status matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Radarsofthouse\Reepay\Api\Data\StatusSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Status
     *
     * @param \Radarsofthouse\Reepay\Api\Data\StatusInterface $status
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Radarsofthouse\Reepay\Api\Data\StatusInterface $status
    );

    /**
     * Delete Status by ID
     *
     * @param string $statusId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($statusId);
}
