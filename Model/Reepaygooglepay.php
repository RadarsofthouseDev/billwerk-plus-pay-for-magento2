<?php
 
namespace Radarsofthouse\Reepay\Model;

class Reepaygooglepay extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_googlepay';

    /**
     * Check is available
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return boolean
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (stripos($user_agent, 'Edg') !== false) {
            return false;
        } elseif (stripos($user_agent, 'Chrome') !== false) {
            return parent::isAvailable($quote);
        } else {
            return false;
        }
    }
}
