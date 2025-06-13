<?php

namespace App\Helpers;

class ApiResponse
{
    const HTTPCODE_SUCCESS = 200;

    const HTTPCODE_CREATED = 201;

    const HTTPCODE_NOT_FOUND = 404;

    const HTTPCODE_BAD_REQUEST = 400;

    const HTTPCODE_UNAUTHORIZED = 401;

    const HTTPCODE_INTERNAL_SERVER_ERROR = 500;

    const HTTPCODE_FORBIDDEN = 403;

    const HTTPCODE_REDIRECT = 301;

    protected $statusCode = 200;

    /**
     * Generate success response
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function success(string $responseMsg = '', array $data = [], int $httpCode = 200, array $headers = [])
    {
        $response = self::prepareResponse($httpCode, $responseMsg, $data);

        return response($response, $httpCode, $headers);
    }

    /**
     * Prepare API response.
     */
    public static function prepareResponse($httpCode, $responseMsg, $data): array
    {
        return [
            'http_code' => $httpCode,
            'message' => $responseMsg,
            'data' => $data,
        ];
    }

    /**
     * Generate a Failed API response.
     *
     * @param  string  $responseCode
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function error(string $responseMsg = '', array $data = [], int $httpCode = 400, array $headers = [])
    {
        $response = self::prepareResponse($httpCode, $responseMsg, $data);

        return response($response, $httpCode, $headers);
    }

    /**
     * Get corresponding message per error code
     */
    public static function getError(array $data): string
    {
        $errorMessage = '';

        switch ($data['http_code']) {
            case 400:
                $errorMessage = 'Bad Request';
                break;
            case 401:
                $errorMessage = 'Unauthorized';
                break;
            case 403:
                $errorMessage = 'Forbidden';
                break;
            case 404:
                $errorMessage = 'Not Found';
                break;
            case 500:
                $errorMessage = 'Internal Server Error';
                break;
        }

        return $errorMessage;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    public function respondWithError($message)
    {
        return $this->respond([
            'error' => [
                'message' => $message,
            ],
        ]);
    }
}
