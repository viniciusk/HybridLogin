<?php

namespace HybridLogin\Controller;


use HybridLogin\Error\AbstractErrorHandler;

class Response extends AbstractErrorHandler
{
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
    public function addData(string $node, $value): void
    {
        $this->data[$node] = $value;
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

}
