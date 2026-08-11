<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dabao Plus</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="css/style.css"/>
</head>
<body>

<!-- ================================================
     LOGIN PAGE
     ================================================ -->
<div id="loginPage" class="login-bg d-flex align-items-center justify-content-center min-vh-100 position-relative">

  <div class="position-absolute top-0 end-0 p-3">
    <button class="btn btn-warning btn-sm fw-semibold" id="loginManageAccountsBtn">
      <i class="fas fa-lock me-1"></i>Manage Accounts
    </button>
  </div>

  <div class="login-card card shadow-lg p-4">
    <div class="text-center mb-4">
      <div class="login-logo mb-2"><i class="fas fa-boxes-stacked"></i></div>
      <h4 class="fw-bold">Dabao Plus</h4>
      <p class="text-muted small">Sign in to your account</p>
    </div>
    <div id="loginError" class="alert alert-danger py-2 small d-none"></div>
    <div class="mb-3">
      <label class="form-label fw-semibold">Email Address</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
        <input type="email" id="loginEmail" class="form-control" placeholder="Enter email"/>
      </div>
    </div>
    <div class="mb-4">
      <label class="form-label fw-semibold">Password</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-lock"></i></span>
        <input type="password" id="loginPassword" class="form-control" placeholder="Enter password"/>
        <button class="btn btn-outline-secondary" type="button" id="toggleLoginPass">
          <i class="fas fa-eye"></i>
        </button>
      </div>
    </div>
    <button class="btn btn-primary w-100 fw-semibold" id="loginBtn">
      <i class="fas fa-sign-in-alt me-2"></i>Sign In
    </button>
    <div class="mt-3 p-2 bg-light rounded small text-muted">
      <strong>Demo accounts:</strong><br/>
      demoadmin&#64;login.com / admin123 &nbsp;<span class="badge bg-danger-subtle text-danger">Admin</span><br/>
      demostaff&#64;login.com / staff123 &nbsp;<span class="badge bg-success-subtle text-success">Staff</span><br/>
      demosupplier&#64;login.com / sup123 &nbsp;<span class="badge bg-warning-subtle text-warning-emphasis">Supplier</span><br/>
      <hr class="my-1"/>
      <strong>Manage Accounts PIN (Mod):</strong> 5678
    </div>
  </div>
</div>

<!-- ================================================
     MAIN APP  (hidden until login)
     ================================================ -->
<div id="mainApp" class="d-none">

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow">
    <div class="container-fluid px-4">
      <span class="navbar-brand fw-bold">
        <i class="fas fa-boxes-stacked me-2"></i>Dabao Plus
      </span>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link active" href="#" data-tab="dashboard">
              <i class="fas fa-chart-line me-1"></i>Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" data-tab="inventory">
              <i class="fas fa-warehouse me-1"></i>Inventory
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" data-tab="transactions">
              <i class="fas fa-exchange-alt me-1"></i>Transactions
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" data-tab="reports">
              <i class="fas fa-chart-bar me-1"></i>Reports
            </a>
          </li>
          <li class="nav-item d-none" id="navUsersTab">
            <a class="nav-link" href="#" data-tab="users">
              <i class="fas fa-users me-1"></i>Users
            </a>
          </li>
        </ul>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <div class="text-white small">
            <i class="fas fa-user-circle me-1"></i>
            <span id="navUserName">User</span>
            <span id="navUserRole" class="badge bg-light text-primary ms-1"></span>
          </div>
          <button class="btn btn-outline-light btn-sm" id="logoutBtn">
            <i class="fas fa-sign-out-alt me-1"></i>Logout
          </button>
        </div>
      </div>
    </div>
  </nav>

  <!-- WELCOME BANNER -->
  <div id="welcomeBanner" class="text-white small d-none"></div>

  <!-- LOW STOCK BANNER -->
  <div id="lowStockBanner" class="d-none">
    <div class="container-fluid px-4 py-2 bg-warning-subtle border-bottom border-warning">
      <div class="d-flex align-items-center gap-2 text-warning-emphasis small">
        <i class="fas fa-triangle-exclamation fa-lg"></i>
        <strong>Low Stock Alert:</strong>
        <span id="lowStockList"></span>
      </div>
    </div>
  </div>

  <div class="container-fluid px-4 py-3">

    <!-- ======= DASHBOARD TAB ======= -->
    <div id="tab-dashboard" class="tab-section">
      <div class="section-header mb-4">
        <h5 class="fw-bold mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Dashboard</h5>
        <small class="text-muted">System overview</small>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <div class="stat-card card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="stat-icon bg-primary-subtle text-primary"><i class="fas fa-cubes"></i></div>
              <div><div class="stat-number" id="statTotalItems">—</div><div class="stat-label">Total Items</div></div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="stat-icon bg-success-subtle text-success"><i class="fas fa-peso-sign"></i></div>
              <div><div class="stat-number" id="statTotalValue">—</div><div class="stat-label">Total Value</div></div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="stat-icon bg-danger-subtle text-danger"><i class="fas fa-triangle-exclamation"></i></div>
              <div><div class="stat-number" id="statLowStock">—</div><div class="stat-label">Low Stock</div></div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="stat-icon bg-info-subtle text-info"><i class="fas fa-receipt"></i></div>
              <div><div class="stat-number" id="statTotalTx">—</div><div class="stat-label">Transactions</div></div>
            </div>
          </div>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-0 pt-3">
              <i class="fas fa-triangle-exclamation text-danger me-2"></i>Low Stock Items
            </div>
            <div class="card-body p-0">
              <div class="list-group list-group-flush" id="dashLowStockList">
                <div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Loading…</p></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-0 pt-3">
              <i class="fas fa-clock text-primary me-2"></i>Recent Transactions
            </div>
            <div class="card-body p-0">
              <div class="list-group list-group-flush" id="dashRecentTx">
                <div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Loading…</p></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ======= INVENTORY TAB ======= -->
    <div id="tab-inventory" class="tab-section d-none">
      <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div class="section-header">
          <h5 class="fw-bold mb-0"><i class="fas fa-warehouse me-2 text-primary"></i>Inventory</h5>
          <small class="text-muted" id="itemCount">Loading…</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <button class="btn btn-primary btn-sm fw-semibold" id="addItemBtn">
            <i class="fas fa-plus me-1"></i>Add Item
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
          <div class="row g-2 align-items-center">
            <div class="col-md-5">
              <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Search by name or SKU…"/>
              </div>
            </div>
            <div class="col-md-3">
              <select id="categoryFilter" class="form-select form-select-sm">
                <option value="">All Categories</option>
                <option>Electronics</option><option>Clothing</option>
                <option>Food</option><option>Office</option><option>Hardware</option>
              </select>
            </div>
            <div class="col-md-4">
              <div class="form-check form-switch mb-0 ms-1">
                <input class="form-check-input" type="checkbox" id="lowStockFilter"/>
                <label class="form-check-label small" for="lowStockFilter">Show Low Stock Only</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Active Items Table -->
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th class="ps-3">SKU</th>
                  <th>Product</th>
                  <th>Category</th>
                  <th>Price</th>
                  <th>Stock</th>
                  <th>Status</th>
                  <th class="text-end pe-3">Actions</th>
                </tr>
              </thead>
              <tbody id="inventoryTable">
                <tr><td colspan="7"><div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Loading…</p></div></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Archive Section (Admin only) -->
      <div id="archiveSection" class="d-none">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
          <h6 class="fw-bold text-muted mb-0">
            <i class="fas fa-box-archive me-1"></i>Archived Items
            <span class="badge bg-secondary ms-1 fw-normal" id="archiveCount"></span>
          </h6>
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-success fw-semibold" id="restoreAllBtn">
              <i class="fas fa-rotate-left me-1"></i>Restore All
            </button>
            <button class="btn btn-sm btn-outline-danger fw-semibold" id="deleteAllArchivedBtn">
              <i class="fas fa-trash me-1"></i>Delete All
            </button>
          </div>
        </div>
        <div class="card border-0 shadow-sm">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="ps-3">SKU</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th class="text-end pe-3">Actions</th>
                  </tr>
                </thead>
                <tbody id="archiveTable"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ======= TRANSACTIONS TAB ======= -->
    <div id="tab-transactions" class="tab-section d-none">
      <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div class="section-header">
          <h5 class="fw-bold mb-0"><i class="fas fa-exchange-alt me-2 text-primary"></i>Transactions</h5>
          <small class="text-muted">All stock movements</small>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-success btn-sm fw-semibold" id="addRestockBtn">
            <i class="fas fa-arrow-down me-1"></i>Restock
          </button>
          <button class="btn btn-danger btn-sm fw-semibold" id="addSaleBtn">
            <i class="fas fa-arrow-up me-1"></i>Sale
          </button>
        </div>
      </div>
      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th class="ps-3">ID</th>
                  <th>Date</th>
                  <th>Product</th>
                  <th>Type</th>
                  <th>Qty</th>
                  <th>Amount</th>
                  <th class="pe-3">By</th>
                </tr>
              </thead>
              <tbody id="transactionTable">
                <tr><td colspan="7"><div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Loading…</p></div></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- ======= REPORTS TAB ======= -->
    <div id="tab-reports" class="tab-section d-none">
      <div class="section-header mb-4">
        <h5 class="fw-bold mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Reports</h5>
        <small class="text-muted">Business summary</small>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-success fw-bold fs-4" id="repRevenue">—</div>
            <div class="text-muted small">Total Sales Revenue</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-primary fw-bold fs-4" id="repRestocked">—</div>
            <div class="text-muted small">Total Restocked Value</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm text-center p-3">
            <div class="text-danger fw-bold fs-4" id="repOutOfStock">—</div>
            <div class="text-muted small">Out of Stock Items</div>
          </div>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-0 pt-3">
              <i class="fas fa-tags text-primary me-1"></i> Stock by Category
            </div>
            <div class="card-body p-0">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr><th class="ps-3">Category</th><th>Items</th><th>Total Stock</th></tr>
                </thead>
                <tbody id="repCategoryTable"></tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-0 pt-3">
              <i class="fas fa-trophy text-warning me-1"></i> Top 5 Items by Value
            </div>
            <div class="card-body p-0">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr><th class="ps-3">Product</th><th>Stock</th><th>Total Value</th></tr>
                </thead>
                <tbody id="repTopItems"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ======= USERS TAB ======= -->
    <div id="tab-users" class="tab-section d-none">
      <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div class="section-header">
          <h5 class="fw-bold mb-0"><i class="fas fa-users me-2 text-primary"></i>User Management</h5>
          <small class="text-muted">Manage system accounts</small>
        </div>
        <button class="btn btn-primary btn-sm fw-semibold" id="addUserBtn">
          <i class="fas fa-plus me-1"></i>Add User
        </button>
      </div>

      <!-- User stats -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-5" id="ustatTotal">—</div>
            <div class="text-muted small">Total Users</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-5 text-danger" id="ustatAdmin">—</div>
            <div class="text-muted small">Admins</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-5 text-success" id="ustatStaff">—</div>
            <div class="text-muted small">Staff</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-5 text-warning" id="ustatSupplier">—</div>
            <div class="text-muted small">Suppliers</div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th class="ps-3">Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Created</th>
                  <th class="text-end pe-3">Actions</th>
                </tr>
              </thead>
              <tbody id="userTable">
                <tr><td colspan="5"><div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Loading…</p></div></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /container-fluid -->
</div><!-- /mainApp -->

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
  <div id="appToast" class="toast align-items-center border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body fw-semibold" id="toastMsg"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<!-- PIN Modal -->
<div class="modal fade" id="pinModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-bold"><i class="fas fa-lock me-2 text-primary"></i>Mod PIN</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-3">
        <p class="text-muted small mb-3">Enter the 4-digit PIN to manage accounts</p>
        <div class="d-flex justify-content-center gap-2 mb-3">
          <input type="password" maxlength="1" class="form-control pin-input" id="pin1"/>
          <input type="password" maxlength="1" class="form-control pin-input" id="pin2"/>
          <input type="password" maxlength="1" class="form-control pin-input" id="pin3"/>
          <input type="password" maxlength="1" class="form-control pin-input" id="pin4"/>
        </div>
        <div id="pinError" class="alert alert-danger py-1 small d-none">Incorrect PIN. Try again.</div>
      </div>
      <div class="modal-footer border-0 pt-0 justify-content-center">
        <button class="btn btn-primary btn-sm fw-semibold px-4" id="submitPinBtn">
          <i class="fas fa-unlock me-1"></i>Unlock
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Inventory Item Modal -->
<div class="modal fade" id="itemModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-bold" id="itemModalTitle">Add Inventory Item</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="itemEditId"/>
        <div class="row g-2">
          <div class="col-12">
            <label class="form-label small fw-semibold">Category *</label>
            <select id="itemCategory" class="form-select form-select-sm">
              <option value="">Select category…</option>
              <option>Electronics</option><option>Clothing</option>
              <option>Food</option><option>Office</option><option>Hardware</option>
            </select>
            <div class="invalid-feedback">Category required.</div>
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">SKU *</label>
            <input type="text" id="itemSku" class="form-control form-control-sm" placeholder="Select a category first"/>
            <div class="form-text text-primary small" id="skuHint"></div>
            <div class="invalid-feedback" id="skuFeedback">SKU is required.</div>
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">Product Name *</label>
            <input type="text" id="itemName" class="form-control form-control-sm"/>
            <div class="invalid-feedback">Name is required.</div>
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">Price (&#8369;) *</label>
            <input type="number" id="itemPrice" class="form-control form-control-sm" min="0" step="0.01"/>
            <div class="invalid-feedback">Valid price required.</div>
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">Stock Qty *</label>
            <input type="number" id="itemStock" class="form-control form-control-sm" min="0"/>
            <div class="invalid-feedback">Stock qty required.</div>
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">Low Stock Threshold</label>
            <input type="number" id="itemThreshold" class="form-control form-control-sm" min="1" value="10"/>
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">Supplier</label>
            <input type="text" id="itemSupplier" class="form-control form-control-sm"/>
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">Description</label>
            <textarea id="itemDesc" class="form-control form-control-sm" rows="2"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary btn-sm fw-semibold" id="saveItemBtn">Save Item</button>
      </div>
    </div>
  </div>
</div>

<!-- Transaction Modal -->
<div class="modal fade" id="txModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-bold" id="txModalTitle">New Transaction</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="txType"/>
        <div class="mb-2">
          <label class="form-label small fw-semibold">Product *</label>
          <select id="txProduct" class="form-select form-select-sm">
            <option value="">Select product...</option>
          </select>
          <div class="invalid-feedback">Please select a product.</div>
        </div>
        <div class="mb-2">
          <label class="form-label small fw-semibold">Quantity *</label>
          <input type="number" id="txQty" class="form-control form-control-sm" min="1"/>
          <div class="invalid-feedback">Valid quantity required.</div>
        </div>
        <div class="mb-2">
          <label class="form-label small fw-semibold">Notes</label>
          <input type="text" id="txNotes" class="form-control form-control-sm"/>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary btn-sm fw-semibold" id="saveTxBtn">Save Transaction</button>
      </div>
    </div>
  </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h6 class="modal-title fw-bold" id="userModalTitle">Add User</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="userEditId"/>
        <div class="row g-2">
          <div class="col-12">
            <label class="form-label small fw-semibold">Full Name *</label>
            <input type="text" id="userName" class="form-control form-control-sm"/>
            <div class="invalid-feedback">Name is required.</div>
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">Email *</label>
            <input type="email" id="userEmail" class="form-control form-control-sm"/>
            <div class="invalid-feedback">Valid @login.com email required.</div>
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">Role *</label>
            <select id="userRole" class="form-select form-select-sm">
              <option value="">Select...</option>
              <option>Admin</option><option>Staff</option><option>Supplier</option>
            </select>
            <div class="invalid-feedback">Role is required.</div>
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">Password *</label>
            <input type="password" id="userPassword" class="form-control form-control-sm"/>
            <div class="invalid-feedback">Min 6 characters.</div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary btn-sm fw-semibold" id="saveUserBtn">Save User</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center py-4">
        <div class="mb-3 text-danger fs-1"><i class="fas fa-circle-exclamation"></i></div>
        <h6 class="fw-bold">Confirm Delete</h6>
        <p class="text-muted small" id="deleteMessage">Are you sure?</p>
      </div>
      <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger btn-sm fw-semibold" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- ================================================
     SCRIPTS — load order matters!
     ================================================ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Data models (kept for InventoryItem.getStatus() helper) -->
<script src="js/InventoryItem.js"></script>
<script src="js/Transaction.js"></script>
<script src="js/User.js"></script>

<!-- API layer (replaces Store.js) -->
<script src="js/api.js"></script>

<!-- UI helpers & renderers -->
<script src="js/UIHelpers.js"></script>
<script src="js/RenderDashboard.js"></script>
<script src="js/RenderInventory.js"></script>
<script src="js/RenderTransactions.js"></script>
<script src="js/RenderReports.js"></script>
<script src="js/RenderUsers.js"></script>

<!-- Controllers -->
<script src="js/AuthController.js"></script>
<script src="js/InventoryController.js"></script>
<script src="js/TransactionController.js"></script>
<script src="js/UserController.js"></script>

<!-- Entry point -->
<script src="js/main.js"></script>

</body>
</html>
