<?php

namespace Radarsofthouse\Reepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Email
 *
 * @package Radarsofthouse\Reepay\Helper
 */
class Email extends AbstractHelper
{
    protected $_transportBuilder;
    protected $_reepayHelper;
    protected $_scopeConfig;
    protected $_logger;
    protected $_storeManager;
 
    /**
    * @param Magento\Framework\App\Helper\Context $context
    * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    * @param Magento\Store\Model\StoreManagerInterface $storeManager
    * @param Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    * @param Radarsofthouse\Reepay\Helper\Data $reepayHelper
    * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    * @param Radarsofthouse\Reepay\Helper\Logger $logger
    * @param \Magento\Store\Model\StoreManagerInterface $storeManager
    */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Radarsofthouse\Reepay\Helper\Logger $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager 
    ) {
        parent::__construct($context);
        $this->_transportBuilder = $transportBuilder;
        $this->_reepayHelper = $reepayHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;

    }
    
    public function sendPaymentLinkEmail($order, $paymentTransactionId)
    {
        $emailTemplateId = $this->_reepayHelper->getConfig('payment_link', $order->getStoreId());
        $this->_logger->addDebug('$emailTemplateId : '.$emailTemplateId);

        if(empty($emailTemplateId)){
            $emailTemplateId = "payment_us_reepay_payment_payment_link";
        }

        $senderName = $this->_scopeConfig->getValue(
            'trans_email/ident_sales/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $order->getStoreId()
        );

        $senderEmail = $this->_scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $order->getStoreId()
        );

        $this->_logger->addDebug('$senderName : '.$senderName);
        $this->_logger->addDebug('$senderEmail : '.$senderEmail);



        $transport = $this->_transportBuilder->setTemplateIdentifier( $emailTemplateId )
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $order->getStoreId(),
                    ]
                )
                ->setTemplateVars(
                    [
                        "increment_id" => $order->getIncrementId(),
                        "payment_url" => 'https://checkout.reepay.com/#/'.$paymentTransactionId
                    ]
                )
                ->setFrom(
                    [
                        'name' => $senderName,
                        'email' => $senderEmail
                    ]
                )
                ->addTo(
                    $order->getCustomerEmail(),
                    $order->getCustomerName()
                )          
                ->getTransport();               
        $sendResult = $transport->sendMessage();
        $this->_logger->addDebug('$sendResult');
        $this->_logger->addDebug($sendResult);


    }


}
