<?php
 
namespace Radarsofthouse\Reepay\Model;

class Reepayapplepay extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    /**
     * @var string
     */
    protected $_code = 'reepay_applepay';

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
            return false;
        } elseif (stripos($user_agent, 'Safari') !== false) {
            return parent::isAvailable($quote);
        } else {
            return false;
        }
    }
}
