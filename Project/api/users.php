<?php
// api/users.php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// All user management requires a logged-in user OR a mod PIN unlock
$action = $_GET['action'] ?? '';
$pdo    = getDB();

$pinOk = ($_SESSION['pin_unlocked'] ?? false) === true;
$user  = currentUser();

if (!$user && !$pinOk) {
    jsonError('Mod PIN or login required.', 401);
}

// If logged in without PIN, must be Admin/Mod role
if ($user && !$pinOk && !in_array($user['role'], ['Admin', 'Mod'])) {
    jsonError('Mod PIN or Admin access required.', 403);
}

// For PIN-only access (no logged-in user), create a placeholder
if (!$user) {
    $user = ['id' => null, 'role' => 'Mod'];
}

switch ($action) {

    // GET — list all users (passwords stripped)
    case 'list': {
        $stmt  = $pdo->query('SELECT id,name,email,role,created_at FROM users ORDER BY created_at ASC');
        jsonResponse($stmt->fetchAll());
    }

    // POST — create user
    case 'create': {
        $d        = json_decode(file_get_contents('php://input'), true);
        $name     = trim($d['name']     ?? '');
        $email    = trim($d['email']    ?? '');
        $role     = $d['role']     ?? '';
        $password = $d['password'] ?? '';

        if (!$name || !$email || !$role || strlen($password) < 6) {
            jsonError('All fields required; password min 6 chars.');
        }
        if (!str_ends_with(strtolower($email), '@login.com')) {
            jsonError('Email must use @login.com domain.');
        }

        // Duplicate email check
        $chk = $pdo->prepare('SELECT id FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1');
        $chk->execute([$email]);
        if ($chk->fetch()) jsonError('Email already in use.');

        $id = generateId('user');
        $pdo->prepare('INSERT INTO users (id,name,email,password,role) VALUES (?,?,?,?,?)')
            ->execute([$id, $name, $email, md5($password), $role]);

        jsonResponse(['id'=>$id,'name'=>$name,'email'=>$email,'role'=>$role], 201);
    }

    // POST — update user
    case 'update': {
        $d        = json_decode(file_get_contents('php://input'), true);
        $id       = $d['id']       ?? '';
        $name     = trim($d['name']     ?? '');
        $email    = trim($d['email']    ?? '');
        $role     = $d['role']     ?? '';
        $password = $d['password'] ?? '';

        if (!$id || !$name || !$email || !$role || strlen($password) < 6) {
            jsonError('All fields required; password min 6 chars.');
        }

        // Duplicate email check (excluding self)
        $chk = $pdo->prepare('SELECT id FROM users WHERE LOWER(email) = LOWER(?) AND id != ? LIMIT 1');
        $chk->execute([$email, $id]);
        if ($chk->fetch()) jsonError('Email already in use.');

        $pdo->prepare('UPDATE users SET name=?,email=?,role=?,password=? WHERE id=?')
            ->execute([$name, $email, $role, md5($password), $id]);

        jsonResponse(['ok' => true]);
    }

    // POST — delete user
    case 'delete': {
        $d        = json_decode(file_get_contents('php://input'), true);
        $id       = $d['id'] ?? '';
        $current  = currentUser();

        if ($current && $current['id'] === $id) {
            jsonError('You cannot delete your own account.');
        }

        $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
        jsonResponse(['ok' => true]);
    }

    default:
        jsonError('Unknown action.', 404);
}
