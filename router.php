<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Roteamento da API REST (/api/tickets e /api/tickets/{id})
if (preg_match('#^/api/tickets(?:\.php)?(?:/.*)?$#', $uri)) {
    require __DIR__ . '/api/index.php';
    return true;
}

// Roteamento do Checkout (/api/checkout ou /api/checkout.php)
if (preg_match('#^/api/checkout(?:\.php)?$#', $uri)) {
    require __DIR__ . '/api/checkout.php';
    return true;
}

// Qualquer rota desconhecida dentro de /api/ retorna 404 JSON padronizado
if (strpos($uri, '/api/') === 0) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(404);
    echo json_encode([
        'status' => 404,
        'error_code' => 'ENDPOINT_NOT_FOUND',
        'message' => 'Endpoint da API não encontrado.'
    ], JSON_UNESCAPED_UNICODE);
    return true;
}

// Arquivos estáticos (CSS, JS, imagens) e páginas .php existentes
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Raiz do projeto
if ($uri === '/' && file_exists(__DIR__ . '/index.php')) {
    require __DIR__ . '/index.php';
    return true;
}

http_response_code(404);
echo "404 Not Found";
return true;
