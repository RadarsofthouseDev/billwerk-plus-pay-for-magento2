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

use Radarsofthouse\Reepay\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Radarsofthouse\Reepay\Api\Data\CustomerInterface;

class Customer extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $customerDataFactory;

    protected $_eventPrefix = 'radarsofthouse_reepay_customer';

    /**
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
