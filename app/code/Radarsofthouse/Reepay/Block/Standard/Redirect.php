<?php

namespace Radarsofthouse\Reepay\Block\Standard;

/**
 * Class Redirect
 *
 * @package Radarsofthouse\Reepay\Block\Standard
 */
class Redirect extends \Magento\Framework\View\Element\Template
{
    private $invoice;
    private $paymentTransactionId;
    private $checkoutSession;
    private $orderFactory;
    private $logo;
    protected $scopeConfig;
    protected $urlInterface;
    
    /**
     * Index constructor.
     *
     * @param array $data
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Radarsofthouse\Reepay\Helper\Invoice $invoice
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Theme\Block\Html\Header\Logo $logo
     * @param \Magento\Framework\UrlInterface $urlInterface
     */
    public function __construct(
        array $data = [],
        \Magento\Framework\View\Element\Template\Context $context,
        \Radarsofthouse\Reepay\Helper\Invoice $invoice,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->invoice = $invoice;
        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        $this->logo = $logo;
        $this->scopeConfig = $context->getScopeConfig();
        $this->urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }

    /**
     * load order fron checkout session
     *
     * @return \Magento\Sales\Model\Order $order | boolean false
     */
    public function getOrder()
    {
        if ($this->checkoutSession->getLastRealOrderId()) {
            $order = $this->orderFactory->create()->loadByIncrementId($this->checkoutSession->getLastRealOrderId());

            return $order;
        }

        return false;
    }

    /**
     * get website logo
     *
     * @return string
     */
    public function getLogoSrc()
    {
        return $this->logo->getLogoSrc();
    }

    /**
     * get website logo Alt
     *
     * @return string
     */
    public function getLogoAlt()
    {
        return $this->logo->getLogoAlt();
    }

    /**
     * set Payment Transaction Id
     *
     * @param string
     */
    public function setPaymentTransactionId($paymentTransactionId)
    {
        $this->paymentTransactionId = $paymentTransactionId;
    }

    /**
     * get Payment Transaction Id
     *
     * @return string
     */
    public function getPaymentTransactionId()
    {
        return $this->paymentTransactionId;
    }

    /**
     * get payment seccess callback url
     *
     * @return string
     */
    public function getAcceptUrl()
    {
        return $this->urlInterface->getUrl('reepay/standard/accept');
    }

    /**
     * get payment error callback url
     *
     * @return string
     */
    public function getErrorUrl()
    {
        return $this->urlInterface->getUrl('reepay/standard/error');
    }

    /**
     * get payment cancel callback url
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->urlInterface->getUrl('reepay/standard/cancel');
    }
}
