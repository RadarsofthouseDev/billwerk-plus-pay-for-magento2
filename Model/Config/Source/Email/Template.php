<?php
namespace Radarsofthouse\Reepay\Model\Config\Source\Email;

class Template implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Email\Template
     */
    private $templateSource;

    /**
     * Constructor
     *
     * @param \Magento\Config\Model\Config\Source\Email\Template $templateSource
     */
    public function __construct(
        \Magento\Config\Model\Config\Source\Email\Template $templateSource
    ) {
        $this->templateSource = $templateSource;
    }

    /**
     * Return list of payment link
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->templateSource->setPath('payment_reepay_payment_payment_link')->toOptionArray();
    }
}
