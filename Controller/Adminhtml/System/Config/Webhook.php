<?php

namespace Radarsofthouse\Reepay\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Radarsofthouse\Reepay\Helper\Logger;
use Radarsofthouse\Reepay\Helper\Webhook as WebhookHelper;
use Radarsofthouse\Reepay\Helper\Data as DataHelper;

class Webhook extends Action
{

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var DataHelper
     */
    protected $helper;

    /**
     * @var WebhookHelper
     */
    private $webhookHelper;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param StoreManagerInterface $storeManager
     * @param DataHelper $helper
     * @param WebhookHelper $webhookHelper
     * @param TimezoneInterface $timezone
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        StoreManagerInterface $storeManager,
        DataHelper $helper,
        WebhookHelper $webhookHelper,
        TimezoneInterface $timezone,
        Logger $logger
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->webhookHelper = $webhookHelper;
        $this->timezone = $timezone;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $lastUpdateTime = $this->timezone->formatDate(null, \IntlDateFormatter::MEDIUM, true);

        try {
            $apiKeys = [];
            $stores = $this->storeManager->getStores();
            foreach ($stores as $store) {
                $storeId = $store->getId();
                foreach (['private_key', 'private_key_test'] as $keyType) {
                    $apiKey = $this->helper->getConfig($keyType, $storeId);
                    if (!empty($apiKey)) {
                        $apiKeys[] = $apiKey;
                    }
                }
            }
            $apiKeys = array_unique($apiKeys);

            $failure = [];
            $success = [];
            foreach ($apiKeys as $apiKey) {
                $updatedUrls = $this->webhookHelper->updateUrl($apiKey);
                if ($updatedUrls === false) {
                    $failure[] = $apiKey;
                } else {
                    $success[$apiKey] = $updatedUrls;
                }
            }

            if (!empty($failure)) {
                $this->logger->addError('Failed to update webhook URLs', ['failed_keys' => $failure]);
                return $result->setData([
                    'success' => false,
                    'failed_keys' => $failure,
                    'time' => $lastUpdateTime
                ]);
            }

            $this->logger->addInfo('Updated webhook URLs', ['success' => $success]);
            return $result->setData([
                'success' => true,
                'updated_urls' => $success,
                'time' => $lastUpdateTime
            ]);
        } catch (\Exception $e) {
            $this->logger->addError($e->getMessage());
        }

        return $result->setData(['success' => false, 'time' => $lastUpdateTime]);
    }

    /**
     * Is Allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Radarsofthouse_Reepay::config');
    }
}
