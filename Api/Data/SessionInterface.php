<?php
declare(strict_types=1);

namespace Radarsofthouse\Reepay\Api\Data;

interface SessionInterface
{

    const HANDLE = 'handle';
    const CHARGE_HANDLE = 'charge_handle';
    const ORDER_ID = 'order_id';
    const CREATED = 'created';
    const SESSION_ID = 'session_id';
    const ORDER_NUMBER = 'order_number';

    /**
     * Get session_id
     * @return string|null
     */
    public function getSessionId();

    /**
     * Set session_id
     * @param string $sessionId
     * @return \Radarsofthouse\Reepay\Session\Api\Data\SessionInterface
     */
    public function setSessionId($sessionId);

    /**
     * Get handle
     * @return string|null
     */
    public function getHandle();

    /**
     * Set handle
     * @param string $handle
     * @return \Radarsofthouse\Reepay\Session\Api\Data\SessionInterface
     */
    public function setHandle($handle);

    /**
     * Get charge_handle
     * @return string|null
     */
    public function getChargeHandle();

    /**
     * Set charge_handle
     * @param string $chargeHandle
     * @return \Radarsofthouse\Reepay\Session\Api\Data\SessionInterface
     */
    public function setChargeHandle($chargeHandle);

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $orderId
     * @return \Radarsofthouse\Reepay\Session\Api\Data\SessionInterface
     */
    public function setOrderId($orderId);

    /**
     * Get order_number
     * @return string|null
     */
    public function getOrderNumber();

    /**
     * Set order_number
     * @param string $orderNumber
     * @return \Radarsofthouse\Reepay\Session\Api\Data\SessionInterface
     */
    public function setOrderNumber($orderNumber);

    /**
     * Get created
     * @return string|null
     */
    public function getCreated();

    /**
     * Set created
     * @param string $created
     * @return \Radarsofthouse\Reepay\Session\Api\Data\SessionInterface
     */
    public function setCreated($created);
}
