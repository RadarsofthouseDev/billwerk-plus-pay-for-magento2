<?php
 
namespace Radarsofthouse\Reepay\Model;

class Reepaypayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * @var string
     */
    protected $_code = 'reepay_payment';

    /**
     * @var boolean
     */
    protected $_isInitializeNeeded = true;

    /**
     * @var boolean
     */
    protected $_canUseInternal = true;

    /**
     * @var boolean
     */
    protected $_canCapture = true;

    /**
     * @var boolean
     */
    protected $_canRefund = true;

    /**
     * @var boolean
     */
    protected $_isGateway = true;

    /**
     * @var boolean
     */
    protected $_canCapturePartial = true;

    /**
     * @var boolean
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var boolean
     */
    protected $_isAutoCapture = false;
    
    /**
     * Override capture payment
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
     * Override refund payment
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        return $this;
    }

    /**
     * Check payment type is "auto_capture" payment
     *
     * @return $bool
     */
    public function isAutoCapture(){
        return $this->_isAutoCapture;
    }
}
