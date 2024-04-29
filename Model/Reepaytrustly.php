<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaytrustly extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_trustly';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
