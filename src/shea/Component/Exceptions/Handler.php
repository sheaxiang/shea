<?php

namespace Shea\Component\Exceptions;

use Exception;
use Shea\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return mixed
     *
     * @throws \Exception
     */
    public function report(Exception $e)
    {
        
    }

    public function render($request, Exception $e)
    {
        return $this->prepareJsonResponse($request, $e);
        /* return $request->expectsJson()
                        ? $this->prepareJsonResponse($request, $e)
                        : $this->prepareResponse($request, $e); */
    }

    protected function prepareJsonResponse($request, Exception $e)
    {
        return new JsonResponse(
            $this->convertExceptionToArray($e),
            $this->isHttpException($e) ? $e->getStatusCode() : 500,
            $this->isHttpException($e) ? $e->getHeaders() : []
        );
    }

    protected function convertExceptionToArray(Exception $e)
    {
        return config('app.debug') ? [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ] : [
            'message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
        ];
    }

    protected function isHttpException(Exception $e)
    {
        return $e instanceof HttpException;
    }
}
