<?php

declare(strict_types=1);

namespace EventHub\Repositories;

use DomainException;
use PDO;
use PDOException;
use Throwable;

final class OrderRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    private function assertIsClient(int $clienteId): void
    {
        $stmt = $this->pdo->prepare('SELECT perfil FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $clienteId]);
        $row = $stmt->fetch();

        if (!$row || $row['perfil'] !== 'cliente') {
            throw new DomainException('Apenas contas com perfil Cliente podem comprar ingressos.', 403);
        }
    }

    public function checkout(int $clienteId, string $metodoPagamento, array $items): array
    {
        $this->assertIsClient($clienteId);

        if (empty($items)) {
            throw new DomainException('O carrinho está vazio.', 422);
        }

        $metodo = in_array($metodoPagamento, ['PIX', 'CARTAO'], true) ? $metodoPagamento : 'PIX';

        $this->pdo->beginTransaction();
        try {
            $valorTotal = 0.0;
            $preparedLines = [];

            $stmtLockType = $this->pdo->prepare(
                'SELECT tt.id, tt.event_id, tt.nome, tt.preco, tt.quantidade_disponivel,
                        e.titulo, e.possui_assentos_numerados, e.status, e.data_evento
                 FROM ticket_types tt
                 INNER JOIN events e ON e.id = tt.event_id
                 WHERE tt.id = :type_id AND tt.event_id = :event_id
                 FOR UPDATE'
            );

            foreach ($items as $item) {
                $eventId = (int) ($item['event_id'] ?? 0);
                $typeId  = (int) ($item['categoria_id'] ?? 0);
                $qty     = (int) ($item['quantidade'] ?? 0);
                $seats   = is_array($item['assentos'] ?? null) ? array_values($item['assentos']) : [];

                if ($eventId <= 0 || $typeId <= 0 || $qty <= 0) {
                    throw new DomainException('Item de compra inválido.', 422);
                }

                $stmtLockType->execute([':type_id' => $typeId, ':event_id' => $eventId]);
                $row = $stmtLockType->fetch();

                if (!$row) {
                    throw new DomainException('Categoria de ingresso não encontrada.', 404);
                }

                if ($row['status'] !== 'ativo' || strtotime((string) $row['data_evento']) < time()) {
                    throw new DomainException("O evento '{$row['titulo']}' já foi encerrado.", 422);
                }

                if ((int) $row['quantidade_disponivel'] < $qty) {
                    throw new DomainException("Estoque insuficiente para '{$row['titulo']} - {$row['nome']}'.", 422);
                }

                $isNumbered = (bool) $row['possui_assentos_numerados'];
                if ($isNumbered && count($seats) !== $qty) {
                    throw new DomainException("Selecione exatamente {$qty} assento(s) para '{$row['titulo']}'.", 422);
                }

                $unitPrice = (float) $row['preco'];
                $valorTotal += $unitPrice * $qty;

                $preparedLines[] = [
                    'event_id'    => $eventId,
                    'type_id'     => $typeId,
                    'qty'         => $qty,
                    'unit_price'  => $unitPrice,
                    'is_numbered' => $isNumbered,
                    'seats'       => $seats,
                ];
            }

            $codigoPedido = 'PED-' . strtoupper(bin2hex(random_bytes(5)));
            $stmtOrder = $this->pdo->prepare(
                'INSERT INTO orders (codigo_pedido, cliente_id, metodo_pagamento, valor_total, status_pagamento)
                 VALUES (:codigo, :cliente_id, :metodo, :total, :status)'
            );
            $stmtOrder->execute([
                ':codigo'     => $codigoPedido,
                ':cliente_id' => $clienteId,
                ':metodo'     => $metodo,
                ':total'      => $valorTotal,
                ':status'     => 'aprovado',
            ]);
            $orderId = (int) $this->pdo->lastInsertId();

            $stmtDecStock = $this->pdo->prepare(
                'UPDATE ticket_types
                 SET quantidade_disponivel = quantidade_disponivel - :qty
                 WHERE id = :id'
            );

            $stmtInsertTicket = $this->pdo->prepare(
                'INSERT INTO issued_tickets (
                    codigo_qr, order_id, event_id, ticket_type_id, cliente_id,
                    codigo_assento, valor_pago, status
                 ) VALUES (
                    :codigo_qr, :order_id, :event_id, :ticket_type_id, :cliente_id,
                    :codigo_assento, :valor_pago, :status
                 )'
            );

            $bilhetesEmitidos = [];

            foreach ($preparedLines as $line) {
                $stmtDecStock->execute([
                    ':qty' => $line['qty'],
                    ':id'  => $line['type_id'],
                ]);

                for ($i = 0; $i < $line['qty']; $i++) {
                    $seat = $line['is_numbered'] ? trim((string) $line['seats'][$i]) : null;
                    $seatSlug = $seat ? preg_replace('/\s+/', '', $seat) : 'LIVRE';
                    $randToken = strtoupper(bin2hex(random_bytes(4)));
                    $codigoQr = "QR-EVT{$line['event_id']}-{$seatSlug}-{$randToken}";

                    try {
                        $stmtInsertTicket->execute([
                            ':codigo_qr'      => $codigoQr,
                            ':order_id'       => $orderId,
                            ':event_id'       => $line['event_id'],
                            ':ticket_type_id' => $line['type_id'],
                            ':cliente_id'     => $clienteId,
                            ':codigo_assento' => $seat,
                            ':valor_pago'     => $line['unit_price'],
                            ':status'         => 'valido',
                        ]);
                    } catch (PDOException $e) {
                        if ($e->getCode() === '23000') {
                            throw new DomainException("O assento {$seat} já foi reservado por outro comprador.", 409);
                        }
                        throw $e;
                    }

                    $bilhetesEmitidos[] = [
                        'id'             => (int) $this->pdo->lastInsertId(),
                        'codigo_qr'      => $codigoQr,
                        'codigo_assento' => $seat ?? 'Entrada Livre',
                    ];
                }
            }

            $this->pdo->commit();

            return [
                'order_id'          => $orderId,
                'codigo_pedido'     => $codigoPedido,
                'metodo_pagamento'  => $metodo,
                'valor_total'       => $valorTotal,
                'bilhetes_emitidos' => $bilhetesEmitidos,
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
