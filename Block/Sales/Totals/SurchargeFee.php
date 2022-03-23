<?php

namespace Radarsofthouse\Reepay\Block\Sales\Totals;

class SurchargeFee extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Data
     */
    private $_helperData;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Logger
     */
    private $_helperLogger;

    /**
     * SurchargeFee constructor.
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
     * Check if we nedd display full tax total info
     *
     * @return bool
     */
    public function displayFullSummary()
    {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Get order store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_order->getStore();
    }

    /**
     * Get order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Get Label Properties
     *
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get Value Properties
     *
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Init Totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();

        /** @var \Magento\Sales\Api\Data\OrderPaymentInterface $payment */
        $payment = $this->_order->getPayment();
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
                $this->_helperLogger->addDebug('Sale ReepaySurchargeFee Total Disabled');
                return $this;
            }
        }

        $surchargeFee = new \Magento\Framework\DataObject(
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

//        $parent->addTotal($surchargeFee, 'reepay_surcharge_fee');
        $this->getParentBlock()->addTotal($surchargeFee, 'reepay_surcharge_fee');

        return $this;
    }
}
