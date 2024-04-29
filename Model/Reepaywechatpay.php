<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaywechatpay extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_wechatpay';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
