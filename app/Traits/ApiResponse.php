<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return json response into client side.
     * 
     * @param int $statusCode HTTP status code.
     * @param bool $success Display if the data work successfully or not.
     * @param string $msg Display a message.
     * @param null|object|array|Collection $resource Data that will return into the response body.
     * 
     * @return JsonResponse return json response
     */
    protected function response(
        int $statusCode,
        bool $success,
        string $msg,
        $resource = null,
    ): JsonResponse
    {
        $response_body = [
            'status' => $statusCode,
            'success' => $success,
            'message' => $msg,
        ];

        if (!is_null($resource)) {
            $response_body['resource'] = $resource;
        }

        return response()->json($response_body, $statusCode);
    }

    /**
     * Return error json response into client side.
     * 
     * @param int $statusCode HTTP status code.
     * @param bool $success Display if the data work successfully or not.
     * @param string $msg Display a message.
     * @param null|object|array|Collection $resource Data that will return into the response body.
     * 
     * @return JsonResponse return json response
     */
    protected function errorResponse(
        int $statusCode,
        bool $success = false,
        string $msg = null,
        $resource = null,
    ): JsonResponse
    {
        if ($success !== false) {
            $msg = "Invalid use of error response.";
            $statusCode = 500;

            throw new Exception(message: "Invalid use of error response.");
        }

        $response_body = [
            'status' => $statusCode,
            'success' => $success,
            'message' => $msg,
        ];

        if (!is_null($resource)) {
            $response_body['resource'] = $resource;
        }

        return response()->json($response_body, $statusCode);
    }

    /**
     * Return success response into client side.
     * 
     * @param int $statusCode HTTP status code.
     * @param bool $success Display if the data work successfully or not.
     * @param string $msg Display success message.
     * @param null|object|array|Collection $resource Data that will return into the response body.
     * 
     * @return JsonResponse return json response
     */
    protected function successResponse(
        int $statusCode = 200, 
        bool $success = true,
        string $msg = "Successfully retrieve data.", 
        $resource = null, 
    ): JsonResponse
    {
        if ($statusCode < 200 || $statusCode > 226) {
            $msg = "Success status not defined.";
            $statusCode = 500;

            throw new Exception(message: "Success status not defined.");
        }

        if ($success !== true) {
            $msg = "Invalid use of success response.";
            $statusCode = 500;

            throw new Exception(message: "Invalid use of success response.");
        }

        $response_body = [
            'status' => $statusCode,
            'success' => $success,
            'message' => $msg,
        ];

        if (!is_null($resource)) {
            $response_body['resource'] = $resource;
        }

        return response()->json($response_body, $statusCode);
    }

    /**
     * Return client error response into client side.
     * 
     * @param int $statusCode HTTP status code.
     * @param bool $success Display if the data work successfully or not.
     * @param string $msg Display error message.
     * @param null|object|array|Collection $resource Data that will return into the response body.
     * 
     * @return JsonResponse return json response
     */
    protected function clientErrorResponse(
        int $statusCode = 400,
        bool $success = false,
        string $msg = "Bad Request.",
        $resource = null,
    ): JsonResponse
    {
        if ($statusCode < 400 || $statusCode > 451) {
            $msg = "Client error status not defined.";
            $statusCode = 500;

            throw new Exception(message: "Client error status not defined.");
        }

        if ($success !== false) {
            $msg = "Invalid use of client error.";
            $statusCode = 500;
            
            throw new Exception(message: "Invalid use of client error.");
        }

        $response_body = [
            'status' => $statusCode,
            'success' => $success,
            'message' => $msg,
        ];

        if (!is_null($resource)) {
            $response_body['resource'] = $resource;
        }

        return response()->json($response_body, $statusCode);
    }

    /**
     * Return server error response into client side.
     * 
     * @param int $statusCode HTTP status code.
     * @param bool $success Display if the data work successfully or not.
     * @param string $msg Display error message.
     * @param null|object|array|Collection $resource Data that will return into the response body.
     * 
     * @return JsonResponse return json response
     */
    protected function serverErrorResponse(
        int $statusCode = 500,
        bool $success = false,
        string $msg = "Internal Server Error.",
        $resource = null,
    ): JsonResponse
    {
        if ($statusCode < 500 || $statusCode > 511) {
            $msg = "Server error status not defined.";
            $statusCode = 500;

            throw new Exception(message: "Server error status not defined.");
        }

        if ($success !== false) {
            $msg = "Invalid use of server error.";
            $statusCode = 500;
            
            throw new Exception(message: "Invalid use of server error.");
        }

        $response_body = [
            'status' => $statusCode,
            'success' => $success,
            'message' => $msg,
        ];

        if (!is_null($resource)) {
            $response_body['resource'] = $resource;
        }

        return response()->json($response_body, $statusCode);
    }
}