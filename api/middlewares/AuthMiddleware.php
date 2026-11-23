<?php

require_once __DIR__ . '/../utils/Response.php';

class AuthMiddleware
{
    public static function requireSeller(): void
    {
        $role = $_SERVER['HTTP_X_USER_ROLE'] ?? '';
        $role = strtolower(trim($role));

        if ($role === '') {
            Response::error(
                401,
                'UNAUTHORIZED',
                'Autenticação necessária. Envie o cabeçalho X-User-Role para acessar esta operação.'
            );
        }

        if ($role !== 'vendedor') {
            Response::error(
                403,
                'FORBIDDEN_ACTION',
                'Acesso negado. Apenas contas autorizadas com perfil vendedor podem criar, editar ou excluir tickets.'
            );
        }
    }
}
