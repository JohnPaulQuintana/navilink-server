<?php

// app/Exceptions/ApiException.php
namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected $status;
    protected $errors;

    public function __construct($message, $status = 400, $errors = null)
    {
        parent::__construct($message);
        $this->status = $status;
        $this->errors = $errors;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}