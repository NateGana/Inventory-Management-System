<?php
// includes/auth.php — Session + auth helpers

session_start();

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function requireLogin(): array {
    $user = currentUser();
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }
    return $user;
}

function requireAdmin(): array {
    $user = requireLogin();
    if ($user['role'] !== 'Admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Moderator access required']);
        exit;
    }
    return $user;
}

function jsonResponse(mixed $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function jsonError(string $message, int $code = 400): void {
    jsonResponse(['error' => $message], $code);
}

function generateId(string $prefix): string {
    return $prefix . '_' . time() . '_' . rand(100, 999);
}
