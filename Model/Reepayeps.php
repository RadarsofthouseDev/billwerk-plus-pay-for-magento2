<?php

namespace Radarsofthouse\Reepay\Model;

class Reepayeps extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_eps';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
