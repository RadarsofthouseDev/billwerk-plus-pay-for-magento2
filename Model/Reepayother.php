<?php

namespace Radarsofthouse\Reepay\Model;

class Reepayother extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_other';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
