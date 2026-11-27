<?php

declare(strict_types=1);

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Http/JsonResponse.php';
require_once __DIR__ . '/Repositories/UserRepository.php';
require_once __DIR__ . '/Repositories/EventRepository.php';
require_once __DIR__ . '/Repositories/OrderRepository.php';
require_once __DIR__ . '/Repositories/TicketValidationRepository.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
