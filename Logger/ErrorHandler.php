<?php

namespace Radarsofthouse\Reepay\Logger;

use Magento\Framework\Filesystem\DriverInterface;
use Monolog\Logger;

/**
 * Class ErrorHandler
 *
 * @package Radarsofthouse\Reepay\Logger
 */
class ErrorHandler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::ERROR;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/reepay.log';
}
