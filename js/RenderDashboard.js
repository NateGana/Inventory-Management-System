"use strict";

async function renderDashboard() {
  let data;
  try {
    data = await API.getDashboard();
  } catch (e) {
    showToast("Failed to load dashboard.", "danger");
    return;
  }

  $("#statTotalItems").text(data.totalItems);
  $("#statTotalValue").text(formatPeso(data.totalValue));
  $("#statLowStock").text(data.lowStock);
  $("#statTotalTx").text(data.totalTx);

  // Low stock list
  const $lowList = $("#dashLowStockList").empty();
  if (!data.lowItems || data.lowItems.length === 0) {
    $lowList.html(
      '<div class="empty-state"><i class="fas fa-check-circle text-success"></i>' +
      '<p>All items are sufficiently stocked</p></div>'
    );
  } else {
    data.lowItems.forEach(function (item) {
      const colorClass = parseInt(item.stock) === 0 ? "text-danger" : "text-warning";
      $lowList.append(
        '<div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">' +
          '<div>' +
            '<span class="fw-semibold">' + item.name + '</span>' +
            '<span class="text-muted ms-2 small">' + item.sku + '</span>' +
          '</div>' +
          '<span class="' + colorClass + ' fw-bold">' + item.stock + ' left</span>' +
        '</div>'
      );
    });
  }

  // Recent transactions
  const $txList = $("#dashRecentTx").empty();
  if (!data.recentTx || data.recentTx.length === 0) {
    $txList.html(
      '<div class="empty-state"><i class="fas fa-receipt"></i><p>No transactions yet</p></div>'
    );
  } else {
    data.recentTx.forEach(function (tx) {
      const iconClass = tx.type === "Restock" ? "fa-arrow-down text-success" : "fa-arrow-up text-danger";
      $txList.append(
        '<div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">' +
          '<div>' +
            '<i class="fas ' + iconClass + ' me-2"></i>' +
            '<span class="fw-semibold">' + tx.item_name + '</span>' +
            '<span class="text-muted ms-2 small">×' + tx.qty + '</span>' +
          '</div>' +
          '<span class="text-muted small">' + formatDate(tx.created_at) + '</span>' +
        '</div>'
      );
    });
  }
}
