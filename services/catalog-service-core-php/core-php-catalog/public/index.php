<?php

require __DIR__ . '/../src/Database.php';
require __DIR__ . '/../src/Book.php';
require __DIR__ . '/../src/BookController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$controller = new BookController();

// Health check - same idea as the Laravel /health route
if ($method === 'GET' && $uri === '/api/v1/health') {
    header('Content-Type: application/json');
    echo json_encode([
        'service' => 'catalog-service-core-php',
        'status' => 'OK',
        'time' => date('Y-m-d H:i:s'),
    ]);
    exit;
}

// GET /api/v1/books
if ($method === 'GET' && $uri === '/api/v1/books') {
    $controller->index();
    exit;
}

// GET /api/v1/books/{id}
if ($method === 'GET' && preg_match('#^/api/v1/books/(\d+)$#', $uri, $matches)) {
    $controller->show((int) $matches[1]);
    exit;
}

// POST /api/v1/books
if ($method === 'POST' && $uri === '/api/v1/books') {
    $controller->store();
    exit;
}

// PUT or PATCH /api/v1/books/{id}
if (in_array($method, ['PUT', 'PATCH']) && preg_match('#^/api/v1/books/(\d+)$#', $uri, $matches)) {
    $controller->update((int) $matches[1]);
    exit;
}

// DELETE /api/v1/books/{id}
if ($method === 'DELETE' && preg_match('#^/api/v1/books/(\d+)$#', $uri, $matches)) {
    $controller->destroy((int) $matches[1]);
    exit;
}

// No route matched
http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['error' => 'Route not found']);
