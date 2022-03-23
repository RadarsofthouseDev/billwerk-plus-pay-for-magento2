<?php

namespace Radarsofthouse\Reepay\Client;

class ApiException extends \Exception
{

    /**
     * Index constructor
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
