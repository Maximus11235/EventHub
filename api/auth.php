<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

use EventHub\Database;
use EventHub\Http\JsonResponse;
use EventHub\Repositories\UserRepository;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $pdo  = Database::getConnection();
    $repo = new UserRepository($pdo);

    if ($method === 'GET') {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            JsonResponse::send(['user' => null]);
        }
        $user = $repo->findById((int) $userId);
        JsonResponse::send(['user' => $user]);
    }

    if ($method === 'POST') {
        $body = JsonResponse::readJsonBody();
        $action = $body['action'] ?? 'login';

        if ($action === 'logout') {
            $_SESSION = [];
            session_destroy();
            JsonResponse::send(['ok' => true]);
        }

        if ($action === 'register') {
            $body['documento'] = trim((string) ($body['documento'] ?? $body['cpf'] ?? ''));
            $required = ['nome_usuario', 'nome_completo', 'email', 'documento', 'senha'];
            foreach ($required as $field) {
                if (trim((string) ($body[$field] ?? '')) === '') {
                    JsonResponse::error('VALIDATION_ERROR', "O campo '{$field}' é obrigatório.", 422);
                }
            }
            $user = $repo->create($body);
            $_SESSION['user_id'] = $user['id'];
            JsonResponse::send(['user' => $user], 201);
        }

        $identifier = (string) ($body['identifier'] ?? '');
        $senha      = (string) ($body['senha'] ?? '');
        $user = $repo->authenticate($identifier, $senha);

        if (!$user) {
            JsonResponse::error('INVALID_CREDENTIALS', 'Usuário/e-mail ou senha inválidos.', 401);
        }

        $_SESSION['user_id'] = $user['id'];
        JsonResponse::send(['user' => $user]);
    }

    JsonResponse::error('METHOD_NOT_ALLOWED', 'Método HTTP não suportado.', 405);
} catch (DomainException $e) {
    JsonResponse::error('DOMAIN_ERROR', $e->getMessage(), $e->getCode() ?: 400);
} catch (RuntimeException $e) {
    JsonResponse::error('DB_UNAVAILABLE', $e->getMessage(), 503);
}
