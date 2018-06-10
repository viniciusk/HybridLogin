<?php

use PHPUnit\Framework\TestCase;
use HybridLogin\Error\ErrorHandler;


/**
 * Class ErrorHandlerTest
 */
final class ErrorHandlerTest extends TestCase
{
    /**
     * @var ErrorHandler $controller
     */
    protected $errorHandler;

    public function setUp()
    {
        $container = new \HybridLogin\Container(new \HybridLogin\Model\Repository\FooRepositoryHandler());
        $this->errorHandler = $container->getErrorHandler();
    }


    public function testControllerIsInstanceOfUserController(): void
    {
        $this->assertInstanceOf(ErrorHandler::class, $this->errorHandler);
    }

    public function testHasErrorInitialState(): void
    {
        $this->assertFalse($this->errorHandler->hasError());
    }

    public function testAddError(): void
    {
        $this->errorHandler->addError('My error');
        $this->assertTrue($this->errorHandler->hasError());
    }

    public function testAddErrors(): void
    {
        $this->assertFalse($this->errorHandler->hasError());
        $errorsArray = ['My error', 'my second error'];
        $this->errorHandler->addErrors($errorsArray);
        $this->assertTrue($this->errorHandler->hasError());
        $this->assertEquals($errorsArray, $this->errorHandler->getErrors());
    }

    public function testGetLastError(): void
    {
        $errorsArray = ['My error', 'my second error'];
        $lastError = 'last error';
        $this->errorHandler->addErrors($errorsArray);
        $this->errorHandler->addError($lastError);
        $this->assertEquals($lastError, $this->errorHandler->getLastError());
    }

    public function testGetNonexistentLastError(): void
    {
        $this->assertNull($this->errorHandler->getLastError());
    }

    public function testClearErrors(): void
    {
        $this->errorHandler->addError('error test');
        $this->errorHandler->clearErrors();
        $this->assertFalse($this->errorHandler->hasError());
        $this->assertEmpty($this->errorHandler->getErrors());
    }

}
