<?php

namespace Radarsofthouse\Reepay\Model\ResourceModel\Customer;

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
            \Radarsofthouse\Reepay\Model\Customer::class,
            \Radarsofthouse\Reepay\Model\ResourceModel\Customer::class
        );
    }
}
