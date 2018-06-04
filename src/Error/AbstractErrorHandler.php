<?php

namespace HybridLogin\Error;


/**
 * Class ErrorHandler
 * @package HybridLogin
 *
 * @property array $errors
 */
abstract class AbstractErrorHandler implements ErrorHandlerInterface
{
    private $errors = [];


    /**
     * Clear errors
     */
    public function clearErrors(): void
    {
        $this->errors = [];
    }


    /**
     * @param string $message
     * @return ErrorHandlerInterface
     */
    public function addError(string $message): ErrorHandlerInterface
    {
        $this->errors[] = $message;
        return $this;
    }


    /**
     * @param array $errors
     * @return ErrorHandlerInterface
     */
    public function addErrors(array $errors): ErrorHandlerInterface
    {
        foreach ($errors as $error) {
            $this->addError($error);
        }
        return $this;
    }


    /**
     * @return null|string
     */
    public function getLastError(): ?string
    {
        if (!$this->hasError()) {
            return null;
        }
        return $this->errors[\count($this->errors)-1];
    }


    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }


    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return !empty($this->errors);
    }
}
