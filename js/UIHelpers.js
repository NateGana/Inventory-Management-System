"use strict";

// Bootstrap modal instances — set in main.js
let pinModalInstance    = null;
let itemModalInstance   = null;
let txModalInstance     = null;
let userModalInstance   = null;
let deleteModalInstance = null;

let pendingDeleteFn = null;
let pinUnlocked     = false;

// Current logged-in user (set after login)
let currentUser = null;

// ---- Toast ----

function showToast(msg, type) {
  type = type || "success";
  const colorMap = {
    success: "bg-success text-white",
    danger:  "bg-danger text-white",
    warning: "bg-warning text-dark",
    info:    "bg-info text-white"
  };
  const $toast = $("#appToast");
  $toast.removeClass("bg-success bg-danger bg-warning bg-info text-white text-dark")
        .addClass(colorMap[type] || colorMap.success);
  $("#toastMsg").text(msg);
  bootstrap.Toast.getOrCreateInstance(
    document.getElementById("appToast"), { delay: 3000 }
  ).show();
}

// ---- Tab switching ----

function switchTab(tabName) {
  $(".tab-section").addClass("d-none");
  $("#tab-" + tabName).removeClass("d-none");
  $(".nav-link").removeClass("active");
  $('[data-tab="' + tabName + '"]').addClass("active");

  if (tabName === "dashboard")    renderDashboard();
  if (tabName === "inventory")    renderInventory();
  if (tabName === "transactions") renderTransactions();
  if (tabName === "reports")      renderReports();
  if (tabName === "users")        renderUsers();
}

// ---- Low stock banner ----

async function updateLowStockBanner() {
  try {
    const items = await API.getItems();
    const lowItems = items.filter(i => parseInt(i.stock) <= parseInt(i.threshold));
    if (lowItems.length > 0) {
      const names = lowItems.map(i => i.name + " (" + i.stock + ")");
      $("#lowStockList").text(names.join(", "));
      $("#lowStockBanner").removeClass("d-none");
    } else {
      $("#lowStockBanner").addClass("d-none");
    }
  } catch (e) { /* silent */ }
}

// ---- Delete confirm modal ----

function confirmDelete(message, callback) {
  $("#deleteMessage").text(message);
  pendingDeleteFn = callback;
  deleteModalInstance.show();
}

// ---- Formatting ----

function formatPeso(amount) {
  return "\u20B1" + parseFloat(amount).toLocaleString("en-PH", { minimumFractionDigits: 2 });
}

function formatDate(dateStr) {
  if (!dateStr) return "—";
  // MySQL returns "YYYY-MM-DD HH:MM:SS" — convert to JS Date
  const d = new Date(dateStr.replace(" ", "T"));
  return d.toLocaleDateString("en-PH", { year: "numeric", month: "short", day: "numeric" });
}

// ---- Badges ----

function roleBadge(role) {
  const map = {
    Admin:    "role-badge-admin",
    Staff:    "role-badge-staff",
    Supplier: "role-badge-supplier"
  };
  return '<span class="status-badge ' + (map[role] || "bg-secondary") + '">' + role + '</span>';
}

function statusBadge(status) {
  const map = {
    "In Stock":     "status-ok",
    "Low Stock":    "status-low",
    "Out of Stock": "status-out"
  };
  return '<span class="status-badge ' + (map[status] || "") + '">' + status + '</span>';
}

function emptyStateHTML(icon, message) {
  return '<div class="empty-state"><i class="' + icon + '"></i><p>' + message + '</p></div>';
}

// ---- Role-based access ----

function applyRolePermissions(user) {
  const role = user.role;
  $("#addItemBtn, #addSaleBtn, #addRestockBtn").show();
  $('[data-tab="reports"]').closest("li").show();
  $("body").attr("data-role", role);

  if (role === "Admin" || role === "Staff" || role === "Mod") return;

  if (role === "Supplier") {
    $("#addItemBtn").hide();
    $("#addSaleBtn").hide();
    $('[data-tab="reports"]').closest("li").hide();
  }
}

function showWelcomeBanner(user) {
  const cfg = {
    Admin:    { cls: "bg-primary",  icon: "fa-crown",    hint: "" },
    Staff:    { cls: "bg-success",  icon: "fa-user-tie", hint: "Access: Inventory, Transactions &amp; Reports" },
    Supplier: { cls: "bg-warning",  icon: "fa-truck",    hint: "Restricted: View Inventory &amp; Restock only" }
  }[user.role] || { cls: "bg-secondary", icon: "fa-user", hint: "" };

  const hintHtml = cfg.hint
    ? '<span class="ms-3 small opacity-75"><i class="fas fa-info-circle me-1"></i>' + cfg.hint + '</span>'
    : "";

  $("#welcomeBanner")
    .removeClass("bg-primary bg-success bg-warning bg-secondary d-none")
    .addClass(cfg.cls)
    .html(
      '<div class="d-flex align-items-center justify-content-center gap-2 py-2 px-4">' +
        '<i class="fas ' + cfg.icon + '"></i>' +
        '<span>Welcome, <strong>' + user.role + '</strong> &mdash; ' + user.name + '</span>' +
        hintHtml +
      '</div>'
    )
    .show();

  setTimeout(function () {
    $("#welcomeBanner").fadeOut(600, function () { $(this).addClass("d-none"); });
  }, 5000);
}
