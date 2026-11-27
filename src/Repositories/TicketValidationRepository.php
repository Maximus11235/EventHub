<?php

declare(strict_types=1);

namespace EventHub\Repositories;

use DateTimeImmutable;
use DomainException;
use PDO;
use Throwable;

final class TicketValidationRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    private function isValidatorAuthorizedForEvent(int $validadorId, int $eventId): bool
    {
        $stmtRole = $this->pdo->prepare('SELECT perfil FROM users WHERE id = :id LIMIT 1');
        $stmtRole->execute([':id' => $validadorId]);
        $user = $stmtRole->fetch();

        if (!$user || $user['perfil'] !== 'validador') {
            return false;
        }

        $stmtAuth = $this->pdo->prepare(
            'SELECT 1 FROM event_validators WHERE event_id = :event_id AND validador_id = :validador_id LIMIT 1'
        );
        $stmtAuth->execute([
            ':event_id'     => $eventId,
            ':validador_id' => $validadorId,
        ]);

        return (bool) $stmtAuth->fetchColumn();
    }

    public function listByClient(int $clienteId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT it.id, it.codigo_qr, it.codigo_assento, it.valor_pago, it.status, it.validado_em,
                    e.id AS event_id, e.titulo AS evento_titulo, e.estabelecimento, e.endereco,
                    e.estado, e.latitude, e.longitude, e.data_evento, e.imagem,
                    tt.nome AS categoria_nome,
                    u.nome_completo AS comprador_nome, u.documento AS comprador_documento, u.email AS comprador_email
             FROM issued_tickets it
             INNER JOIN events e ON e.id = it.event_id
             INNER JOIN ticket_types tt ON tt.id = it.ticket_type_id
             INNER JOIN users u ON u.id = it.cliente_id
             WHERE it.cliente_id = :cliente_id AND it.status <> 'cancelado'
             ORDER BY e.data_evento ASC, it.id DESC"
        );
        $stmt->execute([':cliente_id' => $clienteId]);

        $ativos = [];
        $expiradosCount = 0;
        $agora = time();

        foreach ($stmt->fetchAll() as $row) {
            $dt = new DateTimeImmutable((string) $row['data_evento']);
            if ($dt->getTimestamp() < $agora) {
                $expiradosCount++;
                continue;
            }
            $ativos[] = $this->formatTicket($row, $dt);
        }

        return [
            'ativos'          => $ativos,
            'expirados_count' => $expiradosCount,
        ];
    }

    public function listRecentForPortaria(?int $validadorId = null): array
    {
        if ($validadorId !== null && $validadorId > 0) {
            $stmt = $this->pdo->prepare(
                "SELECT it.id, it.codigo_qr, it.codigo_assento, it.status,
                        e.titulo AS evento_titulo, tt.nome AS categoria_nome
                 FROM issued_tickets it
                 INNER JOIN events e ON e.id = it.event_id
                 INNER JOIN ticket_types tt ON tt.id = it.ticket_type_id
                 INNER JOIN event_validators ev ON ev.event_id = e.id AND ev.validador_id = :validador_id
                 ORDER BY it.id DESC
                 LIMIT 25"
            );
            $stmt->execute([':validador_id' => $validadorId]);
        } else {
            $stmt = $this->pdo->query(
                "SELECT it.id, it.codigo_qr, it.codigo_assento, it.status,
                        e.titulo AS evento_titulo, tt.nome AS categoria_nome
                 FROM issued_tickets it
                 INNER JOIN events e ON e.id = it.event_id
                 INNER JOIN ticket_types tt ON tt.id = it.ticket_type_id
                 ORDER BY it.id DESC
                 LIMIT 25"
            );
        }

        return array_map(static fn(array $r) => [
            'id'            => (int) $r['id'],
            'codigo_qr'     => $r['codigo_qr'],
            'evento_titulo' => $r['evento_titulo'],
            'categoria'     => $r['categoria_nome'],
            'assento'       => $r['codigo_assento'] ?? 'Entrada Livre',
            'status'        => $r['status'],
        ], $stmt->fetchAll());
    }

    public function inspectQrCode(string $codigoQr, ?int $validadorId = null): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT it.id, it.codigo_qr, it.codigo_assento, it.valor_pago, it.status, it.validado_em,
                    e.id AS event_id, e.titulo AS evento_titulo, e.estabelecimento, e.endereco,
                    e.estado, e.latitude, e.longitude, e.data_evento, e.imagem,
                    tt.nome AS categoria_nome,
                    u.nome_completo AS comprador_nome, u.documento AS comprador_documento, u.email AS comprador_email
             FROM issued_tickets it
             INNER JOIN events e ON e.id = it.event_id
             INNER JOIN ticket_types tt ON tt.id = it.ticket_type_id
             INNER JOIN users u ON u.id = it.cliente_id
             WHERE LOWER(it.codigo_qr) = LOWER(:codigo_qr)
             LIMIT 1"
        );
        $stmt->execute([':codigo_qr' => trim($codigoQr)]);
        $row = $stmt->fetch();

        if (!$row) {
            return [
                'resultado' => 'INVALIDO',
                'mensagem'  => 'Nenhum ingresso encontrado com este código QR.',
                'ticket'    => null,
            ];
        }

        $dt = new DateTimeImmutable((string) $row['data_evento']);
        $ticket = $this->formatTicket($row, $dt);

        if ($validadorId !== null && !$this->isValidatorAuthorizedForEvent($validadorId, (int) $row['event_id'])) {
            return [
                'resultado' => 'NAO_AUTORIZADO',
                'mensagem'  => 'Você não está vinculado como validador autorizado para este evento.',
                'ticket'    => $ticket,
            ];
        }

        if ($dt->getTimestamp() < time()) {
            return [
                'resultado' => 'EXPIRADO',
                'mensagem'  => 'Este evento já ocorreu e o ingresso expirou.',
                'ticket'    => $ticket,
            ];
        }

        if ($row['status'] === 'utilizado') {
            return [
                'resultado' => 'JA_UTILIZADO',
                'mensagem'  => 'Este QR Code já deu entrada anteriormente.',
                'ticket'    => $ticket,
            ];
        }

        return [
            'resultado' => 'VALIDO',
            'mensagem'  => 'Ingresso válido e liberado para entrada.',
            'ticket'    => $ticket,
        ];
    }

    public function confirmCheckIn(string $codigoQr, int $validadorId): array
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'SELECT it.id, it.event_id, it.status, e.data_evento
                 FROM issued_tickets it
                 INNER JOIN events e ON e.id = it.event_id
                 WHERE LOWER(it.codigo_qr) = LOWER(:codigo_qr)
                 FOR UPDATE'
            );
            $stmt->execute([':codigo_qr' => trim($codigoQr)]);
            $row = $stmt->fetch();

            if (!$row) {
                throw new DomainException('QR Code não encontrado.', 404);
            }

            if (!$this->isValidatorAuthorizedForEvent($validadorId, (int) $row['event_id'])) {
                throw new DomainException('Validador não autorizado para a portaria deste evento.', 403);
            }

            if (strtotime((string) $row['data_evento']) < time()) {
                throw new DomainException('Não é possível validar ingresso de evento encerrado.', 422);
            }

            if ($row['status'] !== 'valido') {
                throw new DomainException('Este ingresso já foi utilizado ou cancelado.', 409);
            }

            $upd = $this->pdo->prepare(
                "UPDATE issued_tickets
                 SET status = 'utilizado',
                     validado_em = NOW(),
                     validador_id = :validador_id
                 WHERE id = :id"
            );
            $upd->execute([
                ':validador_id' => $validadorId,
                ':id'           => $row['id'],
            ]);

            $this->pdo->commit();
            return $this->inspectQrCode($codigoQr, $validadorId);
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    private function formatTicket(array $row, DateTimeImmutable $dt): array
    {
        $validadoEm = null;
        if (!empty($row['validado_em'])) {
            $validadoEm = (new DateTimeImmutable((string) $row['validado_em']))->format('d/m/Y H:i:s');
        }

        return [
            'id'                     => (int) $row['id'],
            'codigo_qr'              => $row['codigo_qr'],
            'event_id'               => (int) $row['event_id'],
            'comprador_nome'         => $row['comprador_nome'],
            'comprador_documento'    => $row['comprador_documento'],
            'comprador_cpf'          => $row['comprador_documento'],
            'comprador_email'        => $row['comprador_email'],
            'evento_titulo'          => $row['evento_titulo'],
            'evento_estabelecimento' => $row['estabelecimento'],
            'evento_endereco'        => $row['endereco'],
            'evento_estado'          => $row['estado'],
            'evento_latitude'        => (float) $row['latitude'],
            'evento_longitude'       => (float) $row['longitude'],
            'evento_data'            => $dt->format('Y-m-d\TH:i:s'),
            'evento_data_formatada'  => $dt->format('d/m/Y \à\s H:i'),
            'evento_imagem'          => $row['imagem'],
            'categoria'              => $row['categoria_nome'],
            'assento'                => $row['codigo_assento'] ?? 'Entrada Livre',
            'valor_pago'             => (float) $row['valor_pago'],
            'status'                 => $row['status'],
            'validado_em'            => $validadoEm,
        ];
    }
}
