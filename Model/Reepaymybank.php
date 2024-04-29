<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaymybank extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_mybank';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
