<?php
namespace App\Traits;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

trait RestExceptionHandlerTrait
{

    /**
     * Creates a new JSON response based on exception type.
     *
     * @param Request $request
     * @param Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getJsonResponseForException(Request $request, Exception $e)
    {
        switch(true) {
            case $this->isModelNotFoundException($e):
                $retval = $this->modelNotFound();
                break;
            case $this->isUnauthorizedException($e):
                $retval = $this->tokenInvalid();
                break;
            default:
                $retval = $this->badRequest();
        }

        return $retval;
    }

    /**
     * Returns json response for generic bad request.
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badRequest($message='Bad request', $statusCode=400)
    {
        $response = [
            'error' => true,
            'message' => $message
        ];
        return $this->jsonResponse($response, $statusCode);
    }

    /**
     * Returns json response for Eloquent model not found exception.
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function modelNotFound($message='Record not found', $statusCode=404)
    {
        $response = [
            'error' => true,
            'message' => $message
        ];
        return $this->jsonResponse($response, $statusCode);
    }

    protected function tokenInvalid($message='Token Signature could not be verified', $statusCode=401)
    {
        $response = [
            'error' => true,
            'message' => $message
        ];
        return $this->jsonResponse($response, $statusCode);
    }

    protected function unauthorizedUser($message='No authorization to access route', $statusCode=401)
    {
        $response = [
            'error' => true,
            'message' => $message
        ];
        return $this->jsonResponse($response, $statusCode);
    }


    /**
     * Returns json response.
     *
     * @param array|null $payload
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse(array $payload=null, $statusCode=404)
    {
        $payload = $payload ?: [];

        return response()->json($payload, $statusCode);
    }

    /**
     * Determines if the given exception is an Eloquent model not found.
     *
     * @param Exception $e
     * @return bool
     */
    protected function isModelNotFoundException(Exception $e)
    {
        return $e instanceof ModelNotFoundException;
    }


    protected function isUnauthorizedException(Exception $e)
    {
        return $e instanceof UnauthorizedHttpException;
    }


}