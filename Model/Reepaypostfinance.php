<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaypostfinance extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_postfinance';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
