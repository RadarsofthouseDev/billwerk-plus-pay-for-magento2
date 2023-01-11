<?php
 
namespace Radarsofthouse\Reepay\Model;

class Reepaybancontact extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_bancontact';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
