<?php

namespace Radarsofthouse\Reepay\Api\Data;

interface CustomerInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const CUSTOMER_ID = 'customer_id';
    const MAGENTO_CUSTOMER_ID = 'magento_customer_id';
    const MAGENTO_EMAIL = 'magento_email';
    const HANDLE = 'handle';

    /**
     * Get customer_id
     *
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     *
     * @param string $customerId
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get magento_customer_id
     *
     * @return string|null
     */
    public function getMagentoCustomerId();

    /**
     * Set magento_customer_id
     *
     * @param string $magentoCustomerId
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     */
    public function setMagentoCustomerId($magentoCustomerId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Radarsofthouse\Reepay\Api\Data\CustomerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Radarsofthouse\Reepay\Api\Data\CustomerExtensionInterface $extensionAttributes
    );

    /**
     * Get magento_email
     *
     * @return string|null
     */
    public function getMagentoEmail();

    /**
     * Set magento_email
     *
     * @param string $magentoEmail
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     */
    public function setMagentoEmail($magentoEmail);

    /**
     * Get handle
     *
     * @return string|null
     */
    public function getHandle();

    /**
     * Set handle
     *
     * @param string $handle
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     */
    public function setHandle($handle);
}
