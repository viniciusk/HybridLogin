<?php

namespace HybridLogin\Error;


interface ErrorHandlerInterface
{
    public function clearErrors(): void;

    public function addError(string $message): ErrorHandlerInterface;

    public function addErrors(array $errors): ErrorHandlerInterface;

    public function getErrors(): array;

    public function getLastError(): ?string;

    public function hasError(): bool;
}
