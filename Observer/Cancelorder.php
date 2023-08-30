<?php

namespace Radarsofthouse\Reepay\Observer;

class Cancelorder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Radarsofthouse\Reepay\Helper\Charge
     */
    private $reepayCharge;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Session
     */
    private $reepaySession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Data
     */
    protected $reepayHelper;

    /**
     * @var \Radarsofthouse\Reepay\Api\SessionRepositoryInterface
     */
    protected $reepaySessionRepository;

    /**
     * @var \Radarsofthouse\Reepay\Helper\Logger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param \Radarsofthouse\Reepay\Helper\Charge $reepayCharge
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Radarsofthouse\Reepay\Helper\Session $reepaySession
     * @param \Radarsofthouse\Reepay\Helper\Data $reepayHelper
     * @param \Radarsofthouse\Reepay\Api\SessionRepositoryInterface $reepaySessionRepository
     * @param \Radarsofthouse\Reepay\Helper\Logger $logger
     */
    public function __construct(
        \Radarsofthouse\Reepay\Helper\Charge $reepayCharge,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Radarsofthouse\Reepay\Helper\Session $reepaySession,
        \Radarsofthouse\Reepay\Helper\Data $reepayHelper,
        \Radarsofthouse\Reepay\Api\SessionRepositoryInterface $reepaySessionRepository,
        \Radarsofthouse\Reepay\Helper\Logger $logger
    ) {
        $this->reepayCharge = $reepayCharge;
        $this->scopeConfig = $scopeConfig;
        $this->reepaySession = $reepaySession;
        $this->reepayHelper = $reepayHelper;
        $this->reepaySessionRepository = $reepaySessionRepository;
        $this->logger = $logger;
    }

    /**
     * Observe order_cancel_after
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData('order');

        $paymentMethod = $order->getPayment()->getMethodInstance()->getCode();
        if ($this->reepayHelper->isReepayPaymentMethod($paymentMethod)) {
            $this->logger->addDebug(__METHOD__, ['order ID : ' . $order->getIncrementId()]);

            $apiKey = $this->reepayHelper->getApiKey($order->getStoreId());

            $charge = $this->reepayCharge->get(
                $apiKey,
                $order->getIncrementId()
            );

            if ($charge !== false && $charge['state'] == 'created') {
                $charge = $this->reepayCharge->delete(
                    $apiKey,
                    $order->getIncrementId()
                );

                $this->logger->addDebug("Delete charge for order #".$order->getIncrementId());
            } elseif ($charge !== false && $charge['state'] == 'failed') {
                try {
                    $reepaySessions = $this->reepaySessionRepository->getListByOrderNumber($order->getIncrementId());
                    if($reepaySessions->getTotalCount() > 0){
                        /** @var \Radarsofthouse\Reepay\Api\Data\SessionInterface $reepaySession */
                        foreach ($reepaySessions->getItems() as $reepaySession) {
                            $this->reepaySession->delete($apiKey, $reepaySession->getHandle());
                            $this->logger->addDebug("Delete session # {$reepaySession->getHandle()} for order #".$order->getIncrementId());
                        }
                    }
                } catch (\Magento\Framework\Exception\LocalizedException | \Exception $e) {
                    $this->logger->addError("Delete session for order #".$order->getIncrementId() . ' has errors  '. $e->getMessage());
                }

                $this->logger->addDebug("Delete session for order #".$order->getIncrementId());
            } else {
                $cancelRes = $this->reepayCharge->cancel(
                    $apiKey,
                    $order->getIncrementId()
                );

                $this->logger->addDebug("Cancel charge for order #".$order->getIncrementId());

                if (!empty($cancelRes)) {
                    if ($cancelRes['state'] == 'cancelled') {
                        $_payment = $order->getPayment();
                        $this->reepayHelper->setReepayPaymentState($_payment, 'cancelled');

                        // delete reepay session
                        $sessionRes = $this->reepaySession->delete(
                            $apiKey,
                            $order->getIncrementId()
                        );
                    }
                }
            }
        }

        return $this;
    }
}
