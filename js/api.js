"use strict";

// ============================================================
//  api.js — Replaces the localStorage Store with PHP/MySQL API
//  All functions return Promises.
// ============================================================

const API = {

  // -- Auth --

  async login(email, password) {
    return await post('api/auth.php?action=login', { email, password });
  },

  async logout() {
    return await post('api/auth.php?action=logout', {});
  },

  async getSession() {
    const res = await get('api/auth.php?action=session');
    return res.user || null;
  },

  async verifyPin(pin) {
    return await post('api/auth.php?action=pin', { pin });
  },

  // -- Dashboard --

  async getDashboard() {
    return await get('api/dashboard.php');
  },

  // -- Items --

  async getItems() {
    return await get('api/items.php?action=list');
  },

  async getArchive() {
    return await get('api/items.php?action=archive');
  },

  async createItem(data) {
    return await post('api/items.php?action=create', data);
  },

  async updateItem(data) {
    return await post('api/items.php?action=update', data);
  },

  async archiveItem(id) {
    return await post('api/items.php?action=archive_item', { id });
  },

  async restoreItem(id) {
    return await post('api/items.php?action=restore', { id });
  },

  async restoreAllItems() {
    return await post('api/items.php?action=restore_all', {});
  },

  async deleteArchivedItem(id) {
    return await post('api/items.php?action=delete', { id });
  },

  async deleteAllArchivedItems() {
    return await post('api/items.php?action=delete_all_archived', {});
  },

  // -- Transactions --

  async getTransactions() {
    return await get('api/transactions.php?action=list');
  },

  async createTransaction(data) {
    return await post('api/transactions.php?action=create', data);
  },

  // -- Users --

  async getUsers() {
    return await get('api/users.php?action=list');
  },

  async createUser(data) {
    return await post('api/users.php?action=create', data);
  },

  async updateUser(data) {
    return await post('api/users.php?action=update', data);
  },

  async deleteUser(id) {
    return await post('api/users.php?action=delete', { id });
  },

  // -- Reports --

  async getReports() {
    return await get('api/reports.php');
  },
};

// ---- HTTP helpers ----

async function get(url) {
  const res = await fetch(url, { credentials: 'same-origin' });
  const data = await res.json();
  if (!res.ok) throw new Error(data.error || 'Request failed');
  return data;
}

async function post(url, body) {
  const res = await fetch(url, {
    method:      'POST',
    credentials: 'same-origin',
    headers:     { 'Content-Type': 'application/json' },
    body:        JSON.stringify(body),
  });
  const data = await res.json();
  if (!res.ok) throw new Error(data.error || 'Request failed');
  return data;
}

// ---- Item helper: map DB row → InventoryItem-like object ----
function rowToItem(row) {
  return new InventoryItem(
    row.id, row.sku, row.name, row.category,
    row.price, row.stock, row.threshold,
    row.supplier, row.description, row.created_at
  );
}

function rowToTx(row) {
  return new Transaction(
    row.id, row.item_id, row.item_name, row.type,
    row.qty, row.price, row.notes,
    row.user_id, row.user_name, row.created_at
  );
}
