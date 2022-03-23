<?php

namespace Radarsofthouse\Reepay\Model\Config\Source;

class Log implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return Reepay payment display types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Disabled')],
            ['value' => 1, 'label' => __('Only Reepay API')],
            ['value' => 2, 'label' => __('Debug mode')],
        ];
    }
}
