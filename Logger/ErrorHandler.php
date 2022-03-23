<?php

namespace Radarsofthouse\Reepay\Logger;

use Magento\Framework\Filesystem\DriverInterface;
use Monolog\Logger;

class ErrorHandler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var int
     */
    protected $loggerType = Logger::ERROR;

    /**
     * @var string
     */
    protected $fileName = '/var/log/reepay.log';
}
