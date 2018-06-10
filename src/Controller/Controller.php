<?php

namespace HybridLogin\Controller;


use HybridLogin\Container;
use HybridLogin\Error\ErrorMessagesInterface;
use HybridLogin\User\UserController;

class Controller extends AbstractController
{
    /**
     * @var array $params
     */
    private $params;
    /**
     * @var string $route
     */
    private $route;
    /**
     * @var string $action
     */
    private $action;
    /**
     * @var Container $container
     */
    private $container;
    /**
     * @var AbstractController $controller the context controller
     */
    private $controller;
    /**
     * @var Response $response
     */
    private $response;


    /**
     * Controller constructor.
     * @param Container $container
     * @param array|null $params
     */
    public function __construct(Container $container, ?array $params = null)
    {
        $this->controller = $this;
        $this->container = $container;
        $this->response = new Response();
        $this->resolveParams($params);
        if (null === $this->getRoute()) {
            $this->finishWithInvalidRequestError();
        }
        $routeController = "{$this->getRoute()}Controller";
        if (method_exists($this, $routeController)) {
            $this->controller = $this->$routeController();
            return;
        }
        $this->response->setStatus(Response::RESPONSE_404_NOT_FOUND);
    }


    /**
     * @return AbstractController
     */
    public function getEffectiveController(): AbstractController
    {
        return $this->controller;
    }


    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }


    /**
     * @param null|array $params
     */
    private function resolveParams(?array $params = null): void
    {
        $this->params = $params;
        if (!empty($this->params['route'])) {
            $this->route = $this->params['route'];
        } else {
            $this->route = $this->getURIParam(1);
        }
        if (!empty($this->params['action'])) {
            $this->action = $this->params['action'];
        } else {
            $this->action = $this->getURIParam(2);
        }
    }


    /**
     * @return null|string
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }


    /**
     * @return null|string
     */
    public function getAction(): ?string
    {
        return $this->action;
    }


    /**
     * @param int $position
     * @return null|string
     */
    private function getURIParam(int $position): ?string
    {
        $requestURIArray = explode('?', $_SERVER['REQUEST_URI'] ?? '');
        $requestURIArray = explode('/', $requestURIArray[0] ?? '');
        return $requestURIArray[$position] ?? null;
    }


    /**
     * Runs the expected action
     */
    public function run(): void
    {
        $action = $this->getAction();
        if (method_exists($this->controller, $action)) {
            $this->controller->$action();
            $this->finish();
        }
        $this->response->setStatus(Response::RESPONSE_404_NOT_FOUND);
        $this->finish();
    }


    /**
     * Terminates the execution through the Response
     */
    public function finish(): void
    {
        $this->response->finish();
    }


    /**
     * Adds an error and terminates the execution
     */
    private function finishWithInvalidRequestError(): void
    {
        $this->response->addError(ErrorMessagesInterface::INVALID_REQUEST);
        $this->finish();
    }


    /**
     * @return AbstractController
     */
    private function userController(): AbstractController
    {
        return new UserController($this, $this->container->getUserService());
    }
}
