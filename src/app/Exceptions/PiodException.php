<?php

namespace App\Exceptions;

use Exception;

class PiodException extends Exception
{

    protected $context;

    public function __construct($message, $context)
    {
        $this->context = $context;

        parent::__construct($message);
    }

    public function context()
    {
        return $this->context;
    }
}