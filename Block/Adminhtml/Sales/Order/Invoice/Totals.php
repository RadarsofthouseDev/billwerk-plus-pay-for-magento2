<?php

namespace Radarsofthouse\Reepay\Block\Adminhtml\Sales\Order\Invoice;

class Totals extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Radarsofthouse\Reepay\Helper\Data
     */
    private $_helperData;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Logger
     */
    private $_helperLogger;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Radarsofthouse\Reepay\Helper\Data $helperData
     * @param \Radarsofthouse\Reepay\Helper\Logger $helperLogger
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Radarsofthouse\Reepay\Helper\Data $helperData,
        \Radarsofthouse\Reepay\Helper\Logger $helperLogger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helperData = $helperData;
        $this->_helperLogger = $helperLogger;
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get invoice
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function getInvoice()
    {
        return $this->getParentBlock()->getInvoice();
    }

    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getInvoice();
        $this->getSource();
        /** @var \Magento\Sales\Api\Data\OrderPaymentInterface $payment */
        $payment = $this->getInvoice()->getOrder()->getPayment();
        $paymentMethod = null;
        if ($payment !== null) {
            $paymentMethod = $payment->getMethod();
        }
        $isReepayPaymentMethod = $this->_helperData->isReepayPaymentMethod($paymentMethod);
        $isEnable = $this->_helperData->isSurchargeFeeEnabled();
        $surchargeFee = $this->getSource()->getReepaySurchargeFee();
        $this->_helperLogger->addDebug(
            __METHOD__,
            ['PaymentMethod' => $paymentMethod,
            'isReepayPaymentMethod' => $isReepayPaymentMethod,
            'SurchargeFeeEnabled' => ($isEnable ? 'true' : 'false'),
            'SurchargeFee' => $surchargeFee]
        );
        if (empty($surchargeFee) || $surchargeFee == 0) {
            if (!$isEnable || !$isReepayPaymentMethod) {
                $this->_helperLogger->addDebug('Invoice ReepaySurchargeFee Total Disabled');
                return $this;
            }
        }
        $total = new \Magento\Framework\DataObject(
            [
                'code' => 'reepay_surcharge_fee',
                'strong' => false,
                'value' => $surchargeFee,
                'label' => __('Surcharge Fee'),
            ]
        );
        $this->_helperLogger->addDebug('Total', [
            'code' => 'reepay_surcharge_fee',
            'strong' => false,
            'value' => $surchargeFee,
            'label' => __('Surcharge Fee'),
        ]);

        $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        return $this;
    }
}
