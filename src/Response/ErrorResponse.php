<?php

namespace App\Response;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorResponse extends JsonResponse
{
    /**
     * @param Exception $exception
     * @param int|null $code
     */
    public function __construct(Exception $exception, ?int $code = null)
    {
        if ($code == null)
            $code = $exception->getCode() == 0 ? 400 : $exception->getCode();

        parent::__construct(
            [
                'error' => [
                    'code' => $code,
                    'message' => $exception->getMessage()
                ]
            ],
            $code
        );
    }
}