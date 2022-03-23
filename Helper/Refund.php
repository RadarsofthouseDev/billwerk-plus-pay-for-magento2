<?php

namespace Radarsofthouse\Reepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Radarsofthouse\Reepay\Client\Api;

class Refund extends AbstractHelper
{
    const ENDPOINT = 'refund';

    /**
     * @var \Radarsofthouse\Reepay\Client\Api
     */
    private $client = null;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Logger
     */
    private $logger = null;

    /**
     * constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param Logger $logger
     */
    public function __construct(Context $context, Logger $logger)
    {
        parent::__construct($context);
        $this->client = new Api();
        $this->logger = $logger;
    }

    /**
     * Get refund by ID
     *
     * @param string $apiKey
     * @param string $handle
     * @return array|bool
     * @throws \Exception
     */
    public function get($apiKey, $handle)
    {
        $log = ['param' => ['handle' => $handle]];
        $response = $this->client->get($apiKey, self::ENDPOINT . "/{$handle}");
        if ($this->client->success()) {
            $log ['response'] = $response;
            $this->logger->addInfo(__METHOD__, $log, true);

            return $response;
        } else {
            $log['http_errors'] = $this->client->getHttpError();
            $log['response_errors'] = $this->client->getErrors();
            $this->logger->addError(__METHOD__, $log, true);

            return false;
        }
    }

    /**
     * Create refund
     *
     * @param string $apiKey
     * @param array $refund
     * @return bool|array
     * @throws \Exception
     */
    public function create($apiKey, $refund)
    {
        $log = ['param' => ['refund' => $refund]];
        $response = $this->client->post($apiKey, self::ENDPOINT, $refund);
        if ($this->client->success()) {
            $log ['response'] = $response;
            $this->logger->addInfo(__METHOD__, $log, true);

            return $response;
        } else {
            $log['http_errors'] = $this->client->getHttpError();
            $log['response_errors'] = $this->client->getErrors();
            $this->logger->addError(__METHOD__, $log, true);

            return $response;
        }
    }
}
