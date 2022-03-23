<?php

namespace Radarsofthouse\Reepay\Block\Adminhtml\Sales\Order\Creditmemo\View;

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
     * Totals constructor.
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
     *  Get Creditmemo
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->getParentBlock()->getCreditmemo();
    }

    /**
     *  Get Surcharge Fee Label
     *
     * @return string
     */
    public function getReepaySurchargeFeeLabel()
    {
        return __('Surcharge Fee');
    }

    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getCreditmemo();
        $this->getSource();
        /** @var \Magento\Sales\Api\Data\OrderPaymentInterface $payment */
        $payment = $this->getCreditmemo()->getOrder()->getPayment();
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
                $this->_helperLogger->addDebug('Creditmemo ReepaySurchargeFee Total Disabled');
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
