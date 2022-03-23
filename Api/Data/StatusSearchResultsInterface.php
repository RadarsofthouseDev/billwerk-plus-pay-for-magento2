<?php

namespace Radarsofthouse\Reepay\Api\Data;

interface StatusSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Status list.
     *
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     *
     * @param \Radarsofthouse\Reepay\Api\Data\StatusInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
