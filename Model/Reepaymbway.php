<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaymbway extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_mbway';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
