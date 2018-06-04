<?php

namespace HybridLogin;


use HybridLogin\Error\ErrorHandler;
use HybridLogin\Error\ErrorHandlerInterface;
use HybridLogin\Model\Repository\PDORepositoryHandler;
use HybridLogin\Model\Repository\RepositoryHandlerInterface;
use HybridLogin\User\UserRepository;
use HybridLogin\User\UserService;

/**
 * Class Container
 * @package HybridLogin
 */
class Container
{
    /**
     * @var ErrorHandlerInterface $errorHandler
     */
    private $errorHandler;
    /**
     * @var RepositoryHandlerInterface $repositoryHandler
     */
    private $repositoryHandler;
    /**
     * @var UserService $userService
     */
    private $userService;


    /**
     * @return ErrorHandlerInterface
     */
    public function getErrorHandler(): ErrorHandlerInterface
    {
        if ($this->errorHandler === null) {
            $this->errorHandler = new ErrorHandler();
        }

        return $this->errorHandler;
    }


    /**
     * @return RepositoryHandlerInterface
     */
    public function getRepositoryHandler(): RepositoryHandlerInterface
    {
        if ($this->repositoryHandler === null) {
            $this->repositoryHandler = new PDORepositoryHandler();
        }

        return $this->repositoryHandler;
    }


    /**
     * @return UserService
     */
    public function getUserService(): UserService
    {
        if ($this->userService === null) {
            $this->userService = new UserService($this->getErrorHandler(), new UserRepository($this->getRepositoryHandler()));
        }

        return $this->userService;
    }
}
