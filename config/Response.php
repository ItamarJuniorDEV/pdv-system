<?php

class Response
{
    public static function ok($data = null, string $message = ''): string
    {
        $payload = ['success' => true];

        if ($data !== null) {
            $payload['data']    = $data;
        }
        if ($message !== '') {
            $payload['message'] = $message;
        }

        return json_encode($payload);
    }

    public static function error(string $message, int $httpCode = 200): string
    {
        if ($httpCode !== 200) {
            http_response_code($httpCode);
        }

        return json_encode(['success' => false, 'message' => $message]);
    }

    public static function logException(string $context, \Throwable $e): void
    {
        error_log(sprintf(
            '[%s] %s in %s:%d%s%s',
            $context,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            PHP_EOL,
            $e->getTraceAsString()
        ));
    }
}
