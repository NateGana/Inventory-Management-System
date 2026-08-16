# Dabao Plus — PHP + MySQL 
### Setup Guide for XAMPP

---

## 1. Start XAMPP

Open XAMPP Control Panel and start both **Apache** and **MySQL**.

---

## 2. Import the Database

1. Open your browser and go to: **http://localhost/phpmyadmin**
2. Click **Import** in the top menu bar.
3. Click **Choose File** and select `dabaoplus.sql` (in this folder).
4. Scroll down and click **Go**.
5. You should see: *"Import has been successfully finished"*

This creates the `dabaoplus` database with all tables and demo data.

---

## 3. Copy Files to XAMPP

Copy the entire **`dabaoplus/`** folder into your XAMPP `htdocs` directory:

| OS      | Path                              |
|---------|-----------------------------------|
| Windows | `C:\xampp\htdocs\dabaoplus\`      |
| Mac     | `/Applications/XAMPP/htdocs/dabaoplus/` |
| Linux   | `/opt/lampp/htdocs/dabaoplus/`    |

Your final folder structure should look like:

```
htdocs/
└── dabaoplus/
    ├── index.php          ← main app entry point
    ├── dabaoplus.sql      ← database import file
    ├── includes/
    │   ├── db.php         ← database connection
    │   └── auth.php       ← session helpers
    ├── api/
    │   ├── auth.php       ← login / logout / session / PIN
    │   ├── items.php      ← inventory CRUD
    │   ├── transactions.php
    │   ├── users.php
    │   ├── dashboard.php
    │   └── reports.php
    ├── css/
    │   └── style.css
    └── js/
        ├── api.js          ← replaces localStorage Store
        ├── UIHelpers.js
        ├── RenderDashboard.js
        ├── RenderInventory.js
        ├── RenderTransactions.js
        ├── RenderReports.js
        ├── RenderUsers.js
        ├── AuthController.js
        ├── InventoryController.js
        ├── TransactionController.js
        ├── UserController.js
        ├── main.js
        ├── InventoryItem.js
        ├── Transaction.js
        └── User.js
```

---

## 4. (Optional) Change Database Credentials

If your MySQL has a password set, open `includes/db.php` and update:

```php
define('DB_USER', 'root');
define('DB_PASS', '');   // ← put your password here
```

---

## 5. Open the App

Go to: **http://localhost/dabaoplus/**

---

## Demo Login Accounts

| Email                      | Password  | Role     |
|----------------------------|-----------|----------|
| demoadmin@login.com        | admin123  | Admin    |
| demostaff@login.com        | staff123  | Staff    |
| demosupplier@login.com     | sup123    | Supplier |

**Manage Accounts PIN:** `5678`

---

## What Changed from the Original

| Before (localStorage)       | After (PHP + MySQL)                     |
|-----------------------------|------------------------------------------|
| `Store.js` + localStorage   | `api.js` → PHP API endpoints → MySQL    |
| `SeedData.js` (JS seed)     | `dabaoplus.sql` (SQL seed via phpMyAdmin)|
| Client-side session         | PHP `$_SESSION` (server-side)            |
| All data lost on clear cache| Data persists in MySQL permanently       |

---

## Notes

- Passwords are stored in plain text to match the original app's behavior.
  For production use, replace with `password_hash()` / `password_verify()`.
- The PIN (`5678`) is hardcoded in `api/auth.php` — change it there if needed.
