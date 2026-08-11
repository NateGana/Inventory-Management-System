<?php
// api/reports.php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

$user = requireLogin();
if ($user['role'] === 'Supplier') jsonError('Access denied.', 403);

$pdo = getDB();

$revenue    = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE type='Sale'")->fetchColumn();
$restocked  = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE type='Restock'")->fetchColumn();
$outOfStock = $pdo->query("SELECT COUNT(*) FROM inventory_items WHERE is_archived=0 AND stock=0")->fetchColumn();

// Category breakdown
$catRows = $pdo->query(
    "SELECT category,COUNT(*) as item_count, SUM(stock) as total_stock
     FROM inventory_items WHERE is_archived=0 GROUP BY category ORDER BY category"
)->fetchAll();

// Top 5 by value
$topItems = $pdo->query(
    "SELECT name, stock, price, (price*stock) as total_value
     FROM inventory_items WHERE is_archived=0
     ORDER BY total_value DESC LIMIT 5"
)->fetchAll();

jsonResponse([
    'revenue'    => (float)$revenue,
    'restocked'  => (float)$restocked,
    'outOfStock' => (int)$outOfStock,
    'categories' => $catRows,
    'topItems'   => $topItems,
]);
