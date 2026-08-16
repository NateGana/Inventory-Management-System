<div align="center">

# 🥡 Dabao Plus

**Full-Stack Inventory & Transaction Management System**

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=apachefriends&logoColor=white)](https://www.apachefriends.org/)

<p align="center">
  <b>Welcome to Dabao Plus!</b> This repository contains a complete, database-backed inventory management system. Feel free to clone, set up locally on XAMPP, and explore the features!
</p>

</div>

---

## 🚀 Quick Setup Guide (XAMPP)

Follow these simple steps to run **Dabao Plus** on your local machine:

### 1. Start XAMPP
Open your **XAMPP Control Panel** and start both **Apache** and **MySQL**.

### 2. Import the Database
1. Open your browser and go to `http://localhost/phpmyadmin`
2. Click **Import** in the top navigation bar.
3. Click **Choose File** and select `dabaoplus.sql` (located in the root of this repository).
4. Scroll to the bottom and click **Go**.
5. Once complete, you will see *"Import has been successfully finished"*. This creates the `dabaoplus` database populated with all required tables and demo data.

### 3. Move Files to `htdocs`
Copy the entire `dabaoplus/` folder into your XAMPP `htdocs` directory according to your OS:

| Operating System | Default `htdocs` Path |
| :--- | :--- |
| **Windows** | `C:\xampp\htdocs\dabaoplus\` |
| **macOS** | `/Applications/XAMPP/htdocs/dabaoplus/` |
| **Linux** | `/opt/lampp/htdocs/dabaoplus/` |

#### 📂 Project Directory Structure
Ensure your folder layout matches the following setup:

```text
htdocs/
└── dabaoplus/
    ├── index.php          ← Main application entry point
    ├── dabaoplus.sql      ← Database import file
    ├── includes/
    │   ├── db.php         ← Database connection configuration
    │   └── auth.php       ← Session helper utilities
    ├── api/
    │   ├── auth.php       ← Authentication, session & PIN verification
    │   ├── items.php      ← Inventory CRUD operations
    │   ├── transactions.php
    │   ├── users.php
    │   ├── dashboard.php
    │   └── reports.php
    ├── css/
    │   └── style.css
    └── js/
        ├── api.js          ← API adapter (replaces old localStorage Store)
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
