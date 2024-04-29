<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaycash extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_cash';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
