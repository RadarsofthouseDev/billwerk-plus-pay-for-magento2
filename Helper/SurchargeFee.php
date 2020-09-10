<?php

namespace Radarsofthouse\Reepay\Helper;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;

/**
 * Class SurchargeFee
 *
 * @package Radarsofthouse\Reepay\Helper
 */
class SurchargeFee extends AbstractHelper
{
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;
    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Email
     */
    private $email;

    /**
     * constructor.
     *
     * @param Context $context
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        InvoiceRepositoryInterface $invoiceRepository,
        CreditmemoRepositoryInterface $creditmemoRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Email $email,
        Logger $logger
    ) {
        parent::__construct($context);
        $this->objectManager = ObjectManager::getInstance();
        $this->creditmemoRepository = $creditmemoRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->email = $email;
        $this->logger = $logger;
    }

    /**
     * @param $quoteId
     * @return mixed
     */
    private function loadQuote($quoteId)
    {
        return $this->objectManager->create(\Magento\Quote\Model\Quote::class)->load($quoteId);
    }

    /**
     * @param $orderId
     * @param $charge
     */
    public function updateFeeToOrder($orderIncrementId, $charge)
    {
        try {
            $this->logger->addDebug(__METHOD__);
            /** @var  \Magento\Sales\Model\Order $order */
            $order = $this->objectManager->create(\Magento\Sales\Model\Order::class)->loadByIncrementId($orderIncrementId);
            /** @var  \Magento\Quote\Model\Quote $quote */
//            $quote = $this->loadQuote($order->getQuoteId());
            $quote = $this->objectManager->create(\Magento\Quote\Model\Quote::class)->load($order->getQuoteId());
            if (array_key_exists('source', $charge) && array_key_exists('surcharge_fee', $charge['source']) && $charge['source']['surcharge_fee'] > 0) {
                $surchargeFee = (float)($charge['source']['surcharge_fee'] / 100);
                $quote->setReepaySurchargeFee($surchargeFee);
                $quote->setTotalsCollectedFlag(false)->collectTotals();
                $quote->save();
                $order->setReepaySurchargeFee($surchargeFee)
                    ->setSubtotal($quote->getSubtotal())
                    ->setBaseSubtotal($quote->getBaseSubtotal())
                    ->setGrandTotal($quote->getGrandTotal())
                    ->setBaseGrandTotal($quote->getBaseGrandTotal());
                $order->save();
                $this->logger->addDebug('updateFeeToOrder already.');
            }
        } catch (\Exception $exception) {
            $this->logger->addError($exception->getMessage());
        }
        $this->email->sendEmail($orderIncrementId);
    }

    /**
     * Get Invoice is captured surcharge fee
     *
     * @param $orderId
     * @return bool
     */
    public function isInvoicedSurchargeFee($orderId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('order_id', $orderId)->create();
        try {
            $invoiceObject = $this->invoiceRepository->getList($searchCriteria);
            $invoiceRecords = $invoiceObject->getItems();
            foreach ($invoiceRecords as $invoiceRecord) {
                if ($invoiceRecord->getReepaySurchargeFee() > 0) {
                    return true;
                }
            }
        } catch (LocalizedException $localizedException) {
            $this->logger->addError($localizedException->getMessage());
        }
        return false;
    }

    /**
     * @param $orderId
     * @return float
     */
    public function getAvailableSurchargeFeeRefundAmount($orderId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('order_id', $orderId)->create();
        try {
            $invoiceObject = $this->invoiceRepository->getList($searchCriteria);
            $invoiceRecords = $invoiceObject->getItems();
            $invoiced = 0;
            foreach ($invoiceRecords as $invoice) {
                if (!empty($invoice->getReepaySurchargeFee())) {
                    $invoiced += $invoice->getReepaySurchargeFee();
                }
            }

            $creditmemoObject = $this->creditmemoRepository->getList($searchCriteria);
            $creditmemoRecords = $creditmemoObject->getItems();
            $refunded = 0;
            foreach ($creditmemoRecords as $creditmemo) {
                if (!empty($creditmemo->getReepaySurchargeFee())) {
                    $refunded += $creditmemo->getReepaySurchargeFee();
                }
            }
            return round($invoiced - $refunded, 2);
        } catch (LocalizedException $localizedException) {
            $this->logger->addError($localizedException->getMessage());
        }
    }
}
