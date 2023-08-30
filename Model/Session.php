<?php
declare(strict_types=1);

namespace Radarsofthouse\Reepay\Model;

use Magento\Framework\Model\AbstractModel;
use Radarsofthouse\Reepay\Api\Data\SessionInterface;

class Session extends AbstractModel implements SessionInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Radarsofthouse\Reepay\Model\ResourceModel\Session::class);
    }

    /**
     * @inheritDoc
     */
    public function getSessionId()
    {
        return $this->getData(self::SESSION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSessionId($sessionId)
    {
        return $this->setData(self::SESSION_ID, $sessionId);
    }

    /**
     * @inheritDoc
     */
    public function getHandle()
    {
        return $this->getData(self::HANDLE);
    }

    /**
     * @inheritDoc
     */
    public function setHandle($handle)
    {
        return $this->setData(self::HANDLE, $handle);
    }

    /**
     * @inheritDoc
     */
    public function getChargeHandle()
    {
        return $this->getData(self::CHARGE_HANDLE);
    }

    /**
     * @inheritDoc
     */
    public function setChargeHandle($chargeHandle)
    {
        return $this->setData(self::CHARGE_HANDLE, $chargeHandle);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function getOrderNumber()
    {
        return $this->getData(self::ORDER_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setOrderNumber($orderNumber)
    {
        return $this->setData(self::ORDER_NUMBER, $orderNumber);
    }

    /**
     * @inheritDoc
     */
    public function getCreated()
    {
        return $this->getData(self::CREATED);
    }

    /**
     * @inheritDoc
     */
    public function setCreated($created)
    {
        return $this->setData(self::CREATED, $created);
    }
}
