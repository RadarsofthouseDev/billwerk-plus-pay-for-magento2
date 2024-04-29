<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaypaysera extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_paysera';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
