<?php

namespace Radarsofthouse\Reepay\Model;

class Reepaylatviabanks extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_latviabanks';

    /**
     * @var boolean
     */
    protected $_isAutoCapture = true;
}
