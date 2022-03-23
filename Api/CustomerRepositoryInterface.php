<?php

namespace Radarsofthouse\Reepay\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CustomerRepositoryInterface
{

    /**
     * Save Customer
     *
     * @param \Radarsofthouse\Reepay\Api\Data\CustomerInterface $customer
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Radarsofthouse\Reepay\Api\Data\CustomerInterface $customer
    );

    /**
     * Retrieve Customer
     *
     * @param string $customerId
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException | \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($customerId);

    /**
     * Retrieve Customer
     *
     * @param string $customerId
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException | \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByMagentoCustomerId($customerId);

    /**
     * Retrieve Customer
     *
     * @param string $customerEmail
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByMagentoCustomerEmail($customerEmail);

    /**
     * Retrieve Customer matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Customer
     *
     * @param \Radarsofthouse\Reepay\Api\Data\CustomerInterface $customer
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Radarsofthouse\Reepay\Api\Data\CustomerInterface $customer
    );

    /**
     * Delete Customer by ID
     *
     * @param string $customerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($customerId);
}
