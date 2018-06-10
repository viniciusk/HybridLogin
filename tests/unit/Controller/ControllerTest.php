<?php

use \PHPUnit\Framework\TestCase;
use \HybridLogin\Container;
use \HybridLogin\Controller\Controller;

class ControllerTest extends TestCase
{
    /**
     * @var \HybridLogin\Controller\Controller $controller
     */
    protected $controller;
    /**
     * @var \HybridLogin\Container $container
     */
    protected $container;

    public function setUp(): void
    {
        $this->container = new Container(new \HybridLogin\Model\Repository\FooRepositoryHandler());
        $this->controller = new Controller($this->container, ['route'=>'user', 'action'=>'isRegistered']);
    }

    public function testControllerInstance(): void
    {
        $this->assertInstanceOf(\HybridLogin\Controller\Controller::class, $this->controller);
    }

    public function testGetEffectiveUserController(): void
    {
        $this->assertInstanceOf(\HybridLogin\User\UserController::class, $this->controller->getEffectiveController());
    }

    public function testGetResponse(): void
    {
        $this->assertInstanceOf(\HybridLogin\Controller\Response::class, $this->controller->getResponse());
    }

    public function testControllerNotFound(): void
    {
        $this->controller = new HybridLogin\Controller\Controller($this->container, ['route' => 'notnotnot']);
        $this->assertEquals(\HybridLogin\Controller\Response::RESPONSE_404_NOT_FOUND, $this->controller->getResponse()->getStatus());
    }

    /*public function testGetEffectiveController(): void
    {
        $controller = new HybridLogin\Controller\Controller($this->container, ['route'=>'', 'action'=>'isRegistered']);
        $this->assertInstanceOf(\HybridLogin\Controller\Controller::class, $controller->getEffectiveController());
    }*/

    public function testControllerRoute(): void
    {
        $this->assertEquals('user', $this->controller->getRoute());
    }

    public function testControllerAction(): void
    {
        $this->assertEquals('isRegistered', $this->controller->getAction());
    }

    public function testControllerWithServerUri(): void
    {
        $_SERVER['REQUEST_URI'] = '/user/isRegistered/test';
        $this->controller = new HybridLogin\Controller\Controller($this->container);
        $this->assertEquals('user', $this->controller->getRoute());
        $this->assertEquals('isRegistered', $this->controller->getAction());
    }

    public function testRunNonexistentEmailRegistered(): void
    {
        $_REQUEST['email'] = 'foo@bar.com';
        $this->expectOutputString('{"success":true,"errors":[],"data":{"isRegistered":false}}');
        $this->controller->run(true);
    }

    public function testActionNotFound(): void
    {
        $controller = new Controller($this->container, ['route'=>'user', 'action'=>'notnotnot']);
        $controller->run(true);
        $this->assertEquals(\HybridLogin\Controller\Response::RESPONSE_404_NOT_FOUND, $controller->getResponse()->getStatus());
    }

    public function testFinishWithInvalidRequestError(): void
    {
        $this->controller->finishWithInvalidRequestError(true);
        $this->assertEquals(\HybridLogin\Controller\Response::RESPONSE_400_BAD_REQUEST, $this->controller->getResponse()->getStatus());
        $this->assertEquals(\HybridLogin\Error\ErrorMessagesInterface::INVALID_REQUEST, $this->controller->getResponse()->getLastError());
    }
}
