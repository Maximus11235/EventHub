<?php

declare(strict_types=1);

namespace EventHub\Repositories;

use DomainException;
use PDO;
use PDOException;

final class UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, perfil, nome_usuario, nome_completo, email, documento, telefone,
                    cartao_ultimos4, cartao_titular, cartao_validade
             FROM users
             WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->formatUser($row) : null;
    }

    public function authenticate(string $identifier, string $senha): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, perfil, nome_usuario, nome_completo, email, documento, telefone,
                    senha_hash, cartao_ultimos4, cartao_titular, cartao_validade
             FROM users
             WHERE LOWER(email) = LOWER(:email) OR LOWER(nome_usuario) = LOWER(:username)
             LIMIT 1'
        );
        $stmt->execute([
            ':email'    => trim($identifier),
            ':username' => trim($identifier),
        ]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        $storedHash = (string) $user['senha_hash'];
        $valid = false;

        if (str_starts_with($storedHash, 'SEED:')) {
            $plainSeed = substr($storedHash, 5);
            if ($senha === $plainSeed) {
                $valid = true;
                $newHash = password_hash($senha, PASSWORD_DEFAULT);
                $upd = $this->pdo->prepare('UPDATE users SET senha_hash = :hash WHERE id = :id');
                $upd->execute([':hash' => $newHash, ':id' => $user['id']]);
            }
        } else {
            $valid = password_verify($senha, $storedHash);
        }

        if (!$valid) {
            return null;
        }

        return $this->formatUser($user);
    }

    public function create(array $data): array
    {
        $perfil = in_array($data['perfil'] ?? '', ['cliente', 'anunciante', 'validador'], true)
            ? $data['perfil']
            : 'cliente';

        $documento = trim((string) ($data['documento'] ?? $data['cpf'] ?? ''));
        $hash = password_hash((string) $data['senha'], PASSWORD_DEFAULT);

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO users (perfil, nome_usuario, nome_completo, email, documento, telefone, senha_hash)
                 VALUES (:perfil, :nome_usuario, :nome_completo, :email, :documento, :telefone, :senha_hash)'
            );
            $stmt->execute([
                ':perfil'        => $perfil,
                ':nome_usuario'  => trim((string) $data['nome_usuario']),
                ':nome_completo' => trim((string) $data['nome_completo']),
                ':email'         => trim((string) $data['email']),
                ':documento'     => $documento,
                ':telefone'      => trim((string) ($data['telefone'] ?? '')),
                ':senha_hash'    => $hash,
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new DomainException('E-mail, documento (CPF/CNPJ) ou nome de usuário já cadastrado.', 409);
            }
            throw $e;
        }

        return $this->findById((int) $this->pdo->lastInsertId());
    }

    public function updateProfile(int $id, array $data): array
    {
        $documento = trim((string) ($data['documento'] ?? $data['cpf'] ?? ''));

        $fields = [
            'nome_usuario = :nome_usuario',
            'nome_completo = :nome_completo',
            'email = :email',
            'documento = :documento',
            'telefone = :telefone',
        ];
        $params = [
            ':id'            => $id,
            ':nome_usuario'  => trim((string) $data['nome_usuario']),
            ':nome_completo' => trim((string) $data['nome_completo']),
            ':email'         => trim((string) $data['email']),
            ':documento'     => $documento,
            ':telefone'      => trim((string) ($data['telefone'] ?? '')),
        ];

        if (!empty($data['senha'])) {
            $fields[] = 'senha_hash = :senha_hash';
            $params[':senha_hash'] = password_hash((string) $data['senha'], PASSWORD_DEFAULT);
        }

        try {
            $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new DomainException('E-mail, documento ou nome de usuário já está em uso.', 409);
            }
            throw $e;
        }

        return $this->findById($id) ?? throw new DomainException('Usuário não encontrado.', 404);
    }

    public function updateCard(int $id, ?array $card): array
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users
             SET cartao_ultimos4 = :ultimos4,
                 cartao_titular = :titular,
                 cartao_validade = :validade
             WHERE id = :id'
        );
        $stmt->execute([
            ':id'       => $id,
            ':ultimos4' => $card['ultimos4'] ?? null,
            ':titular'  => $card['titular'] ?? null,
            ':validade' => $card['validade'] ?? null,
        ]);

        return $this->findById($id) ?? throw new DomainException('Usuário não encontrado.', 404);
    }

    private function formatUser(array $row): array
    {
        $cartao = null;
        if (!empty($row['cartao_ultimos4'])) {
            $cartao = [
                'ultimos4' => $row['cartao_ultimos4'],
                'titular'  => $row['cartao_titular'],
                'validade' => $row['cartao_validade'],
            ];
        }

        return [
            'id'            => (int) $row['id'],
            'perfil'        => $row['perfil'],
            'nome_usuario'  => $row['nome_usuario'],
            'nome_completo' => $row['nome_completo'],
            'email'         => $row['email'],
            'documento'     => $row['documento'],
            'cpf'           => $row['documento'],
            'telefone'      => $row['telefone'],
            'cartao'        => $cartao,
        ];
    }
}
