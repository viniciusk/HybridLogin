<?php

namespace HybridLogin\Controller;


use HybridLogin\Container;
use HybridLogin\Error\ErrorMessagesInterface;
use HybridLogin\User\UserController;

class Controller extends AbstractController
{
    /**
     * @var Container $container
     */
    private $container;

    /**
     * @var AbstractController $controller the context controller
     */
    private $controller;


    /**
     * Controller constructor.
     * @param Container $container
     * @param null|string $route
     */
    public function __construct(Container $container, ?string $route)
    {
        $this->controller = $this;
        $this->container = $container;
        if (null === $route) {
            $this->invalidRequest();
        }
        $routeController = "{$route}Controller";
        if (method_exists($this, $routeController)) {
            $this->controller = $this->$routeController();
            return;
        }
        $this->invalidRequest();
    }


    /**
     * @param null|string $action
     */
    public function run(?string $action): void
    {
        $action = $action ?? 'index';
        if (method_exists($this->controller, $action)) {
            echo $this->controller->$action();
            exit;
        }
        $this->invalidRequest();
    }


    /**
     * @return string
     */
    private function invalidRequest(): string
    {
        $response = new Response();
        $response->addError(ErrorMessagesInterface::INVALID_REQUEST);
        echo $this->formatResponse($response);
        exit;
    }


    /**
     * @return AbstractController
     */
    private function userController(): AbstractController
    {
        return new UserController($this->container->getUserService());
    }
}
