<?php

use PHPUnit\Framework\TestCase;
use HybridLogin\Model\Repository\FooRepositoryHandler;

/**
 * Class FooRepositoryHandlerTest
 */
final class FooRepositoryHandlerTest extends TestCase
{
    /**
     * @var FooRepositoryHandler $fooHandler
     */
    protected $fooHandler;


    public function setUp()
    {
        $this->fooHandler = new FooRepositoryHandler();
    }


    public function testFooHandlerIsInstanceOfRepositoryHandlerInterface(): void
    {
        $this->assertInstanceOf(\HybridLogin\Model\Repository\RepositoryHandlerInterface::class, $this->fooHandler);
    }

    public function testSaveEmptyArray(): void
    {
        $this->assertFalse($this->fooHandler->save('fooEntity', []));
    }

    public function testSave(): void
    {
        $this->assertTrue($this->fooHandler->save('fooEntity', ['attr' => 'value']));
    }

    public function testFindOneThatDoesNotExist(): void
    {
        $this->assertNull($this->fooHandler->findOne('fooEntity', ['will-not' => 'find-me']));
    }

    public function testFindOneByAttributesThatIsNotSaved(): void
    {
        $entity = 'fooEntity';
        $this->fooHandler->save($entity, ['id'=>1,'name'=>'jon']);
        $this->fooHandler->save($entity, ['id'=>2,'name'=>'bob']);
        $this->fooHandler->save($entity, ['id'=>3,'name'=>'charlie']);
        $this->assertNull($this->fooHandler->findOne('fooEntity', ['id' => 2,'name'=>'ana']));
    }

    public function testFindOneByAttributes(): void
    {
        $entity = 'fooEntity';
        $this->fooHandler->save($entity, ['id'=>1,'name'=>'jon']);
        $this->fooHandler->save($entity, ['id'=>2,'name'=>'ana']);
        $this->fooHandler->save($entity, ['id'=>3,'name'=>'charlie']);
        $this->assertEquals(['id'=>2,'name'=>'ana'], $this->fooHandler->findOne('fooEntity', ['id' => 2,'name'=>'ana']));
    }

    public function testFindOneById(): void
    {
        $entity = 'fooEntity';
        $this->fooHandler->save($entity, ['id'=>1,'name'=>'jon']);
        $this->fooHandler->save($entity, ['id'=>2,'name'=>'ana']);
        $this->fooHandler->save($entity, ['id'=>3,'name'=>'charlie']);
        $this->assertEquals(['id'=>2,'name'=>'ana'], $this->fooHandler->findOne('fooEntity', ['id' => 2]));
    }

}
