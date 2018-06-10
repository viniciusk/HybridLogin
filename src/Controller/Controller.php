<?php

namespace HybridLogin\Controller;


use HybridLogin\Container;
use HybridLogin\Error\ErrorMessagesInterface;
use HybridLogin\User\UserController;

/**
 * Class Controller
 * @package HybridLogin\Controller
 */
class Controller
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
     * @var ContextControllerInterface $controller the context controller
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
     * @return ContextControllerInterface
     */
    public function getEffectiveController(): ContextControllerInterface
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
     * @param bool $outputBodyOnly
     */
    public function run(bool $outputBodyOnly = false): void
    {
        $action = $this->getAction();
        if (method_exists($this->controller, $action)) {
            $this->controller->$action();
            $this->finish($outputBodyOnly);
            if ($outputBodyOnly) {
                return;
            }
        }
        $this->response->setStatus(Response::RESPONSE_404_NOT_FOUND);
        $this->finish($outputBodyOnly);
    }


    /**
     * Terminates the execution through the Response
     * @param bool $outputBodyOnly
     */
    public function finish(bool $outputBodyOnly = false): void
    {
        $this->response->finish($outputBodyOnly);
    }


    /**
     * Adds an error and terminates the execution
     * @param bool $outputBodyOnly
     */
    public function finishWithInvalidRequestError(bool $outputBodyOnly = false): void
    {
        $this->response->addError(ErrorMessagesInterface::INVALID_REQUEST);
        $this->response->setStatus(Response::RESPONSE_400_BAD_REQUEST);
        $this->finish($outputBodyOnly);
    }


    /**
     * @return ContextControllerInterface
     */
    private function userController(): ContextControllerInterface
    {
        return new UserController($this, $this->container->getUserService());
    }
}
