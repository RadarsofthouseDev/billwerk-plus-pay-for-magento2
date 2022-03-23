<?php

namespace Radarsofthouse\Reepay\Api\Data;

interface StatusInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const MASKED_CARD_NUMBER = 'masked_card_number';
    const STATUS_ID = 'status_id';
    const LAST_NAME = 'last_name';
    const FINGERPRINT = 'fingerprint';
    const CARD_TYPE = 'card_type';
    const STATUS = 'status';
    const ERROR_STATE = 'error_state';
    const EMAIL = 'email';
    const ORDER_ID = 'order_id';
    const TOKEN = 'token';
    const FIRST_NAME = 'first_name';
    const ERROR = 'error';

    /**
     * Get status_id
     *
     * @return string|null
     */
    public function getStatusId();

    /**
     * Set status_id
     *
     * @param string $statusId
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setStatusId($statusId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Radarsofthouse\Reepay\Api\Data\StatusExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Radarsofthouse\Reepay\Api\Data\StatusExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Radarsofthouse\Reepay\Api\Data\StatusExtensionInterface $extensionAttributes
    );

    /**
     * Get order_id
     *
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     *
     * @param string $orderId
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setOrderId($orderId);

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setStatus($status);

    /**
     * Get first_name
     *
     * @return string|null
     */
    public function getFirstName();

    /**
     * Set first_name
     *
     * @param string $firstName
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setFirstName($firstName);

    /**
     * Get last_name
     *
     * @return string|null
     */
    public function getLastName();

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setLastName($lastName);

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param string $email
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setEmail($email);

    /**
     * Get token
     *
     * @return string|null
     */
    public function getToken();

    /**
     * Set token
     *
     * @param string $token
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setToken($token);

    /**
     * Get masked_card_number
     *
     * @return string|null
     */
    public function getMaskedCardNumber();

    /**
     * Set masked_card_number
     *
     * @param string $maskedCardNumber
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setMaskedCardNumber($maskedCardNumber);

    /**
     * Get fingerprint
     *
     * @return string|null
     */
    public function getFingerprint();

    /**
     * Set fingerprint
     *
     * @param string $fingerprint
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setFingerprint($fingerprint);

    /**
     * Get card_type
     *
     * @return string|null
     */
    public function getCardType();

    /**
     * Set card_type
     *
     * @param string $cardType
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setCardType($cardType);

    /**
     * Get error
     *
     * @return string|null
     */
    public function getError();

    /**
     * Set error
     *
     * @param string $error
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setError($error);

    /**
     * Get error_state
     *
     * @return string|null
     */
    public function getErrorState();

    /**
     * Set error_state
     *
     * @param string $errorState
     * @return \Radarsofthouse\Reepay\Api\Data\StatusInterface
     */
    public function setErrorState($errorState);
}
