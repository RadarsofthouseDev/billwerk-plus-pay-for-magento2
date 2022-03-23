<?php

namespace Radarsofthouse\Reepay\Model\ResourceModel\Status;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Radarsofthouse\Reepay\Model\Status::class,
            \Radarsofthouse\Reepay\Model\ResourceModel\Status::class
        );
    }
}
