<?php
namespace Radarsofthouse\Reepay\Model\Config\Source\Email;

class Template implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Email\Template
     */
    private $templateSource;

    /**
     * @param \Magento\Config\Model\Config\Source\Email\Template $templateSource
     */
    public function __construct(
        \Magento\Config\Model\Config\Source\Email\Template $templateSource
    ) {
        $this->templateSource = $templateSource;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->templateSource->setPath('payment_reepay_payment_payment_link')->toOptionArray();
    }
}