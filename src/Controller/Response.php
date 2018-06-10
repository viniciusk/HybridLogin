<?php

namespace HybridLogin\Controller;

use HybridLogin\Error\AbstractErrorHandler;

class Response extends AbstractErrorHandler
{
    public const RESPONSE_200_OK = '200 OK';
    public const RESPONSE_400_BAD_REQUEST = '400 Bad Request';
    public const RESPONSE_404_NOT_FOUND = '404 Not Found';

    /**
     * @var string $status
     */
    private $status;
    /**
     * @var bool determines whether the response is successful
     */
    protected $success = false;
    /**
     * @var array $data a set of information to follow with the response
     */
    protected $data = [];


    /**
     * @param string $node
     * @param $value
     */
    public function setNode(string $node, $value): void
    {
        $this->data[$node] = $value;
    }


    /**
     * @param string $node
     * @return mixed|null
     */
    public function getNode(string $node)
    {
        return $this->data[$node] ?? null;
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }


    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }


    /**
     * @return string
     */
    public function getStatus(): string
    {
        if (null !== $this->status) {
            return $this->status;
        }
        return !$this->hasError() ? self::RESPONSE_200_OK : self::RESPONSE_400_BAD_REQUEST;
    }


    /**
     * @return array
     */
    public function getResponseArray(): array
    {
        $responseArray = [];
        $responseArray['success'] = !$this->hasError();
        $responseArray['errors'] = $this->getErrors();
        $responseArray['data'] = $this->getData();
        //$responseArray['session'] = serialize($_SESSION);
        return $responseArray;
    }


    /**
     * @param bool $outputBodyOnly
     * @return void
     */
    public function finish(bool $outputBodyOnly = false): void
    {
        if (!$outputBodyOnly) {
            header("{$_SERVER['SERVER_PROTOCOL']} {$this->getStatus()}");
            header('Content-Type: application/json');
        }
        echo json_encode($this->getResponseArray());
        if (!$outputBodyOnly) {
            exit(0);
        }
    }

}
