<?php

namespace Radarsofthouse\Reepay\Logger;

use Magento\Framework\Filesystem\DriverInterface;
use Monolog\Logger;

/**
 * Class InfoHandler
 *
 * @package Radarsofthouse\Reepay\Logger
 */
class InfoHandler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/reepay.log';
}
