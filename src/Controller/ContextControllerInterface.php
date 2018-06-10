<?php

namespace HybridLogin\Controller;


interface ContextControllerInterface
{
    /**
     * @return Controller
     */
    public function getParentController(): Controller;
}
