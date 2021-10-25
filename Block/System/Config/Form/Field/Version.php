<?php

namespace Radarsofthouse\Reepay\Block\System\Config\Form\Field;

/**
 * Class Version
 *
 * @package Radarsofthouse\Reepay\Block\System\Config\Form\Field
 */
class Version extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $moduleResource;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\ResourceInterface $moduleResource,
        array $data = []
    ) {
        $this->moduleResource = $moduleResource;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $version = $this->moduleResource->getDbVersion('Radarsofthouse_Reepay');
        return $version;
    }

}
