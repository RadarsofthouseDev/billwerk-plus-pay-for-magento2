<?php

namespace Radarsofthouse\Reepay\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;

/**
 * Class ConfigProvider
 *
 * @package Radarsofthouse\Reepay\Model
 */
class ConfigProvider implements ConfigProviderInterface
{
    /** @var LayoutInterface  */
    protected $_layout;

    public function __construct(LayoutInterface $layout)
    {
        $this->_layout = $layout;
    }

    /**
     * provide payment icons html
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment_icons' => $this->_layout->createBlock('Radarsofthouse\Reepay\Block\Paymenticons')->setTemplate('Radarsofthouse_Reepay::payment_icons.phtml')->toHtml(),
            'viabill_payment_icons' => $this->_layout->createBlock('Radarsofthouse\Reepay\Block\Paymenticons')->setTemplate('Radarsofthouse_Reepay::viabill_payment_icons.phtml')->toHtml(),
            'mobilepay_payment_icons' => $this->_layout->createBlock('Radarsofthouse\Reepay\Block\Paymenticons')->setTemplate('Radarsofthouse_Reepay::mobilepay_payment_icons.phtml')->toHtml(),
        ];
    }
}
