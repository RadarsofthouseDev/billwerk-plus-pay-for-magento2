<?php
 
namespace Radarsofthouse\Reepay\Model;

class Reepaysepa extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_sepa';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
