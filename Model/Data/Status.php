<?php

namespace Radarsofthouse\Reepay\Model\Data;

use Radarsofthouse\Reepay\Api\Data\StatusInterface;

class Status extends \Magento\Framework\Api\AbstractExtensibleObject implements StatusInterface
{

    /**
     * Get status_id
     *
     * @return string|null
     */
    public function getStatusId()
    {
        return $this->_get(self::STATUS_ID);
    }

    /**
     * Set status_id
     *
     * @param string $statusId
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setStatusId($statusId)
    {
        return $this->setData(self::STATUS_ID, $statusId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Radarsofthouse\Reepay\Api\Data\StatusExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param \Radarsofthouse\Reepay\Api\Data\StatusExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Radarsofthouse\Reepay\Api\Data\StatusExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get order_id
     *
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->_get(self::ORDER_ID);
    }

    /**
     * Set order_id
     *
     * @param string $orderId
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     *
     * @param string $status
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get first_name
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->_get(self::FIRST_NAME);
    }

    /**
     * Set first_name
     *
     * @param string $firstName
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setFirstName($firstName)
    {
        return $this->setData(self::FIRST_NAME, $firstName);
    }

    /**
     * Get last_name
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this->_get(self::LAST_NAME);
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setLastName($lastName)
    {
        return $this->setData(self::LAST_NAME, $lastName);
    }

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * Set email
     *
     * @param string $email
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Get token
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->_get(self::TOKEN);
    }

    /**
     * Set token
     *
     * @param string $token
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setToken($token)
    {
        return $this->setData(self::TOKEN, $token);
    }

    /**
     * Get masked_card_number
     *
     * @return string|null
     */
    public function getMaskedCardNumber()
    {
        return $this->_get(self::MASKED_CARD_NUMBER);
    }

    /**
     * Set masked_card_number
     *
     * @param string $maskedCardNumber
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setMaskedCardNumber($maskedCardNumber)
    {
        return $this->setData(self::MASKED_CARD_NUMBER, $maskedCardNumber);
    }

    /**
     * Get fingerprint
     *
     * @return string|null
     */
    public function getFingerprint()
    {
        return $this->_get(self::FINGERPRINT);
    }

    /**
     * Set fingerprint
     *
     * @param string $fingerprint
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setFingerprint($fingerprint)
    {
        return $this->setData(self::FINGERPRINT, $fingerprint);
    }

    /**
     * Get card_type
     *
     * @return string|null
     */
    public function getCardType()
    {
        return $this->_get(self::CARD_TYPE);
    }

    /**
     * Set card_type
     *
     * @param string $cardType
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setCardType($cardType)
    {
        return $this->setData(self::CARD_TYPE, $cardType);
    }

    /**
     * Get error
     *
     * @return string|null
     */
    public function getError()
    {
        return $this->_get(self::ERROR);
    }

    /**
     * Set error
     *
     * @param string $error
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setError($error)
    {
        return $this->setData(self::ERROR, $error);
    }

    /**
     * Get error_state
     *
     * @return string|null
     */
    public function getErrorState()
    {
        return $this->_get(self::ERROR_STATE);
    }

    /**
     * Set error_state
     *
     * @param string $errorState
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setErrorState($errorState)
    {
        return $this->setData(self::ERROR_STATE, $errorState);
    }
}
