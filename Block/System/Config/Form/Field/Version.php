<?php

namespace Radarsofthouse\Reepay\Block\System\Config\Form\Field;

class Version extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Framework\Module\ResourceInterface
     */
    protected $moduleResource;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Module\ResourceInterface $moduleResource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\ResourceInterface $moduleResource,
        array $data = []
    ) {
        $this->moduleResource = $moduleResource;
        parent::__construct($context, $data);
    }

    /**
     * Set Payment Transaction Id
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string $version
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $version = $this->moduleResource->getDbVersion('Radarsofthouse_Reepay');
        return $version;
    }
}
