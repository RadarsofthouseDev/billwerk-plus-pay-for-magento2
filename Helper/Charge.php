<?php

namespace Radarsofthouse\Reepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Radarsofthouse\Reepay\Client\Api;

/**
 * Class Charge
 *
 * @package Radarsofthouse\Reepay\Helper
 */
class Charge extends AbstractHelper
{
    const ENDPOINT = 'charge';
    private $client = null;
    private $logger = null;

    /**
     * constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param $logger
     */
    public function __construct(Context $context, Logger $logger)
    {
        parent::__construct($context);
        $this->client = new Api();
        $this->logger = $logger;
    }

    /**
     * Get charge by invoice id or handle
     *
     * @param string $apiKey
     * @param string $handle
     * @return bool|array
     * @throws \Exception
     */
    public function get($apiKey, $handle)
    {
        $log = ['param' => ['handle' => $handle]];
        $response = $this->client->get($apiKey, self::ENDPOINT . "/{$handle}");
        if ($this->client->success()) {
            $log['response'] = $response;
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
     * Create charge
     *
     * @param string $apiKey
     * @param array $charge
     * @return bool|array
     * @throws \Exception
     */
    public function create($apiKey, $charge)
    {
        $log = ['param' => ['charge' => $charge]];
        $response = $this->client->post($apiKey, self::ENDPOINT, $charge);
        if ($this->client->success()) {
            $log['response'] = $response;
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
     * Create charge with create new customer.
     *
     * @param string $apiKey
     * @param string $handle
     * @param string $source
     * @param array $customer
     * @param array $option
     * @return bool|array
     * @throws \Exception
     */
    public function createWithNewCustomer($apiKey, $handle, $source, $customer, $option = [])
    {
        $option['handle'] = $handle;
        $option['source'] = $source;
        $option['customer'] = $customer;
        $log = ['param' => ['option' => $option]];
        $this->logger->addInfo(__METHOD__, $log, true);

        return $this->create($apiKey, $option);
    }

    /**
     * Create charge with exist customer.
     *
     * @param string $apiKey
     * @param string $handle
     * @param string $source
     * @param string $customerHandle
     * @param array $option
     * @return bool|array
     * @throws \Exception
     */
    public function createWithExistCustomer($apiKey, $handle, $source, $customerHandle, $option = [])
    {
        $option['handle'] = $handle;
        $option['source'] = $source;
        $option['customer_handle'] = $customerHandle;
        $log = ['param' => ['option' => $option]];
        $this->logger->addInfo(__METHOD__, $log, true);

        return $this->create($apiKey, $option);
    }

    /**
     * Settle charge
     * Settle an authorized charge.
     * This is the second step in a two-step payment process with an authorization and subsequent settle.
     * Optionally the amount and order lines of the charge can be adjusted.
     *
     * @param string $apiKey
     * @param string $handle
     * @param array $settle
     * @return bool|array
     * @throws \Exception
     */
    public function settle($apiKey, $handle, $settle = ['key' => ''])
    {
        $log = ['param' => ['handle' => $handle, 'settle' => $settle]];
        $response = $this->client->post($apiKey, self::ENDPOINT . "/{$handle}/settle", $settle);
        if ($this->client->success()) {
            $log['response'] = $response;
            $this->logger->addInfo(__METHOD__, $log, true);

            return $response;
        } else {
            $log['http_errors'] = $this->client->getHttpError();
            $log['response_errors'] = $this->client->getErrors();
            $this->logger->addError(__METHOD__, $log, true);

            return $response;
        }
    }

    /**
     * Cancel charge
     * Cancel an authorized charge. A void of reserved money will be attempted.
     *
     * @param string $apiKey
     * @param string $handle
     * @return bool|array
     * @throws \Exception
     */
    public function cancel($apiKey, $handle)
    {
        $log = ['param' => ['handle' => $handle]];
        $response = $this->client->post($apiKey, self::ENDPOINT . "/{$handle}/cancel");
        if ($this->client->success()) {
            $log['response'] = $response;
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
     * Delete charge
     * Delete an created charge. A void of reserved money will be attempted.
     *
     * @param string $apiKey
     * @param string $handle
     * @return bool|array
     * @throws \Exception
     */
    public function delete($apiKey, $handle)
    {
        $log = ['param' => ['handle' => $handle]];
        $response = $this->client->delete($apiKey, self::ENDPOINT . "/{$handle}");
        if ($this->client->success()) {
            $log['response'] = $response;
            $this->logger->addInfo(__METHOD__, $log, true);

            return $response;
        } else {
            $log['http_errors'] = $this->client->getHttpError();
            $log['response_errors'] = $this->client->getErrors();
            $this->logger->addError(__METHOD__, $log, true);

            return false;
        }
    }
}
