<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaypayconiq extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_payconiq';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
