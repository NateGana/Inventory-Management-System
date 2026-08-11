<?php
// api/transactions.php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

$user   = requireLogin();
$action = $_GET['action'] ?? '';
$pdo    = getDB();

switch ($action) {

    // GET — list all transactions (newest first)
    case 'list': {
        $stmt = $pdo->query('SELECT * FROM transactions ORDER BY created_at DESC');
        jsonResponse($stmt->fetchAll());
    }

    // POST — create transaction (restock or sale)
    case 'create': {
        $d      = json_decode(file_get_contents('php://input'), true);
        $type   = $d['type']   ?? '';
        $itemId = $d['itemId'] ?? '';
        $qty    = intval($d['qty'] ?? 0);
        $notes  = trim($d['notes'] ?? '');

        if (!in_array($type, ['Restock','Sale'])) jsonError('Invalid type.');
        if (!$itemId || $qty < 1) jsonError('Item and qty required.');

        // Block Supplier from Sales
        if ($user['role'] === 'Supplier' && $type === 'Sale') jsonError('Suppliers cannot record sales.', 403);

        // Fetch item
        $itemStmt = $pdo->prepare('SELECT * FROM inventory_items WHERE id = ? AND is_archived = 0');
        $itemStmt->execute([$itemId]);
        $item = $itemStmt->fetch();
        if (!$item) jsonError('Item not found.');

        // Stock check for Sale
        if ($type === 'Sale' && $qty > $item['stock']) {
            jsonError('Not enough stock. Available: ' . $item['stock']);
        }

        // Update stock
        $newStock = $type === 'Restock'
            ? $item['stock'] + $qty
            : $item['stock'] - $qty;

        $pdo->prepare('UPDATE inventory_items SET stock = ? WHERE id = ?')->execute([$newStock, $itemId]);

        // Insert transaction
        $txId   = generateId('tx');
        $amount = $qty * floatval($item['price']);

        $pdo->prepare(
            'INSERT INTO transactions (id,item_id,item_name,type,qty,price,amount,notes,user_id,user_name)
             VALUES (?,?,?,?,?,?,?,?,?,?)'
        )->execute([
            $txId, $itemId, $item['name'], $type, $qty,
            $item['price'], $amount, $notes,
            $user['id'], $user['name']
        ]);

        // Return updated item + new transaction
        $txRow = $pdo->prepare('SELECT * FROM transactions WHERE id = ?');
        $txRow->execute([$txId]);

        $itemRow = $pdo->prepare('SELECT * FROM inventory_items WHERE id = ?');
        $itemRow->execute([$itemId]);

        jsonResponse([
            'transaction' => $txRow->fetch(),
            'item'        => $itemRow->fetch()
        ], 201);
    }

    default:
        jsonError('Unknown action.', 404);
}
