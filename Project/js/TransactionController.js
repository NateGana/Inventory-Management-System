"use strict";

async function openTxModal(type) {
  $("#txType").val(type);
  $("#txModalTitle").text(type === "Restock" ? "New Restock" : "New Sale");
  $("#txQty").val("");
  $("#txNotes").val("");
  $(".is-invalid").removeClass("is-invalid");

  let items;
  try {
    items = await API.getItems();
  } catch (e) {
    showToast("Failed to load items.", "danger");
    return;
  }

  const $sel = $("#txProduct").empty().append('<option value="">Select product...</option>');
  items.forEach(function (i) {
    $sel.append('<option value="' + i.id + '">' + i.name + ' (Stock: ' + i.stock + ')</option>');
  });

  txModalInstance.show();
}

async function saveTransaction() {
  const type   = $("#txType").val();
  const itemId = $("#txProduct").val();
  const qty    = parseInt($("#txQty").val());
  const notes  = $("#txNotes").val().trim();

  let valid = true;
  if (!itemId)               { $("#txProduct").addClass("is-invalid"); valid = false; }
  if (isNaN(qty) || qty < 1) { $("#txQty").addClass("is-invalid");    valid = false; }
  if (!valid) return;

  $(".is-invalid").removeClass("is-invalid");

  try {
    const result = await API.createTransaction({ type, itemId, qty, notes });
    txModalInstance.hide();
    renderTransactions();
    renderInventory();
    updateLowStockBanner();
    showToast(type + " recorded for " + result.transaction.item_name + "!", type === "Restock" ? "success" : "info");
  } catch (err) {
    showToast(err.message || "Transaction failed.", "danger");
  }
}
