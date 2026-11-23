<?php

require_once __DIR__ . '/utils/Response.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    Response::setHeaders();
    http_response_code(204);
    exit;
}

if ($method !== 'POST') {
    Response::error(405, 'METHOD_NOT_ALLOWED', 'Apenas o método POST é aceito no endpoint de checkout.');
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    Response::error(400, 'INVALID_JSON', 'JSON malformado enviado no checkout.');
}

if (empty($input['itens']) || !is_array($input['itens'])) {
    Response::error(422, 'EMPTY_CART', 'O carrinho está vazio ou os itens do pedido são inválidos.');
}

// Atualiza estoque no tickets.json caso o ticket exista na base persistida
$dataFile = __DIR__ . '/data/tickets.json';
if (file_exists($dataFile)) {
    $tickets = json_decode(file_get_contents($dataFile), true) ?: [];
    $modified = false;

    foreach ($input['itens'] as $item) {
        $tid = $item['ticket_id'] ?? '';
        $qtd = (int)($item['quantidade'] ?? 1);

        foreach ($tickets as &$ticket) {
            if (($ticket['id'] ?? '') === $tid) {
                if (($ticket['quantidade_disponivel'] ?? 0) < $qtd) {
                    Response::error(
                        422,
                        'INSUFFICIENT_STOCK',
                        "Estoque insuficiente para o evento '{$ticket['titulo']}'. Disponível: {$ticket['quantidade_disponivel']}."
                    );
                }
                $ticket['quantidade_disponivel'] -= $qtd;
                $modified = true;
            }
        }
        unset($ticket);
    }

    if ($modified) {
        file_put_contents($dataFile, json_encode($tickets, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL, LOCK_EX);
    }
}

Response::json(201, [
    'status' => 201,
    'message' => 'Pagamento via PIX confirmado com sucesso!',
    'pedido_id' => 'PED-' . time(),
    'data_confirmacao' => date('c')
]);
