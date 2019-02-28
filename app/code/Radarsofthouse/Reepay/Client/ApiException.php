<?php

namespace Radarsofthouse\Reepay\Client;

/**
 * Class ApiException
 *
 * @package Radarsofthouse\Reepay\Client
 */
class ApiException extends \Exception
{

    /**
     * Index constructor
     *
     * @param string
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
