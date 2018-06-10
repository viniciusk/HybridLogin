<?php

use \PHPUnit\Framework\TestCase;

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
        $this->container = new \HybridLogin\Container(new \HybridLogin\Model\Repository\FooRepositoryHandler());
        $this->controller = new HybridLogin\Controller\Controller($this->container, ['route'=>'user', 'action'=>'isRegistered']);
    }

    public function testControllerInstance(): void
    {
        $this->assertInstanceOf(\HybridLogin\Controller\Controller::class, $this->controller);
    }

    public function testGetEffectiveUserController(): void
    {
        $this->assertInstanceOf(\HybridLogin\User\UserController::class, $this->controller->getEffectiveController());
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
}
