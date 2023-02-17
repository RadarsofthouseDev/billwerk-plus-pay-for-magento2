<?php

namespace Radarsofthouse\Reepay\Model\Config\Source;

class Allowwedpayment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return Reepay payment allowwed payments
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'card', 'label' => __('All available debit / credit cards')],
            ['value' => 'dankort', 'label' => __('Dankort')],
            ['value' => 'visa', 'label' => __('VISA')],
            ['value' => 'visa_dk', 'label' => __('VISA/Dankort')],
            ['value' => 'visa_elec', 'label' => __('VISA Electron')],
            ['value' => 'mc', 'label' => __('MasterCard')],
            ['value' => 'amex', 'label' => __('American Express')],
            ['value' => 'mobilepay', 'label' => __('MobilePay')],
            ['value' => 'viabill', 'label' => __('ViaBill')],
            ['value' => 'anyday', 'label' => __('Anyday')],
            ['value' => 'klarna_pay_later', 'label' => __('Klarna Pay Later')],
            ['value' => 'klarna_pay_now', 'label' => __('Klarna Pay Now')],
            ['value' => 'klarna_slice_it', 'label' => __('Klarna Slice It')],
            ['value' => 'klarna_direct_bank_transfer', 'label' => __('Klarna Direct Bank Transfer')],
            ['value' => 'klarna_direct_debit', 'label' => __('Klarna Direct Debit')],
            ['value' => 'diners', 'label' => __('Diners Club')],
            ['value' => 'maestro', 'label' => __('Maestro')],
            ['value' => 'laser', 'label' => __('Laser')],
            ['value' => 'discover', 'label' => __('Discover')],
            ['value' => 'jcb', 'label' => __('JCB')],
            ['value' => 'china_union_pay', 'label' => __('China Union Pay')],
            ['value' => 'ffk', 'label' => __('Forbrugsforeningen')],
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
