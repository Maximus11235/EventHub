<?php

declare(strict_types=1);

namespace EventHub\Repositories;

use DateTimeImmutable;
use DomainException;
use PDO;
use PDOException;
use Throwable;

final class EventRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    private function assertIsAdvertiser(int $userId): void
    {
        $stmt = $this->pdo->prepare('SELECT perfil FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch();

        if (!$row || $row['perfil'] !== 'anunciante') {
            throw new DomainException('Apenas contas com perfil Anunciante podem gerenciar eventos.', 403);
        }
    }

    public function listActive(?string $search = null): array
    {
        $sql = "SELECT * FROM events WHERE status = 'ativo' AND data_evento >= NOW()";
        $params = [];

        if ($search !== null && trim($search) !== '') {
            $term = '%' . trim($search) . '%';
            $sql .= ' AND (titulo LIKE :q1 OR estado LIKE :q2 OR estabelecimento LIKE :q3 OR endereco LIKE :q4)';
            $params = [
                ':q1' => $term,
                ':q2' => $term,
                ':q3' => $term,
                ':q4' => $term,
            ];
        }

        $sql .= ' ORDER BY data_evento ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map(fn(array $row) => $this->hydrateEvent($row), $stmt->fetchAll());
    }

    public function listByAdvertiser(int $anuncianteId): array
    {
        $this->assertIsAdvertiser($anuncianteId);

        $stmt = $this->pdo->prepare('SELECT * FROM events WHERE anunciante_id = :id ORDER BY criado_em DESC');
        $stmt->execute([':id' => $anuncianteId]);

        return array_map(fn(array $row) => $this->hydrateEvent($row), $stmt->fetchAll());
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM events WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->hydrateEvent($row) : null;
    }

    public function create(int $anuncianteId, array $data): array
    {
        $this->assertIsAdvertiser($anuncianteId);

        $capacidade = (int) ($data['capacidade_maxima'] ?? 0);
        $categorias = $data['categorias'] ?? [];

        if ($capacidade <= 0 || empty($categorias)) {
            throw new DomainException('Informe capacidade máxima válida e ao menos uma categoria.', 422);
        }

        $somaLotes = 0;
        foreach ($categorias as $cat) {
            $qtd = (int) ($cat['quantidade_disponivel'] ?? 0);
            $preco = (float) ($cat['preco'] ?? 0);
            if (trim((string) ($cat['nome'] ?? '')) === '' || $qtd <= 0 || $preco < 0) {
                throw new DomainException('Todas as categorias devem possuir nome, preço e quantidade válidos.', 422);
            }
            $somaLotes += $qtd;
        }

        if ($somaLotes > $capacidade) {
            throw new DomainException("A soma dos lotes ({$somaLotes}) ultrapassa a capacidade máxima ({$capacidade}).", 422);
        }

        $possuiAssentos = !empty($data['possui_assentos_numerados']) ? 1 : 0;
        $fileiras = $possuiAssentos ? max(1, (int) ($data['fileiras'] ?? 5)) : 0;
        $colunas  = $possuiAssentos ? max(1, (int) ($data['colunas'] ?? 8)) : 0;

        $this->pdo->beginTransaction();
        try {
            $stmtEv = $this->pdo->prepare(
                'INSERT INTO events (
                    anunciante_id, titulo, estabelecimento, endereco, estado,
                    latitude, longitude, data_evento, imagem, capacidade_maxima,
                    possui_assentos_numerados, fileiras, colunas, descricao, status
                 ) VALUES (
                    :anunciante_id, :titulo, :estabelecimento, :endereco, :estado,
                    :latitude, :longitude, :data_evento, :imagem, :capacidade_maxima,
                    :possui_assentos, :fileiras, :colunas, :descricao, :status
                 )'
            );
            $stmtEv->execute([
                ':anunciante_id'     => $anuncianteId,
                ':titulo'            => trim((string) $data['titulo']),
                ':estabelecimento'   => trim((string) $data['estabelecimento']),
                ':endereco'          => trim((string) $data['endereco']),
                ':estado'            => trim((string) $data['estado']),
                ':latitude'          => (float) ($data['latitude'] ?? -16.6869),
                ':longitude'         => (float) ($data['longitude'] ?? -49.2648),
                ':data_evento'       => (string) $data['data_evento'],
                ':imagem'            => (string) ($data['imagem'] ?? 'imagens/capaIngresso.jpg'),
                ':capacidade_maxima' => $capacidade,
                ':possui_assentos'   => $possuiAssentos,
                ':fileiras'          => $fileiras,
                ':colunas'           => $colunas,
                ':descricao'         => trim((string) ($data['descricao'] ?? '')),
                ':status'            => 'ativo',
            ]);

            $eventId = (int) $this->pdo->lastInsertId();

            $stmtCat = $this->pdo->prepare(
                'INSERT INTO ticket_types (event_id, nome, preco, quantidade_total, quantidade_disponivel)
                 VALUES (:event_id, :nome, :preco, :total, :disponivel)'
            );

            foreach ($categorias as $cat) {
                $qtd = (int) $cat['quantidade_disponivel'];
                $stmtCat->execute([
                    ':event_id'   => $eventId,
                    ':nome'       => trim((string) $cat['nome']),
                    ':preco'      => (float) $cat['preco'],
                    ':total'      => $qtd,
                    ':disponivel' => $qtd,
                ]);
            }

            if (!empty($data['validadores']) && is_array($data['validadores'])) {
                foreach ($data['validadores'] as $identificador) {
                    $this->linkValidatorInternal($eventId, (string) $identificador);
                }
            }

            $this->pdo->commit();
            return $this->findById($eventId);
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function assignValidator(int $eventId, int $anuncianteId, string $identificador): array
    {
        $this->assertIsAdvertiser($anuncianteId);
        $this->assertEventOwner($eventId, $anuncianteId);
        $this->linkValidatorInternal($eventId, $identificador);

        return $this->findById($eventId);
    }

    public function removeValidator(int $eventId, int $anuncianteId, int $validadorId): array
    {
        $this->assertIsAdvertiser($anuncianteId);
        $this->assertEventOwner($eventId, $anuncianteId);

        $stmt = $this->pdo->prepare(
            'DELETE FROM event_validators WHERE event_id = :event_id AND validador_id = :validador_id'
        );
        $stmt->execute([
            ':event_id'     => $eventId,
            ':validador_id' => $validadorId,
        ]);

        return $this->findById($eventId);
    }

    public function delete(int $eventId, int $anuncianteId): void
    {
        $this->assertIsAdvertiser($anuncianteId);

        try {
            $stmt = $this->pdo->prepare(
                'DELETE FROM events WHERE id = :id AND anunciante_id = :anunciante_id'
            );
            $stmt->execute([
                ':id'            => $eventId,
                ':anunciante_id' => $anuncianteId,
            ]);

            if ($stmt->rowCount() === 0) {
                throw new DomainException('Evento não encontrado ou sem permissão para exclusão.', 404);
            }
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new DomainException('Não é possível excluir um evento que já possui ingressos vendidos.', 409);
            }
            throw $e;
        }
    }

    private function assertEventOwner(int $eventId, int $anuncianteId): void
    {
        $stmt = $this->pdo->prepare('SELECT anunciante_id FROM events WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $eventId]);
        $row = $stmt->fetch();

        if (!$row || (int) $row['anunciante_id'] !== $anuncianteId) {
            throw new DomainException('Evento não encontrado ou não pertence a este anunciante.', 403);
        }
    }

    private function linkValidatorInternal(int $eventId, string $identificador): void
    {
        $term = trim($identificador);
        if ($term === '') {
            throw new DomainException('Informe o e-mail, documento ou nome de usuário do validador.', 422);
        }

        $stmt = $this->pdo->prepare(
            "SELECT id, perfil FROM users
             WHERE LOWER(email) = LOWER(:q1)
                OR LOWER(nome_usuario) = LOWER(:q2)
                OR documento = :q3
             LIMIT 1"
        );
        $stmt->execute([
            ':q1' => $term,
            ':q2' => $term,
            ':q3' => $term,
        ]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new DomainException("Nenhum usuário encontrado para '{$term}'.", 404);
        }

        if ($user['perfil'] !== 'validador') {
            throw new DomainException("O usuário '{$term}' não possui perfil de Validador.", 422);
        }

        $ins = $this->pdo->prepare(
            'INSERT IGNORE INTO event_validators (event_id, validador_id) VALUES (:event_id, :validador_id)'
        );
        $ins->execute([
            ':event_id'     => $eventId,
            ':validador_id' => (int) $user['id'],
        ]);
    }

    private function hydrateEvent(array $row): array
    {
        $eventId = (int) $row['id'];

        $stmtCat = $this->pdo->prepare(
            'SELECT id, nome, preco, quantidade_total, quantidade_disponivel
             FROM ticket_types
             WHERE event_id = :event_id
             ORDER BY preco ASC'
        );
        $stmtCat->execute([':event_id' => $eventId]);
        $categorias = array_map(static fn(array $c) => [
            'id'                    => (int) $c['id'],
            'nome'                  => $c['nome'],
            'preco'                 => (float) $c['preco'],
            'quantidade_total'      => (int) $c['quantidade_total'],
            'quantidade_disponivel' => (int) $c['quantidade_disponivel'],
        ], $stmtCat->fetchAll());

        $stmtSeats = $this->pdo->prepare(
            "SELECT codigo_assento
             FROM issued_tickets
             WHERE event_id = :event_id AND status <> 'cancelado' AND codigo_assento IS NOT NULL"
        );
        $stmtSeats->execute([':event_id' => $eventId]);
        $ocupados = array_column($stmtSeats->fetchAll(), 'codigo_assento');

        $stmtVal = $this->pdo->prepare(
            'SELECT u.id, u.nome_usuario, u.nome_completo, u.email
             FROM event_validators ev
             INNER JOIN users u ON u.id = ev.validador_id
             WHERE ev.event_id = :event_id
             ORDER BY u.nome_completo ASC'
        );
        $stmtVal->execute([':event_id' => $eventId]);
        $validadores = array_map(static fn(array $v) => [
            'id'            => (int) $v['id'],
            'nome_usuario'  => $v['nome_usuario'],
            'nome_completo' => $v['nome_completo'],
            'email'         => $v['email'],
        ], $stmtVal->fetchAll());

        $dt = new DateTimeImmutable((string) $row['data_evento']);

        return [
            'id'                        => $eventId,
            'anunciante_id'             => (int) $row['anunciante_id'],
            'titulo'                    => $row['titulo'],
            'estabelecimento'           => $row['estabelecimento'],
            'endereco'                  => $row['endereco'],
            'estado'                    => $row['estado'],
            'latitude'                  => (float) $row['latitude'],
            'longitude'                 => (float) $row['longitude'],
            'data_evento'               => $dt->format('Y-m-d\TH:i:s'),
            'data_formatada'            => $dt->format('d/m/Y \à\s H:i'),
            'imagem'                    => $row['imagem'],
            'capacidade_maxima'         => (int) $row['capacidade_maxima'],
            'possui_assentos_numerados' => (bool) $row['possui_assentos_numerados'],
            'fileiras'                  => (int) $row['fileiras'],
            'colunas'                   => (int) $row['colunas'],
            'assentos_ocupados'         => $ocupados,
            'descricao'                 => $row['descricao'],
            'status'                    => $row['status'],
            'categorias'                => $categorias,
            'validadores'               => $validadores,
        ];
    }
}
