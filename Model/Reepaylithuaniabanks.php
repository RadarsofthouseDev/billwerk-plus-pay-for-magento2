<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaylithuaniabanks extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_lithuaniabanks';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
