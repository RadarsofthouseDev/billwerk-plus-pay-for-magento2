<?php
declare(strict_types=1);

namespace Radarsofthouse\Reepay\Api\Data;

interface SessionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Session list.
     * @return \Radarsofthouse\Reepay\Api\Data\SessionInterface[]
     */
    public function getItems();

    /**
     * Set handle list.
     * @param \Radarsofthouse\Reepay\Api\Data\SessionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
