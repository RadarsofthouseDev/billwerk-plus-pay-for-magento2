<?php
 
namespace Radarsofthouse\Reepay\Model;

class Reepayswish extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_swish';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
