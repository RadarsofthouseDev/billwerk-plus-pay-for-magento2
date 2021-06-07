<?php
 
namespace Radarsofthouse\Reepay\Model;

/**
 * Class Reepaygooglepay
 *
 * @package Radarsofthouse\Reepay\Model
 */
class Reepaygooglepay extends \Radarsofthouse\Reepay\Model\Reepaypayment
{
    protected $_code = 'reepay_googlepay';

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null){
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if(stripos( $user_agent, 'Edg') !== false){
            return false;
        }elseif (stripos( $user_agent, 'Chrome') !== false){
            return parent::isAvailable($quote);   
        }else{
            return false;
        }
    }
}
