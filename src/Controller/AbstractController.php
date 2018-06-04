<?php

namespace HybridLogin\Controller;


abstract class AbstractController
{
    /**
     * @param Response $response
     * @return string
     */
    public function formatResponse(Response $response): string
    {
        $responseArray = [];
        $responseArray['success'] = !$response->hasError();
        $responseArray['errors'] = $response->getErrors();
        $responseArray['data'] = $response->getData();
        //$responseArray['session'] = serialize($_SESSION);
        return json_encode($responseArray);
    }
}
