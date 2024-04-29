<?php

namespace Radarsofthouse\Reepay\Model;

class Reepayestoniabanks extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_estoniabanks';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
