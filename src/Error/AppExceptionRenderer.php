<?php

namespace App\Error;

use Cake\Error\Renderer\WebExceptionRenderer;
use Cake\Log\Log;

class AppExceptionRenderer extends WebExceptionRenderer
{
    public function render(): \Psr\Http\Message\ResponseInterface
    {
        if ($this->_getController()->getRequest()->getParam('prefix') == 'Api') {
            return $this->renderJson();
        }
        return parent::render();
    }

    /**
     * Renders a JSON:API error response
     *
     * @return \Cake\Http\Response
     */
    public function renderJson(): \Cake\Http\Response
    {
        $exception = $this->error;
        $code = $this->getHttpCode($exception);
        $message = $this->_message($exception, $code);

        $response = $this->_getController()->getResponse();
        $response = $response->withStatus($code);
        $response = $response->withStringBody(json_encode([
            'errors' => [
                [
                    'status' => $code,
                    'detail' => $message,
                ],
            ],
        ]));
        $response = $response->withHeader('Content-Type', 'application/vnd.api+json');

        return $response;
    }
}
