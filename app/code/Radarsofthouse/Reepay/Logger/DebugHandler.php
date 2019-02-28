<?php

namespace Radarsofthouse\Reepay\Logger;

use Magento\Framework\Filesystem\DriverInterface;
use Monolog\Logger;

/**
 * Class DebugHandler
 *
 * @package Radarsofthouse\Reepay\Logger
 */
class DebugHandler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/reepay.log';
}
