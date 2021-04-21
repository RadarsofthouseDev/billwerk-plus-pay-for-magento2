<?php
/**
 * Copyright (c) 2021 radarsofthouse.dk
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Radarsofthouse\Reepay\Api\Data;

interface CustomerInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const CUSTOMER_ID = 'customer_id';
    const MAGENTO_CUSTOMER_ID = 'magento_customer_id';
    const MAGENTO_EMAIL = 'magento_email';
    const HANDLE = 'handle';

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get magento_customer_id
     * @return string|null
     */
    public function getMagentoCustomerId();

    /**
     * Set magento_customer_id
     * @param string $magentoCustomerId
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     */
    public function setMagentoCustomerId($magentoCustomerId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Radarsofthouse\Reepay\Api\Data\CustomerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Radarsofthouse\Reepay\Api\Data\CustomerExtensionInterface $extensionAttributes
    );

    /**
     * Get magento_email
     * @return string|null
     */
    public function getMagentoEmail();

    /**
     * Set magento_email
     * @param string $magentoEmail
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     */
    public function setMagentoEmail($magentoEmail);

    /**
     * Get handle
     * @return string|null
     */
    public function getHandle();

    /**
     * Set handle
     * @param string $handle
     * @return \Radarsofthouse\Reepay\Api\Data\CustomerInterface
     */
    public function setHandle($handle);
}
