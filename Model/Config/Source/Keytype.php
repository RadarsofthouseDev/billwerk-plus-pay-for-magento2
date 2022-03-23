<?php

namespace Radarsofthouse\Reepay\Model\Config\Source;

class Keytype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return Reepay payment key types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Test')],
            ['value' => 1, 'label' => __('Live')],
        ];
    }
}
