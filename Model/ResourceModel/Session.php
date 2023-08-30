<?php
declare(strict_types=1);

namespace Radarsofthouse\Reepay\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Session extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('radarsofthouse_reepay_session', 'session_id');
    }
}
