<?php

namespace Radarsofthouse\Reepay\Model\Config\Source;

class Paymenticons implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return Reepay payment icons
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'american-express', 'label' => __('American express')],
            ['value' => 'dankort', 'label' => __('Dankort')],
            ['value' => 'diners-club-international', 'label' => __('Diners club international')],
            ['value' => 'discover', 'label' => __('Discover')],
            ['value' => 'forbrugsforeningen', 'label' => __('Forbrugsforeningen')],
            ['value' => 'jcb', 'label' => __('JCB')],
            ['value' => 'maestro', 'label' => __('Maestro')],
            ['value' => 'mastercard', 'label' => __('Mastercard')],
            ['value' => 'mobilepay', 'label' => __('Mobilepay')],
            ['value' => 'unionpay', 'label' => __('Unionpay')],
            ['value' => 'viabill', 'label' => __('Viabill')],
            ['value' => 'anyday', 'label' => __('Anyday')],
            ['value' => 'visa', 'label' => __('Visa')],
            ['value' => 'visa-electron', 'label' => __('Visa electron')],
            ['value' => 'klarna-pay-later', 'label' => __('Klarna Pay Later')],
            ['value' => 'klarna-pay-now', 'label' => __('Klarna Pay Now')],
            ['value' => 'klarna-slice-it', 'label' => __('Klarna Slice It')],
            ['value' => 'klarna-direct-bank-transfer', 'label' => __('Klarna Direct Bank Transfer')],
            ['value' => 'klarna-direct-debit', 'label' => __('Klarna Direct Debit')],
            ['value' => 'applepay', 'label' => __('Apple Pay')],
            ['value' => 'paypal', 'label' => __('PayPal')],
            ['value' => 'resurs', 'label' => __('Resurs Bank')],
            ['value' => 'vipps', 'label' => __('Vipps')],
            ['value' => 'googlepay', 'label' => __('Google Pay')],
            ['value' => 'blik', 'label' => __('BLIK')],
            ['value' => 'p24', 'label' => __('Przelewy24 (P24)')],
            ['value' => 'giropay', 'label' => __('giropay')]
        ];
    }
}
