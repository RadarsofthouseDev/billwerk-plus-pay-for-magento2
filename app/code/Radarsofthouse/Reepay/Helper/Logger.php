<?php

namespace Radarsofthouse\Reepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Logger
 *
 * @package Radarsofthouse\Reepay\Helper
 */
class Logger extends AbstractHelper
{
    const CONFIG_PATH = 'log_level';
    private $loggerLevel = \Monolog\Logger::EMERGENCY;
    private $debugLogger;
    private $infoLogger;
    private $errorLogger;
    protected $scopeConfig;

    /**
     * constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param Data $dataHelper
     * @param Radarsofthouse\Reepay\Logger\Debug $debug
     * @param Radarsofthouse\Reepay\Logger\Info $info
     * @param Radarsofthouse\Reepay\Logger\Error $error
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        \Radarsofthouse\Reepay\Logger\Debug $debug,
        \Radarsofthouse\Reepay\Logger\Info $info,
        \Radarsofthouse\Reepay\Logger\Error $error,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->debugLogger = $debug;
        $this->infoLogger = $info;
        $this->errorLogger = $error;
        $this->loggerLevel = \Monolog\Logger::DEBUG;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * log debug
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function addDebug($message, $context = [], $isApi = false)
    {
        if ($this->loggerLevel <= \Monolog\Logger::DEBUG) {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
            $logConfig = $this->scopeConfig->getValue('payment/reepay_payment/log', $storeScope);

            if ($logConfig == 1 && $isApi) {
                $this->debugLogger->addDebug($message, $context);
            } elseif ($logConfig == 2) {
                $this->debugLogger->addDebug($message, $context);
            }
        }
    }

    /**
     * log info
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function addInfo($message, $context = [], $isApi = false)
    {
        if ($this->loggerLevel <= \Monolog\Logger::INFO) {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
            $logConfig = $this->scopeConfig->getValue('payment/reepay_payment/log', $storeScope);

            if ($logConfig == 1 && $isApi) {
                $this->infoLogger->addInfo($message, $context);
            } elseif ($logConfig == 2) {
                $this->infoLogger->addInfo($message, $context);
            }
        }
    }

    /**
     * log error
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function addError($message, $context = [], $isApi = false)
    {
        if ($this->loggerLevel <= \Monolog\Logger::ERROR) {
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
            $logConfig = $this->scopeConfig->getValue('payment/reepay_payment/log', $storeScope);

            if ($logConfig == 1 && $isApi) {
                $this->errorLogger->addError($message, $context);
            } elseif ($logConfig == 2) {
                $this->errorLogger->addError($message, $context);
            }
        }
    }
}
