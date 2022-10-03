<?php
 
namespace Radarsofthouse\Reepay\Model;

class Reepayverkkopankki extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_verkkopankki';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
