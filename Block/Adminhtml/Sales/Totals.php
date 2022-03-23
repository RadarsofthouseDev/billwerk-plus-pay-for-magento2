<?php

namespace Radarsofthouse\Reepay\Block\Adminhtml\Sales;

class Totals extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

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
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Radarsofthouse\Reepay\Helper\Data $helperData
     * @param \Radarsofthouse\Reepay\Helper\Logger $helperLogger
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\Currency $currency,
        \Radarsofthouse\Reepay\Helper\Data $helperData,
        \Radarsofthouse\Reepay\Helper\Logger $helperLogger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_currency = $currency;
        $this->_helperData = $helperData;
        $this->_helperLogger = $helperLogger;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get Source
     *
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get Currency Symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     * Init totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getOrder();
        $this->getSource();
        /** @var \Magento\Sales\Api\Data\OrderPaymentInterface $payment */
        $payment = $this->getOrder()->getPayment();
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
            if (!$isReepayPaymentMethod || !$isEnable) {
                $this->_helperLogger->addDebug('Sale ReepaySurchargeFee Total Disabled');
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

        $this->getParentBlock()->addTotal($total);
        return $this;
    }
}
