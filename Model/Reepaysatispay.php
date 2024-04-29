<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaysatispay extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_satispay';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
