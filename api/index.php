<?php

require_once __DIR__ . '/utils/Response.php';
require_once __DIR__ . '/middlewares/AuthMiddleware.php';
require_once __DIR__ . '/controllers/TicketController.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    Response::setHeaders();
    http_response_code(204);
    exit;
}

// Extrai o ID da rota (/api/tickets/{id}) ou via query string (?id=)
$uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$ticketId = null;

if (preg_match('#^/api/tickets(?:\.php)?/([^/]+)/?$#', $uriPath, $matches)) {
    $ticketId = urldecode($matches[1]);
} elseif (!empty($_GET['id'])) {
    $ticketId = trim($_GET['id']);
}

// Lê e valida JSON para métodos que enviam corpo
$body = null;
if (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
    $rawInput = file_get_contents('php://input');
    if (trim($rawInput) === '') {
        Response::error(400, 'EMPTY_BODY', 'O corpo da requisição JSON não pode estar vazio.');
    }

    $body = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        Response::error(400, 'INVALID_JSON', 'JSON malformado na requisição: ' . json_last_error_msg());
    }
}

$controller = new TicketController();

switch ($method) {
    case 'GET':
        if ($ticketId !== null) {
            $controller->show($ticketId);
        } else {
            $query = $_GET['q'] ?? null;
            $action = $_GET['action'] ?? null;
            $controller->index($query, $action);
        }
        break;

    case 'POST':
        if ($ticketId !== null) {
            Response::error(405, 'METHOD_NOT_ALLOWED', 'Não é permitido usar POST especificando um ID na URL. Use POST /api/tickets.');
        }
        AuthMiddleware::requireSeller();
        $controller->store($body);
        break;

    case 'PUT':
        if ($ticketId === null) {
            Response::error(400, 'MISSING_ID', 'Informe o ID do ticket na URL (ex: PUT /api/tickets/EVT-101).');
        }
        AuthMiddleware::requireSeller();
        $controller->update($ticketId, $body);
        break;

    case 'PATCH':
        if ($ticketId === null) {
            Response::error(400, 'MISSING_ID', 'Informe o ID do ticket na URL (ex: PATCH /api/tickets/EVT-101).');
        }
        AuthMiddleware::requireSeller();
        $controller->patch($ticketId, $body);
        break;

    case 'DELETE':
        if ($ticketId === null) {
            Response::error(400, 'MISSING_ID', 'Informe o ID do ticket na URL (ex: DELETE /api/tickets/EVT-101).');
        }
        AuthMiddleware::requireSeller();
        $controller->destroy($ticketId);
        break;

    default:
        Response::error(405, 'METHOD_NOT_ALLOWED', "Método HTTP '{$method}' não suportado neste endpoint.");
        break;
}
