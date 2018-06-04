<?php

namespace HybridLogin\Model;


use HybridLogin\Error\ErrorHandlerInterface;
use HybridLogin\Model\Repository\RepositoryInterface;


/**
 * Class AbstractService
 * @package HybridLogin\Model
 *
 * @property ErrorHandlerInterface $errorHandler
 * @property RepositoryInterface $repository
 */
abstract class AbstractService
{
    /**
     * @var ErrorHandlerInterface $errorHandler
     */
    protected $errorHandler;
    /**
     * @var RepositoryInterface $repository
     */
    protected $repository;


    /**
     * AbstractService constructor.
     * @param ErrorHandlerInterface $errorHandler
     * @param RepositoryInterface $repository
     */
    public function __construct(ErrorHandlerInterface $errorHandler, RepositoryInterface $repository)
    {
        $this->errorHandler = $errorHandler;
        $this->repository = $repository;
    }


    /**
     * @return AbstractEntity
     */
    abstract public function create(): AbstractEntity;


    /**
     * @param array $data
     * @param bool $fromRepository
     * @return AbstractEntity
     */
    abstract public function createFromArray(array $data, bool $fromRepository): AbstractEntity;


    /**
     * @return ErrorHandlerInterface
     */
    public function getErrorHandler(): ErrorHandlerInterface
    {
        return $this->errorHandler;
    }


    /**
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }
}
