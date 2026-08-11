<?php
// api/auth.php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {

    // POST /api/auth.php?action=login
    case 'login': {
        $data = json_decode(file_get_contents('php://input'), true);
        $email    = trim($data['email']    ?? '');
        $password = $data['password'] ?? '';

        if (!$email || !$password) {
            jsonError('Email and password are required.');
        }

        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || $user['password'] !== md5($password)) {
            jsonError('Invalid email or password.', 401);
        }

        $_SESSION['user'] = $user;
        unset($user['password']);
        jsonResponse(['user' => $user]);
    }

    // POST /api/auth.php?action=logout
    case 'logout': {
        session_destroy();
        jsonResponse(['ok' => true]);
    }

    // GET /api/auth.php?action=session
    case 'session': {
        $user = currentUser();
        if ($user) {
            unset($user['password']);
            jsonResponse(['user' => $user]);
        } else {
            jsonResponse(['user' => null]);
        }
    }

    // POST /api/auth.php?action=pin
    case 'pin': {
        $data = json_decode(file_get_contents('php://input'), true);
        $pin  = $data['pin'] ?? '';
        if ($pin === '5678') {
            $_SESSION['pin_unlocked'] = true;
            jsonResponse(['ok' => true]);
        } else {
            jsonError('Incorrect PIN.', 403);
        }
    }

    default:
        jsonError('Unknown action.', 404);
}
