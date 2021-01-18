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
class Customer extends AbstractHelper
{
    const ENDPOINT = 'customer';
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
     * Get customer by email.
     *
     * @param $apiKey
     * @param $email
     * @return false|string
     */
    public function search($apiKey, $email){
        $log = ['param' => ['email' => $email]];
        $param = [
            'page'=> 1,
            'size'=> 20,
            'search'=> "email:{$email}",
        ];
        if(empty($email)){
            $log['input_error'] = 'empty email.';
            $this->logger->addInfo(__METHOD__, $log, true);
            return false;
        }
        try {
            $response = $this->client->get($apiKey, self::ENDPOINT,$param);
            $log['response'] = $response;
            $this->logger->addInfo(__METHOD__, $log, true);
            if ($this->client->success() && array_key_exists('count', $response) && (int)$response['count'] > 0) {
                foreach ($response['content'] as $index => $item) {
                    if(!array_key_exists('deleted', $item) || empty($item['deleted'])) {
                        return $item['handle'];
                    }
                }
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
