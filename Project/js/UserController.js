"use strict";

// -- Modals --

function openAddUserModal() {
  $("#userEditId").val("");
  $("#userName, #userEmail, #userPassword").val("");
  $("#userRole").val("");
  $(".is-invalid").removeClass("is-invalid");
  $("#userModalTitle").text("Add User");
  userModalInstance.show();
}

function openEditUserModal(id, userData) {
  $("#userEditId").val(userData.id);
  $("#userName").val(userData.name);
  $("#userEmail").val(userData.email);
  $("#userRole").val(userData.role);
  $("#userPassword").val(userData.password || "");
  $(".is-invalid").removeClass("is-invalid");
  $("#userModalTitle").text("Edit User");
  userModalInstance.show();
}

// -- Save (create or update) --

async function saveUser() {
  const id       = $("#userEditId").val();
  const name     = $("#userName").val().trim();
  const email    = $("#userEmail").val().trim();
  const role     = $("#userRole").val();
  const password = $("#userPassword").val();

  let valid = true;
  if (!name)                                    { $("#userName").addClass("is-invalid");     valid = false; }
  if (!email || !email.endsWith("@login.com"))  { $("#userEmail").addClass("is-invalid");    valid = false; }
  if (!role)                                    { $("#userRole").addClass("is-invalid");     valid = false; }
  if (!password || password.length < 6)         { $("#userPassword").addClass("is-invalid"); valid = false; }
  if (!valid) return;

  $(".is-invalid").removeClass("is-invalid");

  try {
    if (id) {
      await API.updateUser({ id, name, email, role, password });
      showToast("User updated!", "success");
    } else {
      await API.createUser({ name, email, role, password });
      showToast("User added!", "success");
    }
    userModalInstance.hide();
    renderUsers();
  } catch (err) {
    showToast(err.message || "Failed to save user.", "danger");
    if (err.message && err.message.toLowerCase().includes("email")) {
      $("#userEmail").addClass("is-invalid");
    }
  }
}

// -- Delete --

async function deleteUser(id, name) {
  if (currentUser && currentUser.id === id) {
    showToast("You cannot delete your own account.", "danger");
    return;
  }

  confirmDelete(
    'Delete user "' + name + '"? This cannot be undone.',
    async function () {
      try {
        await API.deleteUser(id);
        renderUsers();
        showToast("User deleted.", "warning");
        deleteModalInstance.hide();
      } catch (err) {
        showToast(err.message || "Failed to delete user.", "danger");
        deleteModalInstance.hide();
      }
    }
  );
}
