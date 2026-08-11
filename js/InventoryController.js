"use strict";

const SKU_PREFIXES = {
  Electronics: "ELEC-",
  Clothing:    "CLO-",
  Food:        "FOO-",
  Office:      "OFF-",
  Hardware:    "HRD-"
};

// -- Modals --

function openAddItemModal() {
  $("#itemEditId").val("");
  $("#itemSku, #itemName, #itemSupplier, #itemDesc").val("");
  $("#itemCategory").val("");
  $("#itemPrice, #itemStock").val("");
  $("#itemThreshold").val("10");
  $(".is-invalid").removeClass("is-invalid");
  $("#itemSku").attr("placeholder", "Select a category first");
  $("#skuHint").text("");
  $("#itemModalTitle").text("Add Inventory Item");
  itemModalInstance.show();
}

function openEditItemModal(id, itemData) {
  // itemData passed from rendered row to avoid another API call
  $("#itemEditId").val(itemData.id);
  $("#itemSku").val(itemData.sku);
  $("#itemName").val(itemData.name);
  $("#itemCategory").val(itemData.category);
  $("#itemPrice").val(itemData.price);
  $("#itemStock").val(itemData.stock);
  $("#itemThreshold").val(itemData.threshold);
  $("#itemSupplier").val(itemData.supplier);
  $("#itemDesc").val(itemData.description);
  $(".is-invalid").removeClass("is-invalid");
  updateSkuHint(itemData.category);
  $("#itemSku").attr("placeholder", (SKU_PREFIXES[itemData.category] || "") + "001");
  $("#itemModalTitle").text("Edit Inventory Item");
  itemModalInstance.show();
}

// -- SKU helpers --

function updateSkuHint(category) {
  const prefix = SKU_PREFIXES[category];
  if (prefix) {
    $("#skuHint").html(
      '<i class="fas fa-tag me-1"></i>Must start with <code>' + prefix + '</code> &mdash; no spaces allowed.'
    );
    $("#itemSku").attr("placeholder", prefix + "001");
  } else {
    $("#skuHint").text("");
    $("#itemSku").attr("placeholder", "Select a category first");
  }
}

function validateSkuFormat(sku, category) {
  if (!sku) return "SKU is required.";
  if (/\s/.test(sku)) return "SKU must not contain spaces.";
  const prefix = SKU_PREFIXES[category];
  if (prefix && !sku.toUpperCase().startsWith(prefix.toUpperCase())) {
    return 'SKU for ' + category + ' must start with "' + prefix + '" (e.g. ' + prefix + '001).';
  }
  return "";
}

// -- Save (create or update) --

async function saveItem() {
  const id        = $("#itemEditId").val();
  const sku       = $("#itemSku").val().trim();
  const name      = $("#itemName").val().trim();
  const category  = $("#itemCategory").val();
  const price     = parseFloat($("#itemPrice").val());
  const stock     = parseInt($("#itemStock").val());
  const threshold = parseInt($("#itemThreshold").val()) || 10;
  const supplier  = $("#itemSupplier").val().trim();
  const desc      = $("#itemDesc").val().trim();

  let valid = true;

  if (!category) { $("#itemCategory").addClass("is-invalid"); valid = false; }

  const skuError = validateSkuFormat(sku, category);
  if (skuError) {
    $("#itemSku").addClass("is-invalid");
    $("#skuFeedback").text(skuError);
    valid = false;
  }

  if (!name)                     { $("#itemName").addClass("is-invalid");  valid = false; }
  if (isNaN(price) || price < 0) { $("#itemPrice").addClass("is-invalid"); valid = false; }
  if (isNaN(stock) || stock < 0) { $("#itemStock").addClass("is-invalid"); valid = false; }
  if (!valid) return;

  $(".is-invalid").removeClass("is-invalid");

  const payload = { id, sku, name, category, price, stock, threshold, supplier, description: desc };

  try {
    if (id) {
      await API.updateItem(payload);
      showToast("Item updated successfully!", "success");
    } else {
      await API.createItem(payload);
      showToast("Item added successfully!", "success");
    }
    itemModalInstance.hide();
    renderInventory();
    updateLowStockBanner();
  } catch (err) {
    showToast(err.message || "Failed to save item.", "danger");
    // Highlight SKU if duplicate error
    if (err.message && err.message.toLowerCase().includes("sku")) {
      $("#itemSku").addClass("is-invalid");
      $("#skuFeedback").text(err.message);
    }
  }
}

// -- Archive / restore / delete --

async function archiveItem(id, name) {
  try {
    await API.archiveItem(id);
    renderInventory();
    updateLowStockBanner();
    showToast('"' + name + '" archived. You can restore it below.', "warning");
  } catch (err) {
    showToast(err.message || "Failed to archive item.", "danger");
  }
}

async function restoreItem(id, name) {
  try {
    await API.restoreItem(id);
    renderInventory();
    updateLowStockBanner();
    showToast('"' + name + '" restored to inventory!', "success");
  } catch (err) {
    showToast(err.message || "Failed to restore item.", "danger");
  }
}

async function restoreAllItems(count) {
  if (!count) return;
  try {
    await API.restoreAllItems();
    renderInventory();
    updateLowStockBanner();
    showToast(count + " item" + (count !== 1 ? "s" : "") + " restored to inventory!", "success");
  } catch (err) {
    showToast(err.message || "Failed to restore items.", "danger");
  }
}

async function deleteArchivedItem(id, name) {
  confirmDelete(
    'Permanently delete "' + name + '"? This cannot be undone.',
    async function () {
      try {
        await API.deleteArchivedItem(id);
        renderInventory();
        showToast('"' + name + '" permanently deleted.', "danger");
        deleteModalInstance.hide();
      } catch (err) {
        showToast(err.message || "Failed to delete item.", "danger");
        deleteModalInstance.hide();
      }
    }
  );
}

async function deleteAllArchivedItems(count) {
  if (!count) return;
  confirmDelete(
    'Permanently delete all ' + count + ' archived item' + (count !== 1 ? "s" : "") + '? This cannot be undone.',
    async function () {
      try {
        await API.deleteAllArchivedItems();
        renderInventory();
        showToast("All archived items permanently deleted.", "danger");
        deleteModalInstance.hide();
      } catch (err) {
        showToast(err.message || "Failed.", "danger");
        deleteModalInstance.hide();
      }
    }
  );
}
