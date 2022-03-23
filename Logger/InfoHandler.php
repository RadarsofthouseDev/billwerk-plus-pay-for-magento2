<?php

namespace Radarsofthouse\Reepay\Logger;

use Magento\Framework\Filesystem\DriverInterface;
use Monolog\Logger;

class InfoHandler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * @var string
     */
    protected $fileName = '/var/log/reepay.log';
}
