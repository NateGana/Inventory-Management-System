"use strict";

// -- Login --

async function doLogin() {
  const email    = $("#loginEmail").val().trim();
  const password = $("#loginPassword").val();

  if (!email || !email.endsWith("@login.com")) {
    showLoginError("Email must use @login.com domain.");
    return;
  }
  if (!password) {
    showLoginError("Please enter your password.");
    return;
  }

  try {
    const result = await API.login(email, password);
    currentUser = result.user;
    $("#loginError").addClass("d-none");
    $("#loginPage").addClass("d-none");
    $("#mainApp").removeClass("d-none").hide().fadeIn(400);
    initMainApp(currentUser);
  } catch (err) {
    showLoginError(err.message || "Invalid email or password.");
  }
}

function showLoginError(msg) {
  const $err = $("#loginError");
  $err.text(msg).removeClass("d-none").hide().slideDown(250);
  $("#loginEmail, #loginPassword").addClass("is-invalid");
  setTimeout(function () {
    $("#loginEmail, #loginPassword").removeClass("is-invalid");
  }, 2500);
}

// -- Logout --

async function doLogout() {
  await API.logout();
  currentUser = null;
  pinUnlocked = false;
  $("body").removeAttr("data-role");
  $("#navUsersTab").addClass("d-none");
  $("#mainApp").addClass("d-none");
  $("#loginEmail, #loginPassword").val("");
  $("#loginError").addClass("d-none");
  $("#loginPage").removeClass("d-none").hide().fadeIn(400);
}

// -- PIN modal --

function openPinModal() {
  if (pinUnlocked) {
    switchTab("users");
    return;
  }
  $("#pin1, #pin2, #pin3, #pin4").val("");
  $("#pinError").addClass("d-none");
  pinModalInstance.show();
  $("#pinModal").one("shown.bs.modal", function () {
    $("#pin1").focus();
  });
}

async function checkPin() {
  const entered = $("#pin1").val() + $("#pin2").val() +
                  $("#pin3").val() + $("#pin4").val();

  try {
    await API.verifyPin(entered);
    pinUnlocked = true;

    if (!currentUser) {
      // PIN access from login page
      $("#pinModal").one("hidden.bs.modal", function () {
        $("#loginPage").addClass("d-none");
        $("#navUserName").text("Mod (PIN)");
        $("#navUserRole").text("Moderator Access");
        applyRolePermissions({ role: "Admin" });
        $("#navUsersTab").removeClass("d-none");
        $("#mainApp").removeClass("d-none").hide().fadeIn(400, function () {
          showToast("PIN accepted. Welcome to User Management!", "success");
          switchTab("users");
        });
      });
      pinModalInstance.hide();
    } else {
      pinModalInstance.hide();
      showToast("PIN accepted. Welcome to User Management!", "success");
      setTimeout(function () { switchTab("users"); }, 400);
    }

  } catch (err) {
    $("#pinError").removeClass("d-none").hide().slideDown(200);
    $("#pin1, #pin2, #pin3, #pin4").val("");
    $("#pin1").focus();
    showToast("Incorrect PIN! Access denied.", "danger");
  }
}

// -- Post-login setup --

function initMainApp(user) {
  currentUser = user;
  $("#navUserName").text(user.name);
  $("#navUserRole").text(user.role);
  pinUnlocked = false;
  applyRolePermissions(user);
  showWelcomeBanner(user);
  switchTab("dashboard");
  updateLowStockBanner();
}
