<?php

class Validator
{
    public static function validateFullTicket($data): array
    {
        if (!is_array($data)) {
            return ['payload' => 'O corpo da requisição deve ser um objeto JSON válido.'];
        }

        $errors = [];

        if (empty($data['titulo']) || !is_string($data['titulo']) || mb_strlen(trim($data['titulo'])) < 3) {
            $errors['titulo'] = 'O título é obrigatório e deve ter pelo menos 3 caracteres.';
        }

        if (empty($data['data_evento']) || !is_string($data['data_evento']) || strtotime($data['data_evento']) === false) {
            $errors['data_evento'] = 'A data do evento é obrigatória e deve estar em formato ISO válido (ex: 2026-12-15T20:00:00-03:00).';
        }

        if (empty($data['estado']) || !is_string($data['estado'])) {
            $errors['estado'] = 'O estado/UF do evento é obrigatório.';
        }

        if (empty($data['local']) || !is_string($data['local'])) {
            $errors['local'] = 'O endereço/local do evento é obrigatório.';
        }

        if (!isset($data['preco']) || !is_numeric($data['preco']) || (float)$data['preco'] <= 0) {
            $errors['preco'] = 'O preço é obrigatório e deve ser um número maior que zero.';
        }

        if (!isset($data['quantidade_disponivel']) || !is_numeric($data['quantidade_disponivel']) || (int)$data['quantidade_disponivel'] < 0) {
            $errors['quantidade_disponivel'] = 'A quantidade disponível é obrigatória e deve ser um inteiro maior ou igual a zero.';
        }

        if (isset($data['status']) && !in_array($data['status'], ['ativo', 'pausado'], true)) {
            $errors['status'] = "O status deve ser 'ativo' ou 'pausado'.";
        }

        return $errors;
    }

    public static function validatePartialTicket($data): array
    {
        if (!is_array($data) || empty($data)) {
            return ['payload' => 'Envie ao menos um campo válido para atualização parcial (PATCH).'];
        }

        $allowedFields = [
            'titulo',
            'data_evento',
            'data_formatada',
            'estado',
            'local',
            'imagem',
            'preco',
            'quantidade_disponivel',
            'descricao',
            'status'
        ];

        $hasValidKey = false;
        foreach (array_keys($data) as $key) {
            if (in_array($key, $allowedFields, true)) {
                $hasValidKey = true;
                break;
            }
        }

        if (!$hasValidKey) {
            return ['campos' => 'Nenhum campo reconhecido foi enviado para atualização.'];
        }

        $errors = [];

        if (array_key_exists('titulo', $data)) {
            if (!is_string($data['titulo']) || mb_strlen(trim($data['titulo'])) < 3) {
                $errors['titulo'] = 'O título deve ser uma string com pelo menos 3 caracteres.';
            }
        }

        if (array_key_exists('data_evento', $data)) {
            if (!is_string($data['data_evento']) || strtotime($data['data_evento']) === false) {
                $errors['data_evento'] = 'A data do evento deve estar em formato ISO válido.';
            }
        }

        if (array_key_exists('preco', $data)) {
            if (!is_numeric($data['preco']) || (float)$data['preco'] <= 0) {
                $errors['preco'] = 'O preço deve ser um número maior que zero.';
            }
        }

        if (array_key_exists('quantidade_disponivel', $data)) {
            if (!is_numeric($data['quantidade_disponivel']) || (int)$data['quantidade_disponivel'] < 0) {
                $errors['quantidade_disponivel'] = 'A quantidade disponível deve ser maior ou igual a zero.';
            }
        }

        if (array_key_exists('status', $data)) {
            if (!in_array($data['status'], ['ativo', 'pausado'], true)) {
                $errors['status'] = "O status deve ser 'ativo' ou 'pausado'.";
            }
        }

        return $errors;
    }
}
