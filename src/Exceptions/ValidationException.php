<?php

namespace YourNamespace\MyOnlineStore\Exceptions;

class ValidationException extends \Exception
{
    protected $errors;

    public function __construct(string $message, array $errors = [])
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
} 