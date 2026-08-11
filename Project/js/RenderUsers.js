"use strict";

async function renderUsers() {
  let users;
  try {
    users = await API.getUsers();
  } catch (e) {
    showToast("Failed to load users.", "danger");
    return;
  }

  $("#ustatTotal").text(users.length);
  $("#ustatAdmin").text(users.filter(function(u) { return u.role === "Admin"; }).length);
  $("#ustatStaff").text(users.filter(function(u) { return u.role === "Staff"; }).length);
  $("#ustatSupplier").text(users.filter(function(u) { return u.role === "Supplier"; }).length);

  const $tbody = $("#userTable").empty();

  if (users.length === 0) {
    $tbody.html('<tr><td colspan="5">' + emptyStateHTML("fas fa-users", "No users found") + '</td></tr>');
    return;
  }

  users.forEach(function(user) {
    const isSelf   = currentUser && user.id === currentUser.id;
    const youBadge = isSelf
      ? '<span class="badge bg-primary bg-opacity-10 text-primary ms-1" style="font-size:0.65rem">You</span>'
      : "";
    const deleteBtn = isSelf
      ? ""
      : '<button class="action-btn bg-danger-subtle text-danger del-user-btn" data-id="' + user.id + '" data-name="' + user.name + '" title="Delete"><i class="fas fa-trash"></i></button>';

    const userJson = encodeURIComponent(JSON.stringify(user));

    $tbody.append(
      '<tr>' +
        '<td class="ps-3"><span class="fw-semibold">' + user.name + '</span>' + youBadge + '</td>' +
        '<td class="text-muted">' + user.email + '</td>' +
        '<td>' + roleBadge(user.role) + '</td>' +
        '<td class="text-muted small">' + formatDate(user.created_at) + '</td>' +
        '<td class="text-end pe-3">' +
          '<button class="action-btn bg-primary-subtle text-primary me-1 edit-user-btn" data-id="' + user.id + '" data-user="' + userJson + '" title="Edit"><i class="fas fa-pen"></i></button>' +
          deleteBtn +
        '</td>' +
      '</tr>'
    );
  });
}
