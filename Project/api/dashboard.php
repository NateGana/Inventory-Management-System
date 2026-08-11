<?php
// api/dashboard.php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

requireLogin();
$pdo = getDB();

$totalItems = $pdo->query('SELECT COUNT(*) FROM inventory_items WHERE is_archived = 0')->fetchColumn();
$totalValue = $pdo->query('SELECT COALESCE(SUM(price * stock), 0) FROM inventory_items WHERE is_archived = 0')->fetchColumn();
$lowStock   = $pdo->query('SELECT COUNT(*) FROM inventory_items WHERE is_archived = 0 AND stock <= threshold')->fetchColumn();
$totalTx    = $pdo->query('SELECT COUNT(*) FROM transactions')->fetchColumn();

// Low stock items list
$lowItems = $pdo->query(
    'SELECT name, sku, stock FROM inventory_items WHERE is_archived = 0 AND stock <= threshold ORDER BY stock ASC'
)->fetchAll();

// Recent 5 transactions
$recentTx = $pdo->query(
    'SELECT item_name, type, qty, created_at FROM transactions ORDER BY created_at DESC LIMIT 5'
)->fetchAll();

jsonResponse([
    'totalItems' => (int)$totalItems,
    'totalValue' => (float)$totalValue,
    'lowStock'   => (int)$lowStock,
    'totalTx'    => (int)$totalTx,
    'lowItems'   => $lowItems,
    'recentTx'   => $recentTx,
]);
