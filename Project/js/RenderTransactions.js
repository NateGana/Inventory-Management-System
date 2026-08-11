"use strict";

async function renderTransactions() {
  let txs;
  try {
    txs = await API.getTransactions();
  } catch (e) {
    showToast("Failed to load transactions.", "danger");
    return;
  }

  const $tbody = $("#transactionTable").empty();

  if (txs.length === 0) {
    $tbody.html('<tr><td colspan="7">' + emptyStateHTML("fas fa-receipt", "No transactions yet") + '</td></tr>');
    return;
  }

  txs.forEach(function (tx) {
    const isRestock = tx.type === "Restock";
    const typeClass = isRestock ? "text-success" : "text-danger";
    const typeIcon  = isRestock ? "fa-arrow-down" : "fa-arrow-up";
    const shortId   = tx.id.slice(-8);

    $tbody.append(
      '<tr>' +
        '<td class="ps-3"><code class="small text-muted">' + shortId + '</code></td>' +
        '<td class="text-muted small">' + formatDate(tx.created_at) + '</td>' +
        '<td class="fw-semibold">' + tx.item_name + '</td>' +
        '<td><span class="' + typeClass + ' fw-semibold"><i class="fas ' + typeIcon + ' me-1"></i>' + tx.type + '</span></td>' +
        '<td>×' + tx.qty + '</td>' +
        '<td class="fw-semibold">' + formatPeso(tx.amount) + '</td>' +
        '<td class="pe-3 text-muted small">' + tx.user_name + '</td>' +
      '</tr>'
    );
  });
}
