<?php
// api/items.php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

$user   = requireLogin();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$pdo    = getDB();

switch ($action) {

    // GET — list active items
    case 'list': {
        $stmt = $pdo->query("SELECT * FROM inventory_items WHERE is_archived = 0 ORDER BY created_at ASC");
        jsonResponse($stmt->fetchAll());
    }

    // GET — list archived items (Admin only)
    case 'archive': {
        if ($user['role'] !== 'Admin') jsonError('Admin only.', 403);
        $stmt = $pdo->query("SELECT * FROM inventory_items WHERE is_archived = 1 ORDER BY created_at ASC");
        jsonResponse($stmt->fetchAll());
    }

    // POST — create item
    case 'create': {
        if ($user['role'] === 'Supplier') jsonError('Not allowed.', 403);
        $d = json_decode(file_get_contents('php://input'), true);

        $id        = generateId('item');
        $sku       = trim($d['sku']         ?? '');
        $name      = trim($d['name']        ?? '');
        $category  = $d['category']  ?? '';
        $price     = floatval($d['price']   ?? 0);
        $stock     = intval($d['stock']     ?? 0);
        $threshold = intval($d['threshold'] ?? 10);
        $supplier  = trim($d['supplier']    ?? '');
        $desc      = trim($d['description'] ?? '');

        if (!$sku || !$name || !$category) jsonError('SKU, name, and category are required.');

        // Check duplicate SKU
        $chk = $pdo->prepare('SELECT id FROM inventory_items WHERE UPPER(sku) = UPPER(?) LIMIT 1');
        $chk->execute([$sku]);
        if ($chk->fetch()) jsonError('SKU already exists.');

        $stmt = $pdo->prepare(
            'INSERT INTO inventory_items (id,sku,name,category,price,stock,threshold,supplier,description)
             VALUES (?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([$id,$sku,$name,$category,$price,$stock,$threshold,$supplier,$desc]);

        $row = $pdo->prepare('SELECT * FROM inventory_items WHERE id = ?');
        $row->execute([$id]);
        jsonResponse($row->fetch(), 201);
    }

    // POST — update item
    case 'update': {
        if ($user['role'] === 'Supplier') jsonError('Not allowed.', 403);
        $d = json_decode(file_get_contents('php://input'), true);
        $id = $d['id'] ?? '';
        if (!$id) jsonError('Item ID required.');

        $sku       = trim($d['sku']         ?? '');
        $name      = trim($d['name']        ?? '');
        $category  = $d['category']  ?? '';
        $price     = floatval($d['price']   ?? 0);
        $stock     = intval($d['stock']     ?? 0);
        $threshold = intval($d['threshold'] ?? 10);
        $supplier  = trim($d['supplier']    ?? '');
        $desc      = trim($d['description'] ?? '');

        // Check duplicate SKU (excluding self)
        $chk = $pdo->prepare('SELECT id FROM inventory_items WHERE UPPER(sku) = UPPER(?) AND id != ? LIMIT 1');
        $chk->execute([$sku, $id]);
        if ($chk->fetch()) jsonError('SKU already used by another item.');

        $stmt = $pdo->prepare(
            'UPDATE inventory_items SET sku=?,name=?,category=?,price=?,stock=?,threshold=?,supplier=?,description=?
             WHERE id=?'
        );
        $stmt->execute([$sku,$name,$category,$price,$stock,$threshold,$supplier,$desc,$id]);

        $row = $pdo->prepare('SELECT * FROM inventory_items WHERE id = ?');
        $row->execute([$id]);
        jsonResponse($row->fetch());
    }

    // POST — archive item
    case 'archive_item': {
        if ($user['role'] !== 'Admin' && $user['role'] !== 'Staff') jsonError('Not allowed.', 403);
        $d  = json_decode(file_get_contents('php://input'), true);
        $id = $d['id'] ?? '';
        $pdo->prepare('UPDATE inventory_items SET is_archived = 1 WHERE id = ?')->execute([$id]);
        jsonResponse(['ok' => true]);
    }

    // POST — restore item
    case 'restore': {
        if ($user['role'] !== 'Admin') jsonError('Admin only.', 403);
        $d  = json_decode(file_get_contents('php://input'), true);
        $id = $d['id'] ?? '';
        $pdo->prepare('UPDATE inventory_items SET is_archived = 0 WHERE id = ?')->execute([$id]);
        jsonResponse(['ok' => true]);
    }

    // POST — restore all archived
    case 'restore_all': {
        if ($user['role'] !== 'Admin') jsonError('Admin only.', 403);
        $pdo->exec('UPDATE inventory_items SET is_archived = 0 WHERE is_archived = 1');
        jsonResponse(['ok' => true]);
    }

    // POST — permanently delete archived item
    case 'delete': {
        if ($user['role'] !== 'Admin') jsonError('Admin only.', 403);
        $d  = json_decode(file_get_contents('php://input'), true);
        $id = $d['id'] ?? '';
        $pdo->prepare('DELETE FROM inventory_items WHERE id = ? AND is_archived = 1')->execute([$id]);
        jsonResponse(['ok' => true]);
    }

    // POST — delete all archived
    case 'delete_all_archived': {
        if ($user['role'] !== 'Admin') jsonError('Admin only.', 403);
        $pdo->exec('DELETE FROM inventory_items WHERE is_archived = 1');
        jsonResponse(['ok' => true]);
    }

    default:
        jsonError('Unknown action.', 404);
}
