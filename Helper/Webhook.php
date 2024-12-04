<?php

namespace Radarsofthouse\Reepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Radarsofthouse\Reepay\Client\Api;

class Webhook extends AbstractHelper
{
    public const ENDPOINT = 'account/webhook_settings';
    const WEBHOOK_PATH = '/reepay/webhooks/index';

    /**
     * @var \Radarsofthouse\Reepay\Client\Api
     */
    private $client = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Logger
     */
    private $logger = null;

    /**
     * Constructor
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Logger $logger
     */
    public function __construct(Context $context, StoreManagerInterface $storeManager, Logger $logger)
    {
        parent::__construct($context);
        $this->client = new Api();
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * Update webhook url
     *
     * @param string $apiKey
     * @return bool
     */
    public function getUrl($apiKey)
    {
        $param = [];
        $log = ['param' => $param];
        try {
            $response = $this->client->get($apiKey, self::ENDPOINT, $param);
            $log['response'] = $response;
            $this->logger->addInfo(__METHOD__, $log, true);
            if ($this->client->success()) {
                return $response['urls'];
            }
        } catch (\Exception $e) {
            $log['exception_error'] = $e->getMessage();
            $log['http_errors'] = $this->client->getHttpError();
            $log['response_errors'] = $this->client->getErrors();
            $this->logger->addInfo(__METHOD__, $log, true);
        }
        return false;
    }

    /**
     * Update webhook url
     *
     * @param string $apiKey
     * @return bool
     */
    public function updateUrl($apiKey)
    {
        // Try to get the default store view first
        $defaultStore = $this->storeManager->getDefaultStoreView();
        if (!$defaultStore) {
            // Default store not found, get the first activated store
            $stores = $this->storeManager->getStores();
            foreach ($stores as $store) {
                if ($store->getIsActive()) {
                    $defaultStore = $store;
                    break;
                }
            }

            if (!$defaultStore) {
                $this->logger->addError('No active store found.');
                return false;
            }
        }

        $defaultStoreBaseUrl = rtrim($defaultStore->getBaseUrl(), '/');
        $webhookUrl = $defaultStoreBaseUrl . self::WEBHOOK_PATH;

        $urls = [$webhookUrl];
        $currentUrls = $this->getUrl($apiKey);
        if (is_array($currentUrls) && !empty($currentUrls)) {
            // Remove any URLs that contain the webhook path
            $currentUrls = array_filter($currentUrls, function ($url) {
                return strpos($url, self::WEBHOOK_PATH) === false;
            });
            $currentUrls = array_values($currentUrls);

            $urls = $currentUrls;
            // add webhook URL
            if (!in_array($webhookUrl, $currentUrls)) {
                $urls[] = $webhookUrl;
            }
        }

        $param = [
            'urls' => $urls,
            'disabled' => false,
        ];
        $log = ['param' => $param];
        try {
            $response = $this->client->put($apiKey, self::ENDPOINT, $param);
            $log['response'] = $response;
            $this->logger->addInfo(__METHOD__, $log, true);
            if ($this->client->success()) {
                return $response['urls'];
            }
        } catch (\Exception $e) {
            $log['exception_error'] = $e->getMessage();
            $log['http_errors'] = $this->client->getHttpError();
            $log['response_errors'] = $this->client->getErrors();
            $this->logger->addInfo(__METHOD__, $log, true);
        }
        return false;
    }
}
