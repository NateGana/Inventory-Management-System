"use strict";

async function renderReports() {
  let data;
  try {
    data = await API.getReports();
  } catch (e) {
    showToast("Failed to load reports.", "danger");
    return;
  }

  $("#repRevenue").text(formatPeso(data.revenue));
  $("#repRestocked").text(formatPeso(data.restocked));
  $("#repOutOfStock").text(data.outOfStock);

  // Category breakdown
  const $catTable = $("#repCategoryTable").empty();
  if (!data.categories || data.categories.length === 0) {
    $catTable.html('<tr><td colspan="3" class="text-center text-muted py-3">No data</td></tr>');
  } else {
    data.categories.forEach(function (cat) {
      $catTable.append(
        '<tr>' +
          '<td class="ps-3">' + cat.category + '</td>' +
          '<td>' + cat.item_count + '</td>' +
          '<td>' + cat.total_stock + '</td>' +
        '</tr>'
      );
    });
  }

  // Top 5 items
  const $topTable = $("#repTopItems").empty();
  if (!data.topItems || data.topItems.length === 0) {
    $topTable.html('<tr><td colspan="3" class="text-center text-muted py-3">No data</td></tr>');
  } else {
    data.topItems.forEach(function (item) {
      $topTable.append(
        '<tr>' +
          '<td class="ps-3">' + item.name + '</td>' +
          '<td>' + item.stock + '</td>' +
          '<td>' + formatPeso(item.total_value) + '</td>' +
        '</tr>'
      );
    });
  }
}
