<?php

class Response
{
    public static function json($data, $code = 200, $message = '')
    {
        $esError = $code >= 400;

        $response = [
            'status'  => $esError ? 'error' : 'success',
            'code'    => $code,
            'message' => $message,
        ];

        if (!$esError) {
            $response['data'] = $data;
        }

        self::enviar($response, $code);
    }

    public static function error($message, $code = 400, $details = null)
    {
        $response = [
            'status'  => 'error',
            'code'    => $code,
            'message' => $message,
        ];

        if ($details !== null) {
            $response['details'] = $details;
        }

        self::enviar($response, $code);
    }

    private static function enviar(array $response, int $code)
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}