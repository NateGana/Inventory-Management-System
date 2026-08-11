"use strict";

$(document).ready(async function () {

  // Bootstrap modal instances
  pinModalInstance    = new bootstrap.Modal(document.getElementById("pinModal"));
  itemModalInstance   = new bootstrap.Modal(document.getElementById("itemModal"));
  txModalInstance     = new bootstrap.Modal(document.getElementById("txModal"));
  userModalInstance   = new bootstrap.Modal(document.getElementById("userModal"));
  deleteModalInstance = new bootstrap.Modal(document.getElementById("deleteModal"));

  // Resume session on page refresh (PHP session via cookie)
  try {
    const sessionUser = await API.getSession();
    if (sessionUser) {
      currentUser = sessionUser;
      $("#loginPage").addClass("d-none");
      $("#mainApp").removeClass("d-none");
      initMainApp(sessionUser);
    }
  } catch (e) { /* no session */ }

  // -- Login --

  $("#loginBtn").on("click", function () { doLogin(); });

  $("#loginEmail, #loginPassword").on("keypress", function (e) {
    if (e.which === 13) doLogin();
  });

  $("#loginEmail, #loginPassword").on("input", function () {
    $(this).removeClass("is-invalid");
  });

  $("#toggleLoginPass").on("click", function () {
    const $pwd   = $("#loginPassword");
    const isPass = $pwd.attr("type") === "password";
    $pwd.attr("type", isPass ? "text" : "password");
    $(this).find("i").toggleClass("fa-eye fa-eye-slash");
  });

  // Opens PIN modal from login page
  $("#loginManageAccountsBtn").on("click", function () {
    $("#pin1, #pin2, #pin3, #pin4").val("");
    $("#pinError").addClass("d-none");
    pinModalInstance.show();
    $("#pinModal").one("shown.bs.modal", function () { $("#pin1").focus(); });
  });

  // -- Logout --

  $("#logoutBtn").on("click", function () { doLogout(); });

  // -- Nav tabs --

  $(document).on("click", ".nav-link[data-tab]", function (e) {
    e.preventDefault();
    switchTab($(this).data("tab"));
  });

  // -- PIN modal --

  $(".pin-input").on("input", function () {
    $(this).val($(this).val().replace(/[^0-9]/g, ""));
    if ($(this).val().length === 1) $(this).next(".pin-input").focus();
  });

  $(".pin-input").on("keydown", function (e) {
    if (e.key === "Backspace" && $(this).val() === "") $(this).prev(".pin-input").focus();
  });

  $("#submitPinBtn").on("click", function () { checkPin(); });
  $("#pin4").on("keypress", function (e) { if (e.which === 13) checkPin(); });

  $("#pinModal").on("hidden.bs.modal", function () {
    $("#pin1, #pin2, #pin3, #pin4").val("");
    $("#pinError").addClass("d-none");
  });

  // -- Inventory: active items --

  $("#addItemBtn").on("click", function () { openAddItemModal(); });

  $(document).on("click", ".edit-item-btn", function () {
    const itemData = JSON.parse(decodeURIComponent($(this).data("item")));
    openEditItemModal($(this).data("id"), itemData);
  });

  $(document).on("click", ".archive-item-btn", function () {
    archiveItem($(this).data("id"), $(this).data("name"));
  });

  $("#saveItemBtn").on("click", function () { saveItem(); });

  // Strip spaces live
  $("#itemSku").on("input", function () {
    $(this).val($(this).val().replace(/\s/g, "")).removeClass("is-invalid");
  });

  $("#itemCategory").on("change", function () {
    updateSkuHint($(this).val());
    $(this).removeClass("is-invalid");
  });

  $("#itemModal").on("input change", ".form-control, .form-select", function () {
    $(this).removeClass("is-invalid");
  });

  $("#itemModal").on("hidden.bs.modal", function () {
    $(".is-invalid").removeClass("is-invalid");
    $("#skuHint").text("");
    $("#itemSku").attr("placeholder", "Select a category first");
  });

  // -- Inventory: archive --

  $(document).on("click", ".restore-item-btn", function () {
    restoreItem($(this).data("id"), $(this).data("name"));
  });

  $(document).on("click", ".perm-delete-item-btn", function () {
    deleteArchivedItem($(this).data("id"), $(this).data("name"));
  });

  $("#restoreAllBtn").on("click", function () {
    restoreAllItems(_archiveCount);
  });

  $("#deleteAllArchivedBtn").on("click", function () {
    deleteAllArchivedItems(_archiveCount);
  });

  // -- Inventory: search & filters --

  $("#searchInput").on("input",     function () { renderInventory(); });
  $("#categoryFilter").on("change", function () { renderInventory(); });
  $("#lowStockFilter").on("change", function () { renderInventory(); });

  // -- Transactions --

  $("#addRestockBtn").on("click", function () { openTxModal("Restock"); });
  $("#addSaleBtn").on("click",    function () { openTxModal("Sale"); });
  $("#saveTxBtn").on("click",     function () { saveTransaction(); });

  $("#txModal").on("input change", ".form-control, .form-select", function () {
    $(this).removeClass("is-invalid");
  });

  // -- User management --

  $("#addUserBtn").on("click", function () { openAddUserModal(); });

  $(document).on("click", ".edit-user-btn", function () {
    const userData = JSON.parse(decodeURIComponent($(this).data("user")));
    openEditUserModal($(this).data("id"), userData);
  });

  $(document).on("click", ".del-user-btn", function () {
    deleteUser($(this).data("id"), $(this).data("name"));
  });

  $("#saveUserBtn").on("click", function () { saveUser(); });

  $("#userModal").on("input change", ".form-control, .form-select", function () {
    $(this).removeClass("is-invalid");
  });

  // -- Delete confirm modal --

  $("#confirmDeleteBtn").on("click", function () {
    if (typeof pendingDeleteFn === "function") {
      pendingDeleteFn();
      pendingDeleteFn = null;
    }
  });

  $("#deleteModal").on("hidden.bs.modal", function () { pendingDeleteFn = null; });

});
