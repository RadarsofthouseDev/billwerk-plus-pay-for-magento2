<?php

namespace Radarsofthouse\Reepay\Model\ResourceModel;

class Status extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('radarsofthouse_reepay_status', 'status_id');
    }
}
