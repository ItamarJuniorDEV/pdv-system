<?php

class Guard
{
    public static function requireAjax(): void
    {
        header('Cache-Control: no-store');

        $requested = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        if (strtolower($requested) !== 'xmlhttprequest') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
            exit;
        }

        Auth::start();
        if (!Auth::check()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Sessão expirada. Faça login novamente.']);
            exit;
        }
    }

    public static function requireRole(string ...$roles): void
    {
        if (!Auth::can(...$roles)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Permissão insuficiente.']);
            exit;
        }
    }
}
