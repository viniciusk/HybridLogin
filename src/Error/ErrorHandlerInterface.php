<?php

namespace HybridLogin\Error;


interface ErrorHandlerInterface
{
    public function clearErrors(): void;

    public function addError(string $message);

    public function addErrors(array $errors);

    public function getErrors(): array;

    public function getLastError(): ?string;

    public function hasError(): bool;
}
