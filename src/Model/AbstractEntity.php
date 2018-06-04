<?php

namespace HybridLogin\Model;


use HybridLogin\Error\ErrorHandlerInterface;

/**
 * Class AbstractEntity
 * @package HybridLogin\Model
 */
abstract class AbstractEntity
{
    /**
     * @var int|null $uuid the unique identifier the this entity
     */
    protected $uuid;

    /**
     * @var ErrorHandlerInterface $errorHandler
     */
    protected $errorHandler;


    public function __construct(ErrorHandlerInterface $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }


    /**
     * @return int|null
     */
    public function getUUID(): ?int
    {
        return $this->uuid;
    }


    /**
     * @param int $uuid
     */
    public function setUUID(?int $uuid): void
    {
        $this->uuid = $uuid;
    }


    /**
     * @return array|null
     */
    public function getErrors(): ?array
    {
        return $this->errorHandler->getErrors();
    }


    /**
     * @return array
     */
    abstract public function getAttributes(): array;


    /**
     * @return bool
     */
    abstract public function validate(): bool;


    /**
     * @return bool
     */
    abstract public function isNew(): bool;
}
