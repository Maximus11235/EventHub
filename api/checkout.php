<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 405, 'message' => 'Apenas o método POST é aceito no checkout.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['itens']) || !is_array($input['itens'])) {
    http_response_code(422);
    echo json_encode([
        'status' => 422,
        'message' => 'O carrinho está vazio ou os dados do pedido estão malformatados.'
    ]);
    exit;
}

// Simula processamento com sucesso (Status 200 / 201)
http_response_code(200);
echo json_encode([
    'status' => 200,
    'message' => 'Pagamento via PIX confirmado com sucesso!',
    'pedido_id' => 'PED-' . time(),
    'data_confirmacao' => date('Y-m-d H:i:s')
]);