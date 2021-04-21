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

namespace Radarsofthouse\Reepay\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CustomerRepositoryInterface
{

    /**
     * Save Customer
     * @param \Radarsofthouse\Reepay\Api\Data\CustomerInterface $customer
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Radarsofthouse\Reepay\Api\Data\CustomerInterface $customer
    );

    /**
     * Retrieve Customer
     * @param string $customerId
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException | \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($customerId);

    /**
     * Retrieve Customer
     * @param string $customerId
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException | \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByMagentoCustomerId($customerId);

    /**
     * Retrieve Customer
     * @param string $customerEmail
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByMagentoCustomerEmail($customerEmail);

    /**
     * Retrieve Customer matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Customer
     * @param \Radarsofthouse\Reepay\Api\Data\CustomerInterface $customer
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Radarsofthouse\Reepay\Api\Data\CustomerInterface $customer
    );

    /**
     * Delete Customer by ID
     * @param string $customerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($customerId);
}
