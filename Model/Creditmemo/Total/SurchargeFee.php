<?php

namespace Radarsofthouse\Reepay\Model\Creditmemo\Total;

class SurchargeFee extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    /**
     * @var \Radarsofthouse\Reepay\Helper\Data
     */
    private $_helperData;

    /**
     * @var \Radarsofthouse\Reepay\Helper\SurchargeFee
     */
    private $_helperSurchargeFee;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Logger
     */
    private $_helperLogger;

    /**
     * SurchargeFee constructor
     *
     * @param \Radarsofthouse\Reepay\Helper\Data $helperData
     * @param \Radarsofthouse\Reepay\Helper\SurchargeFee $helperSurchargeFee
     * @param \Radarsofthouse\Reepay\Helper\Logger $helperLogger
     * @param array $data
     */
    public function __construct(
        \Radarsofthouse\Reepay\Helper\Data $helperData,
        \Radarsofthouse\Reepay\Helper\SurchargeFee $helperSurchargeFee,
        \Radarsofthouse\Reepay\Helper\Logger $helperLogger,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_helperData = $helperData;
        $this->_helperSurchargeFee = $helperSurchargeFee;
        $this->_helperLogger = $helperLogger;
    }

    /**
     * Collect total
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $payment = $creditmemo->getOrder()->getPayment();
        $storeId = $creditmemo->getOrder()->getStoreId();
        $paymentMethod = null;
        if ($payment !== null) {
            $paymentMethod = $payment->getMethod();
        }
        $isReepayPaymentMethod = $this->_helperData->isReepayPaymentMethod($paymentMethod);
        $isEnable = $this->_helperData->isSurchargeFeeEnabled($storeId);
        $this->_helperLogger->addDebug(
            __METHOD__,
            [
                'PaymentMethod' => $paymentMethod,
                'isReepayPaymentMethod' => $isReepayPaymentMethod,
                'SurchargeFeeEnabled' => ($isEnable ? 'true' : 'false')
            ]
        );
        if (!$isEnable || !$isReepayPaymentMethod) {
            return $this;
        }

        $amount = $creditmemo->getReepaySurchargeFee();
        $this->_helperLogger->addDebug('DefaultReepaySurchargeFee: ', [$amount]);

        if (empty($amount)) {
            $this->_helperLogger->addDebug('isEmptyReepaySurchargeFee: ', [$amount]);
            $amount = $this->_helperSurchargeFee->getAvailableSurchargeFeeRefundAmount(
                $creditmemo->getOrder()->getEntityId()
            );
            $creditmemo->setReepaySurchargeFee($amount);
        }
        $this->_helperLogger->addDebug('ResultReepaySurchargeFee: ', [$amount]);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $amount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $amount);

        return $this;
    }
}
