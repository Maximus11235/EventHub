<?php

require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';

class TicketController
{
    private string $dataFile;

    public function __construct()
    {
        $this->dataFile = __DIR__ . '/../data/tickets.json';
    }

    private function readTickets(): array
    {
        if (!file_exists($this->dataFile)) {
            return [];
        }

        $content = file_get_contents($this->dataFile);
        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function writeTickets(array $tickets): bool
    {
        $json = json_encode(array_values($tickets), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return file_put_contents($this->dataFile, $json . PHP_EOL, LOCK_EX) !== false;
    }

    private function findIndexById(array $tickets, string $id): int
    {
        foreach ($tickets as $index => $ticket) {
            if (isset($ticket['id']) && strcasecmp($ticket['id'], $id) === 0) {
                return $index;
            }
        }
        return -1;
    }

    private function formatarDataLegivel(string $isoDate): string
    {
        $timestamp = strtotime($isoDate);
        if ($timestamp === false) {
            return $isoDate;
        }

        $meses = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];

        $dia = date('d', $timestamp);
        $mes = $meses[(int)date('m', $timestamp)];
        $hora = date('H:i', $timestamp);

        return "{$dia} de {$mes} - {$hora}";
    }

    // GET /api/tickets
    public function index(?string $query = null, ?string $action = null): void
    {
        if ($action === 'my-tickets') {
            Response::json(200, [
                [
                    'id' => '987654321ABCA-0',
                    'ticket_id' => 'EVT-101',
                    'titulo' => 'Steins Gate',
                    'data' => '15 de Novembro - 20:00',
                    'imagem' => 'imagens/capaIngresso.jpg',
                    'status' => 'Válido'
                ]
            ]);
        }

        $tickets = $this->readTickets();

        if ($query !== null && trim($query) !== '') {
            $termo = trim($query);
            $tickets = array_values(array_filter($tickets, function ($ticket) use ($termo) {
                return mb_stripos($ticket['titulo'] ?? '', $termo) !== false
                    || mb_stripos($ticket['estado'] ?? '', $termo) !== false
                    || mb_stripos($ticket['local'] ?? '', $termo) !== false;
            }));
        }

        Response::json(200, $tickets);
    }

    // GET /api/tickets/{id}
    public function show(string $id): void
    {
        $tickets = $this->readTickets();
        $index = $this->findIndexById($tickets, $id);

        if ($index === -1) {
            Response::error(404, 'TICKET_NOT_FOUND', "Ticket com ID '{$id}' não foi encontrado.");
        }

        Response::json(200, $tickets[$index]);
    }

    // POST /api/tickets
    public function store($input): void
    {
        $errors = Validator::validateFullTicket($input);
        if (!empty($errors)) {
            Response::error(422, 'VALIDATION_ERROR', 'Os dados informados para criação do ticket são inválidos.', $errors);
        }

        $tickets = $this->readTickets();
        $nextNumber = 101;
        foreach ($tickets as $t) {
            if (preg_match('/^EVT-(\d+)$/', $t['id'] ?? '', $m)) {
                $nextNumber = max($nextNumber, (int)$m[1] + 1);
            }
        }

        $newId = 'EVT-' . $nextNumber;
        $dataEvento = trim($input['data_evento']);

        $newTicket = [
            'id' => $newId,
            'titulo' => trim($input['titulo']),
            'data_evento' => $dataEvento,
            'data_formatada' => !empty($input['data_formatada']) ? trim($input['data_formatada']) : $this->formatarDataLegivel($dataEvento),
            'estado' => trim($input['estado']),
            'local' => trim($input['local']),
            'imagem' => !empty($input['imagem']) ? trim($input['imagem']) : 'imagens/capaIngresso.jpg',
            'preco' => round((float)$input['preco'], 2),
            'quantidade_disponivel' => (int)$input['quantidade_disponivel'],
            'descricao' => isset($input['descricao']) ? trim($input['descricao']) : '',
            'status' => $input['status'] ?? 'ativo',
            'criado_em' => date('c')
        ];

        $tickets[] = $newTicket;

        if (!$this->writeTickets($tickets)) {
            Response::error(500, 'PERSISTENCE_ERROR', 'Falha ao salvar o novo ticket no servidor.');
        }

        Response::json(201, [
            'status' => 201,
            'message' => 'Ticket publicado com sucesso.',
            'data' => $newTicket
        ]);
    }

    // PUT /api/tickets/{id}
    public function update(string $id, $input): void
    {
        $tickets = $this->readTickets();
        $index = $this->findIndexById($tickets, $id);

        if ($index === -1) {
            Response::error(404, 'TICKET_NOT_FOUND', "Ticket com ID '{$id}' não foi encontrado.");
        }

        $errors = Validator::validateFullTicket($input);
        if (!empty($errors)) {
            Response::error(422, 'VALIDATION_ERROR', 'Todos os campos obrigatórios devem ser preenchidos corretamente no PUT.', $errors);
        }

        $existing = $tickets[$index];
        $dataEvento = trim($input['data_evento']);

        $updatedTicket = [
            'id' => $existing['id'],
            'titulo' => trim($input['titulo']),
            'data_evento' => $dataEvento,
            'data_formatada' => !empty($input['data_formatada']) ? trim($input['data_formatada']) : $this->formatarDataLegivel($dataEvento),
            'estado' => trim($input['estado']),
            'local' => trim($input['local']),
            'imagem' => !empty($input['imagem']) ? trim($input['imagem']) : ($existing['imagem'] ?? 'imagens/capaIngresso.jpg'),
            'preco' => round((float)$input['preco'], 2),
            'quantidade_disponivel' => (int)$input['quantidade_disponivel'],
            'descricao' => isset($input['descricao']) ? trim($input['descricao']) : '',
            'status' => $input['status'] ?? 'ativo',
            'criado_em' => $existing['criado_em'] ?? date('c'),
            'atualizado_em' => date('c')
        ];

        $tickets[$index] = $updatedTicket;

        if (!$this->writeTickets($tickets)) {
            Response::error(500, 'PERSISTENCE_ERROR', 'Falha ao atualizar o ticket no servidor.');
        }

        Response::json(200, [
            'status' => 200,
            'message' => "Ticket '{$existing['id']}' atualizado integralmente (PUT).",
            'data' => $updatedTicket
        ]);
    }

    // PATCH /api/tickets/{id}
    public function patch(string $id, $input): void
    {
        $tickets = $this->readTickets();
        $index = $this->findIndexById($tickets, $id);

        if ($index === -1) {
            Response::error(404, 'TICKET_NOT_FOUND', "Ticket com ID '{$id}' não foi encontrado.");
        }

        $errors = Validator::validatePartialTicket($input);
        if (!empty($errors)) {
            Response::error(422, 'VALIDATION_ERROR', 'Erro na validação dos campos enviados no PATCH.', $errors);
        }

        $allowedFields = [
            'titulo', 'data_evento', 'data_formatada', 'estado',
            'local', 'imagem', 'preco', 'quantidade_disponivel',
            'descricao', 'status'
        ];

        $ticket = $tickets[$index];
        $camposAlterados = [];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $input)) {
                if ($field === 'preco') {
                    $ticket[$field] = round((float)$input[$field], 2);
                } elseif ($field === 'quantidade_disponivel') {
                    $ticket[$field] = (int)$input[$field];
                } else {
                    $ticket[$field] = is_string($input[$field]) ? trim($input[$field]) : $input[$field];
                }
                $camposAlterados[] = $field;
            }
        }

        if (in_array('data_evento', $camposAlterados, true) && !in_array('data_formatada', $camposAlterados, true)) {
            $ticket['data_formatada'] = $this->formatarDataLegivel($ticket['data_evento']);
        }

        $ticket['atualizado_em'] = date('c');
        $tickets[$index] = $ticket;

        if (!$this->writeTickets($tickets)) {
            Response::error(500, 'PERSISTENCE_ERROR', 'Falha ao salvar alteração parcial no servidor.');
        }

        Response::json(200, [
            'status' => 200,
            'message' => "Ticket '{$ticket['id']}' atualizado parcialmente (PATCH).",
            'campos_alterados' => $camposAlterados,
            'data' => $ticket
        ]);
    }

    // DELETE /api/tickets/{id}
    public function destroy(string $id): void
    {
        $tickets = $this->readTickets();
        $index = $this->findIndexById($tickets, $id);

        if ($index === -1) {
            Response::error(404, 'TICKET_NOT_FOUND', "Ticket com ID '{$id}' não foi encontrado.");
        }

        $removed = $tickets[$index];
        array_splice($tickets, $index, 1);

        if (!$this->writeTickets($tickets)) {
            Response::error(500, 'PERSISTENCE_ERROR', 'Falha ao excluir o ticket no servidor.');
        }

        Response::json(200, [
            'status' => 200,
            'message' => "Ticket '{$removed['id']}' excluído com sucesso.",
            'id' => $removed['id']
        ]);
    }
}
