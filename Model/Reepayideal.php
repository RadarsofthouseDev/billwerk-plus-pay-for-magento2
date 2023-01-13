<?php
 
namespace Radarsofthouse\Reepay\Model;

class Reepayideal extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_ideal';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
