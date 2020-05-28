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
    protected $_canUseInternal = true;
    protected $_canCapture = true;
    protected $_canRefund = true;
    protected $_isGateway = true;
    protected $_canCapturePartial = true;
    protected $_canRefundInvoicePartial = true;
    
    /**
     * override capture payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {

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
        
        return $this;
    }
}
