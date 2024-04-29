<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaybanktransfer extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_banktransfer';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
