<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaymultibanco extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_multibanco';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
