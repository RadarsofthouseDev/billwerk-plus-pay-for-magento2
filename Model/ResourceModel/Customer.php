<?php

namespace Radarsofthouse\Reepay\Model\ResourceModel;

class Customer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('radarsofthouse_reepay_customer', 'customer_id');
    }
}
