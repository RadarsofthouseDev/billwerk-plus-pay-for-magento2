<?php

namespace Radarsofthouse\Reepay\Model\Config\Source;

class Displaytype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return Reepay payment display types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Embedded')],
            ['value' => 2, 'label' => __('Overlay (Modal)')],
            ['value' => 3, 'label' => __('Window')],
        ];
    }
}
