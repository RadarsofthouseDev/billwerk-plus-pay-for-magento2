<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaypaysafecard extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_paysafecard';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
