<?php
/**
 * Radarsofthouse
 * Copyright (C) 2019 Radarsofthouse
 *
 * This file included in Radarsofthouse/Reepay is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Radarsofthouse\Reepay\Plugin\Magento\Sales\Model\Order;

class CreditmemoFactory
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
     * CreditmemoFactory constructor.
     * @param \Radarsofthouse\Reepay\Helper\Data $helperData
     * @param \Radarsofthouse\Reepay\Helper\SurchargeFee $helperSurchargeFee
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     */
    public function __construct(
        \Radarsofthouse\Reepay\Helper\Data $helperData,
        \Radarsofthouse\Reepay\Helper\SurchargeFee $helperSurchargeFee,
        \Radarsofthouse\Reepay\Helper\Logger $logger
    ) {
        $this->_helperData = $helperData;
        $this->_helperSurchargeFee = $helperSurchargeFee;
        $this->_helperLogger = $logger;
    }

    public function aroundCreateByOrder(
        \Magento\Sales\Model\Order\CreditmemoFactory $subject,
        \Closure $proceed,
        \Magento\Sales\Model\Order $order,
        $data = []
    ) {
        /** @var \Magento\Sales\Model\Order\Creditmemo $result */
        $result = $proceed($order, $data);
        $payment = $order->getPayment();
        $paymentMethod = null;
        if ($payment !== null) {
            $paymentMethod = $payment->getMethod();
        }
        $isReepayPaymentMethod = $this->_helperData->isReepayPaymentMethod($paymentMethod);
        $isEnable = $this->_helperData->isSurchargeFeeEnabled();
        $this->_helperLogger->addDebug(__METHOD__, ['PaymentMethod' => $paymentMethod, 'isReepayPaymentMethod'=>$isReepayPaymentMethod, 'SurchargeFeeEnabled'=> ($isEnable ? 'true' : 'false')]);
        if (!$isEnable || !$isReepayPaymentMethod) {
            return $result;
        }
        $this->_helperLogger->addDebug(__METHOD__, $data);
        if (array_key_exists('reepay_surcharge_fee', $data)) {
            $surchargeFee = round((float)$result->getReepaySurchargeFee(), 2);
            $inputSurchargeFee = round((float)$data['reepay_surcharge_fee'], 2);
            $availableSurchargeFee = $this->_helperSurchargeFee->getAvailableSurchargeFeeRefundAmount($order->getId());
            if ($surchargeFee === $inputSurchargeFee) {
                $this->_helperLogger->addDebug('surchargeFee == inputSurchargeFee');
                return $result;
            }
            if ($inputSurchargeFee > $availableSurchargeFee) {
                $this->_helperLogger->addDebug(' inputSurchargeFee > surchargeFee');
                $inputSurchargeFee = $availableSurchargeFee;
            }
            $this->_helperLogger->addDebug(__METHOD__, ['surchargeFee'=> $surchargeFee, 'inputSurchargeFee' => $inputSurchargeFee, 'GrandTotal'=> $result->getGrandTotal(), 'BaseGrandTotal'=> $result->getBaseGrandTotal()]);
            $result->setGrandTotal($result->getGrandTotal() - $surchargeFee + $inputSurchargeFee);
            $result->setBaseGrandTotal($result->getBaseGrandTotal() - $surchargeFee + $inputSurchargeFee);
            $result->setReepaySurchargeFee($inputSurchargeFee);
            $this->_helperLogger->addDebug(__METHOD__, ['newGrandTotal'=> $result->getGrandTotal(), 'newBaseGrandTotal'=> $result->getBaseGrandTotal()]);
        }
        return $result;
    }

    public function aroundCreateByInvoice(
        \Magento\Sales\Model\Order\CreditmemoFactory $subject,
        \Closure $proceed,
        \Magento\Sales\Model\Order\Invoice $invoice,
        $data = []
    ) {
        /** @var \Magento\Sales\Model\Order\Creditmemo $result */
        $result = $proceed($invoice, $data);
        $order = $invoice->getOrder();
        $payment = $order->getPayment();
        $paymentMethod = null;
        if ($payment !== null) {
            $paymentMethod = $payment->getMethod();
        }
        $isReepayPaymentMethod = $this->_helperData->isReepayPaymentMethod($paymentMethod);
        $isEnable = $this->_helperData->isSurchargeFeeEnabled();
        $this->_helperLogger->addDebug(__METHOD__, ['PaymentMethod' => $paymentMethod, 'isReepayPaymentMethod'=>$isReepayPaymentMethod, 'SurchargeFeeEnabled'=> ($isEnable ? 'true' : 'false')]);
        if (!$isEnable || !$isReepayPaymentMethod) {
            return $result;
        }
        $this->_helperLogger->addDebug(__METHOD__, $data);
        if (array_key_exists('reepay_surcharge_fee', $data)) {
            $surchargeFee = round((float)$result->getReepaySurchargeFee(), 2);
            $inputSurchargeFee = round((float)$data['reepay_surcharge_fee'], 2);
            $availableSurchargeFee = $this->_helperSurchargeFee->getAvailableSurchargeFeeRefundAmount($invoice->getOrder()->getId());
            if ($surchargeFee === $inputSurchargeFee) {
                $this->_helperLogger->addDebug('surchargeFee == inputSurchargeFee');
                return $result;
            }
            if ($inputSurchargeFee > $availableSurchargeFee) {
                $this->_helperLogger->addDebug(' inputSurchargeFee > surchargeFee');
                $inputSurchargeFee = $availableSurchargeFee;
            }
            $this->_helperLogger->addDebug(__METHOD__, ['surchargeFee'=> $surchargeFee, 'inputSurchargeFee' => $inputSurchargeFee, 'GrandTotal'=> $result->getGrandTotal(), 'BaseGrandTotal'=> $result->getBaseGrandTotal()]);
            $result->setGrandTotal($result->getGrandTotal() - $surchargeFee + $inputSurchargeFee);
            $result->setBaseGrandTotal($result->getBaseGrandTotal() - $surchargeFee + $inputSurchargeFee);
            $result->setReepaySurchargeFee($inputSurchargeFee);
            $this->_helperLogger->addDebug(__METHOD__, ['newGrandTotal'=> $result->getGrandTotal(), 'newBaseGrandTotal'=> $result->getBaseGrandTotal()]);
        }
        return $result;
    }
}
