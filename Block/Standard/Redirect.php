<?php

namespace Radarsofthouse\Reepay\Block\Standard;

class Redirect extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Radarsofthouse\Reepay\Helper\Invoice
     */
    private $invoice;

    /**
     * @var string
     */
    private $paymentTransactionId;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;

    /**
     * @var \Magento\Theme\Block\Html\Header\Logo
     */
    private $logo;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;
    
    /**
     * Index constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Radarsofthouse\Reepay\Helper\Invoice $invoice
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Theme\Block\Html\Header\Logo $logo
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Radarsofthouse\Reepay\Helper\Invoice $invoice,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\Framework\UrlInterface $urlInterface,
        array $data = []
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
     * Load order fron checkout session
     *
     * @return \Magento\Sales\Model\Order $order | boolean false
     */
    public function getOrder()
    {
        if ($this->checkoutSession->getLastRealOrderId()) {
            $order = $this->orderFactory->create()->loadByIncrementId(
                $this->checkoutSession->getLastRealOrderId()
            );

            return $order;
        }

        return false;
    }

    /**
     * Get website logo
     *
     * @return string
     */
    public function getLogoSrc()
    {
        return $this->logo->getLogoSrc();
    }

    /**
     * Get website logo Alt
     *
     * @return string
     */
    public function getLogoAlt()
    {
        return $this->logo->getLogoAlt();
    }

    /**
     * Set Payment Transaction Id
     *
     * @param string $paymentTransactionId
     */
    public function setPaymentTransactionId($paymentTransactionId)
    {
        $this->paymentTransactionId = $paymentTransactionId;
    }

    /**
     * Get Payment Transaction Id
     *
     * @return string
     */
    public function getPaymentTransactionId()
    {
        return $this->paymentTransactionId;
    }

    /**
     * Get payment seccess callback url
     *
     * @return string
     */
    public function getAcceptUrl()
    {
        return $this->urlInterface->getUrl('reepay/standard/accept');
    }

    /**
     * Get payment error callback url
     *
     * @return string
     */
    public function getErrorUrl()
    {
        return $this->urlInterface->getUrl('reepay/standard/error');
    }

    /**
     * Get payment cancel callback url
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->urlInterface->getUrl('reepay/standard/cancel');
    }
}
