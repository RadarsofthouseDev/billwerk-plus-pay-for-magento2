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
        try {
            $url = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_LINK, true);
            $url .= $url[-1] === '/' ? '' : '/';
            $url .= 'reepay/webhooks/index';
        } catch (NoSuchEntityException $e) {
            $log['exception_error'] = $e->getMessage();
            $this->logger->addInfo(__METHOD__, $log, true);
            return false;
        }
        $urls = [$url];
        $currentUrls = $this->getUrl($apiKey);
        if ($currentUrls !== false && !empty($currentUrls)) {
            $urls = $currentUrls;
            $isExistUrl = array_search($url, $currentUrls);
            if ($isExistUrl === false) {
                $urls[] = $url;
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
