<?php

namespace HybridLogin\Error;


/**
 * Trait ErrorHandlerTrait
 * @package HybridLogin\Error
 */
trait ErrorHandlerTrait
{
    /**
     * @var array $errors
     */
    private $errors = [];


    /**
     * @param string $message
     * @return self
     */
    public function addError(string $message): self
    {
        $this->errors[] = $message;
        return $this;
    }


    /**
     * @param array $errors
     * @return self
     */
    public function addErrors(array $errors): self
    {
        foreach ($errors as $error) {
            $this->addError($error);
        }
        return $this;
    }


    /**
     * Clear errors
     */
    public function clearErrors(): void
    {
        $this->errors = [];
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
