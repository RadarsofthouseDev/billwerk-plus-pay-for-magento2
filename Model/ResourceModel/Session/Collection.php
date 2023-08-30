<?php
declare(strict_types=1);

namespace Radarsofthouse\Reepay\Model\ResourceModel\Session;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'session_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Radarsofthouse\Reepay\Model\Session::class,
            \Radarsofthouse\Reepay\Model\ResourceModel\Session::class
        );
    }
}
