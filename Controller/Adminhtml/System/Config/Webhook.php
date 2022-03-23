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
            $urls = null;
            $urlsTest = null;
            $storeId = $this->storeManager->getStore()->getId();
            $apiKey = $this->helper->getConfig('private_key', $storeId);
            $apiKeyTest = $this->helper->getConfig('private_key_test', $storeId);
            $this->logger->addDebug('api key.', ['api_key'=>$apiKey, 'api_key_test'=>$apiKeyTest]);
            if (!empty($apiKey)) {
                $urls = $this->webhookHelper->updateUrl($apiKey);
            }
            if (!empty($apiKeyTest)) {
                $urlsTest = $this->webhookHelper->updateUrl($apiKeyTest);
            }
            $this->logger->addDebug('webhook update result', ['urls'=>$urls, 'urls_test'=>$urlsTest]);
            if (($urls !== false && $urls !== null) || ($urlsTest !== false && $urlsTest !== null)) {
                return $result->setData([
                    'success' => true,
                    'urls' => $urls,
                    'urls_test' => $urlsTest,
                    'time' => $lastUpdateTime
                ]);
            }
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
