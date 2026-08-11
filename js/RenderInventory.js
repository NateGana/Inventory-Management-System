"use strict";

// Cache for archive item count (to pass to bulk-delete handler)
let _archiveCount = 0;

async function renderInventory() {
  await renderActiveItems();
  await renderArchivedItems();
}

async function renderActiveItems() {
  const search   = $("#searchInput").val().toLowerCase();
  const category = $("#categoryFilter").val();
  const lowOnly  = $("#lowStockFilter").is(":checked");

  let items;
  try {
    items = await API.getItems();
  } catch (e) {
    showToast("Failed to load inventory.", "danger");
    return;
  }

  // Convert stock/threshold to numbers
  items = items.map(function (i) {
    i.stock     = parseInt(i.stock);
    i.threshold = parseInt(i.threshold);
    i.price     = parseFloat(i.price);
    return i;
  });

  if (search)   items = items.filter(function (i) {
    return i.name.toLowerCase().indexOf(search) !== -1 || i.sku.toLowerCase().indexOf(search) !== -1;
  });
  if (category) items = items.filter(function (i) { return i.category === category; });
  if (lowOnly)  items = items.filter(function (i) { return i.stock <= i.threshold; });

  $("#itemCount").text(items.length + " item" + (items.length !== 1 ? "s" : ""));

  const $tbody     = $("#inventoryTable").empty();
  const isSupplier = $("body").attr("data-role") === "Supplier";

  if (items.length === 0) {
    $tbody.html('<tr><td colspan="7">' + emptyStateHTML("fas fa-box-open", "No items found") + '</td></tr>');
    return;
  }

  items.forEach(function (item) {
    const status = getItemStatus(item);
    // Store item data in data attribute (JSON) for edit modal
    const itemJson = encodeURIComponent(JSON.stringify(item));

    const actionCell = isSupplier
      ? '<td class="text-end pe-3"><span class="text-muted small fst-italic">View only</span></td>'
      : '<td class="text-end pe-3">' +
          '<button class="action-btn bg-primary-subtle text-primary me-1 edit-item-btn" data-id="' + item.id + '" data-item="' + itemJson + '" title="Edit"><i class="fas fa-pen"></i></button>' +
          '<button class="action-btn bg-warning-subtle text-warning archive-item-btn" data-id="' + item.id + '" data-name="' + escHtml(item.name) + '" title="Archive"><i class="fas fa-box-archive"></i></button>' +
        '</td>';

    $tbody.append(
      '<tr>' +
        '<td class="ps-3"><code class="text-primary">' + escHtml(item.sku) + '</code></td>' +
        '<td>' +
          '<div class="fw-semibold">' + escHtml(item.name) + '</div>' +
          '<div class="text-muted small">' + (item.supplier ? escHtml(item.supplier) : "\u2014") + '</div>' +
        '</td>' +
        '<td><span class="badge bg-secondary bg-opacity-10 text-secondary">' + item.category + '</span></td>' +
        '<td class="fw-semibold">' + formatPeso(item.price) + '</td>' +
        '<td><span class="fw-bold">' + item.stock + '</span><span class="text-muted small"> / min ' + item.threshold + '</span></td>' +
        '<td>' + statusBadge(status) + '</td>' +
        actionCell +
      '</tr>'
    );
  });
}

async function renderArchivedItems() {
  const $container = $("#archiveSection");

  if ($("body").attr("data-role") !== "Admin") {
    $container.addClass("d-none");
    return;
  }

  let archived;
  try {
    archived = await API.getArchive();
  } catch (e) {
    $container.addClass("d-none");
    return;
  }

  archived = archived.map(function (i) {
    i.stock = parseInt(i.stock); i.price = parseFloat(i.price); return i;
  });

  _archiveCount = archived.length;

  if (archived.length === 0) {
    $container.addClass("d-none");
    return;
  }

  $container.removeClass("d-none");
  $("#archiveCount").text(archived.length + " archived item" + (archived.length !== 1 ? "s" : ""));

  const $tbody = $("#archiveTable").empty();
  archived.forEach(function (item) {
    $tbody.append(
      '<tr class="archived-row">' +
        '<td class="ps-3"><code class="text-secondary">' + escHtml(item.sku) + '</code></td>' +
        '<td>' +
          '<div class="fw-semibold text-muted">' + escHtml(item.name) + '</div>' +
          '<div class="text-muted small">' + (item.supplier ? escHtml(item.supplier) : "\u2014") + '</div>' +
        '</td>' +
        '<td><span class="badge bg-secondary bg-opacity-10 text-secondary">' + item.category + '</span></td>' +
        '<td class="text-muted">' + formatPeso(item.price) + '</td>' +
        '<td class="text-muted">' + item.stock + '</td>' +
        '<td class="text-end pe-3">' +
          '<button class="action-btn bg-success-subtle text-success me-1 restore-item-btn" data-id="' + item.id + '" data-name="' + escHtml(item.name) + '" title="Restore"><i class="fas fa-rotate-left"></i></button>' +
          '<button class="action-btn bg-danger-subtle text-danger perm-delete-item-btn" data-id="' + item.id + '" data-name="' + escHtml(item.name) + '" title="Delete permanently"><i class="fas fa-trash"></i></button>' +
        '</td>' +
      '</tr>'
    );
  });
}

function getItemStatus(item) {
  if (item.stock === 0)             return "Out of Stock";
  if (item.stock <= item.threshold) return "Low Stock";
  return "In Stock";
}

function escHtml(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}
