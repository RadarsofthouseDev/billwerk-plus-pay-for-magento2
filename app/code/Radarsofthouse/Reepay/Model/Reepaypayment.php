<?php
 
namespace Radarsofthouse\Reepay\Model;

/**
 * Class Reepaypayment
 *
 * @package Radarsofthouse\Reepay\Model
 */
class Reepaypayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = 'reepay_payment';
    protected $_isInitializeNeeded = true;
    protected $_canUseInternal = false;
    protected $_canCapture = true;
    protected $_canRefund = true;
    protected $_isGateway = true;
    
    /**
     * override capture payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if (!$this->canCapture()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The capture action is not available.'));
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reepayCharge = $objectManager->create('Radarsofthouse\Reepay\Helper\Charge');
        $reepayHelper = $objectManager->create('Radarsofthouse\Reepay\Helper\Data');
        $logger = $objectManager->create('Radarsofthouse\Reepay\Helper\Logger');

        $order = $payment->getOrder();
        $amount = $amount;

        $logger->addDebug(__METHOD__, ['capture : '.$order->getIncrementId()]);

        $apiKey = $reepayHelper->getApiKey();

        $charge = $reepayCharge->settle(
            $apiKey,
            $order->getIncrementId()
        );

        if (!empty($charge)) {
            if ($charge['state'] == 'settled') {
                $_payment = $order->getPayment();
                $reepayHelper->setReepayPaymentState($_payment, 'settled');
                $order->save();

                $logger->addDebug('settled : '.$order->getIncrementId());
            }
        }

        return $this;
    }

    /**
     * override refund payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        if (!$this->canRefund()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The refund action is not available.'));
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reepayRefund = $objectManager->create('Radarsofthouse\Reepay\Helper\Refund');
        $reepayHelper = $objectManager->create('Radarsofthouse\Reepay\Helper\Data');
        $logger = $objectManager->create('Radarsofthouse\Reepay\Helper\Logger');
        
        $order = $payment->getOrder();
        $amount = $amount;

        $logger->addDebug(__METHOD__, ['capture : '.$order->getIncrementId()]);

        $apiKey = $reepayHelper->getApiKey();

        $refund = $reepayRefund->create(
            $apiKey,
            ['invoice' => $order->getIncrementId()]
        );
        if (!empty($refund)) {
            if ($refund['state'] == 'refunded') {
                $_payment = $order->getPayment();
                $reepayHelper->setReepayPaymentState($_payment, 'refunded');
                $order->save();

                $logger->addDebug('refunded : '.$order->getIncrementId());
            }
        }

        return $this;
    }
}
