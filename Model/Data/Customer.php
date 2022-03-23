<?php

namespace Radarsofthouse\Reepay\Model\Data;

use Radarsofthouse\Reepay\Api\Data\CustomerInterface;

class Customer extends \Magento\Framework\Api\AbstractExtensibleObject implements CustomerInterface
{

    /**
     * Get customer_id
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     *
     * @param string $customerId
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get magento_customer_id
     *
     * @return string|null
     */
    public function getMagentoCustomerId()
    {
        return $this->_get(self::MAGENTO_CUSTOMER_ID);
    }

    /**
     * Set magento_customer_id
     *
     * @param string $magentoCustomerId
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     */
    public function setMagentoCustomerId($magentoCustomerId)
    {
        return $this->setData(self::MAGENTO_CUSTOMER_ID, $magentoCustomerId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param \Radarsofthouse\Reepay\Api\Data\CustomerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Radarsofthouse\Reepay\Api\Data\CustomerExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get magento_email
     *
     * @return string|null
     */
    public function getMagentoEmail()
    {
        return $this->_get(self::MAGENTO_EMAIL);
    }

    /**
     * Set magento_email
     *
     * @param string $magentoEmail
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     */
    public function setMagentoEmail($magentoEmail)
    {
        return $this->setData(self::MAGENTO_EMAIL, $magentoEmail);
    }

    /**
     * Get handle
     *
     * @return string|null
     */
    public function getHandle()
    {
        return $this->_get(self::HANDLE);
    }

    /**
     * Set handle
     *
     * @param string $handle
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     */
    public function setHandle($handle)
    {
        return $this->setData(self::HANDLE, $handle);
    }
}
