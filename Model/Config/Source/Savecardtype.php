<?php

namespace Radarsofthouse\Reepay\Model\Config\Source;

class Savecardtype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return Reepay payment key types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('CIT (Customer Initiated Transaction)')],
            ['value' => 1, 'label' => __('MIT (Merchant Initiated Transaction)')],
        ];
    }
}
