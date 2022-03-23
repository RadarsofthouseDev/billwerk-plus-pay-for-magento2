<?php

namespace Radarsofthouse\Reepay\Block\Standard;

class Success extends \Magento\Framework\View\Element\Template
{
    /**
     * Get Continue URL
     *
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
