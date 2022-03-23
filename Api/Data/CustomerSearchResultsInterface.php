<?php

namespace Radarsofthouse\Reepay\Api\Data;

interface CustomerSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Customer list.
     *
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface[]
     */
    public function getItems();

    /**
     * Set magento_customer_id list.
     *
     * @param \Radarsofthouse\Reepay\Api\Data\CustomerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
