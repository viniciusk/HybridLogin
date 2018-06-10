<?php

use \PHPUnit\Framework\TestCase;
use \HybridLogin\Controller\Response;

class ResponseTest extends TestCase
{
    /**
     * @var Response $response
     */
    protected $response;


    function setUp()
    {
        $this->response = new Response();
    }

    public function testInstanceOfResponse(): void
    {
        $this->assertInstanceOf(Response::class, $this->response);
    }

    public function testSetNode(): void
    {
        $this->response->setNode('node', 'value');
        $this->assertEquals('value', $this->response->getNode('node'));
    }

    public function testGetNonexistentNode(): void
    {
        $this->assertNull($this->response->getNode('node'));
    }

    public function testGetData(): void
    {
        $data = [
            'node1' => 'value 1',
            'node2' => 'value 2',
        ];
        foreach ($data as $node => $value) {
            $this->response->setNode($node, $value);
        }
        $this->assertEquals($data, $this->response->getData());
    }

    public function testGetResponseArray(): void
    {
        $data = [
            'node1' => 'value 1',
            'node2' => 'value 2',
        ];
        foreach ($data as $node => $value) {
            $this->response->setNode($node, $value);
        }
        $this->assertEquals([
            'success' => true,
            'errors' => [],
            'data' => $data
        ], $this->response->getResponseArray());
    }

    public function testSuccessStatus(): void
    {
        $this->assertEquals(Response::RESPONSE_200_OK, $this->response->getStatus());
    }

    public function testErrorStatus(): void
    {
        $this->response->addError('Foo error');
        $this->assertEquals(Response::RESPONSE_400_BAD_REQUEST, $this->response->getStatus());
    }

    public function testSetStatus(): void
    {
        $this->response->setStatus(Response::RESPONSE_404_NOT_FOUND);
        $this->assertEquals(Response::RESPONSE_404_NOT_FOUND, $this->response->getStatus());
    }

    public function testFinish(): void
    {
        $data = [
            'node1' => 'value 1',
            'node2' => 'value 2',
        ];
        foreach ($data as $node => $value) {
            $this->response->setNode($node, $value);
        }

        $this->expectOutputString(
            json_encode($this->response->getResponseArray())
        );

        $this->response->finish(true);
    }
}
