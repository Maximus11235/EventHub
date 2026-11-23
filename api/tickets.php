<?php
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

// Simulação de banco de dados em memória
$tickets = [
    [
        'id' => 'EVT-101',
        'titulo' => 'Steins Gate',
        'data_evento' => '2026-11-15T19:00:00-03:00',
        'data_formatada' => '15 de Novembro - 19:00',
        'estado' => 'Goiás, GO',
        'local' => 'Arena XYZ, Av. Principal, 123',
        'imagem' => 'imagens/capaIngresso.jpg',
        'preco' => 150.00
    ],
    [
        'id' => 'EVT-102',
        'titulo' => 'Festival de Música 2026',
        'data_evento' => '2026-12-01T20:00:00-03:00',
        'data_formatada' => '01 de Dezembro - 20:00',
        'estado' => 'São Paulo, SP',
        'local' => 'Allianz Parque, Av. Francisco Matarazzo',
        'imagem' => 'imagens/capaIngresso.jpg',
        'preco' => 220.00
    ]
];

switch ($method) {
    case 'GET':
        // Operação de Consulta (GET)
        $action = $_GET['action'] ?? '';
        
        if ($action === 'my-tickets') {
            // Retorna bilhetes simulados do usuário autenticado
            echo json_encode([
                [
                    'id' => '987654321ABCA-0',
                    'titulo' => 'Steins Gate',
                    'data' => '15 de Novembro - 20:00',
                    'imagem' => 'imagens/capaIngresso.jpg',
                    'status' => 'Válido'
                ]
            ]);
            exit;
        }

        echo json_encode($tickets);
        exit;

    case 'POST':
        // Operação de Criação / Publicação de Ticket (POST)
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['titulo']) || empty($input['preco'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'message' => 'Campos obrigatórios (título e preço) não foram preenchidos.'
            ]);
            exit;
        }

        http_response_code(201);
        echo json_encode([
            'status' => 201,
            'message' => 'Ticket publicado com sucesso!',
            'data' => array_merge(['id' => 'EVT-' . rand(100, 999)], $input)
        ]);
        exit;

    case 'PUT':
        // Operação de Substituição Integral (PUT)
        $id = $_GET['id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 400, 'message' => 'ID do ticket é obrigatório para atualização via PUT.']);
            exit;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Ticket #' . $id . ' atualizado por completo via PUT.',
            'data' => $input
        ]);
        exit;

    case 'PATCH':
        // Operação de Atualização Parcial (PATCH)
        $id = $_GET['id'] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 400, 'message' => 'ID do ticket é obrigatório para atualização via PATCH.']);
            exit;
        }

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Campos do ticket #' . $id . ' atualizados via PATCH.',
            'alteracoes' => array_keys($input)
        ]);
        exit;

    default:
        http_response_code(405);
        echo json_encode(['status' => 405, 'message' => 'Método HTTP não permitido.']);
        exit;
}