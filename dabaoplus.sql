-- ============================================================
--  Dabao Plus — MySQL Database Schema + Seed Data
--  Import this via phpMyAdmin: Import tab > Choose File
-- ============================================================

CREATE DATABASE IF NOT EXISTS dabaoplus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dabaoplus;

-- ---- Users ----
CREATE TABLE IF NOT EXISTS users (
  id         VARCHAR(60)  PRIMARY KEY,
  name       VARCHAR(120) NOT NULL,
  email      VARCHAR(120) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  role       ENUM('Admin','Staff','Supplier') NOT NULL DEFAULT 'Staff',
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---- Inventory Items ----
CREATE TABLE IF NOT EXISTS inventory_items (
  id          VARCHAR(60)    PRIMARY KEY,
  sku         VARCHAR(60)    NOT NULL UNIQUE,
  name        VARCHAR(200)   NOT NULL,
  category    ENUM('Electronics','Clothing','Food','Office','Hardware') NOT NULL,
  price       DECIMAL(12,2)  NOT NULL DEFAULT 0,
  stock       INT            NOT NULL DEFAULT 0,
  threshold   INT            NOT NULL DEFAULT 10,
  supplier    VARCHAR(200)   DEFAULT '',
  description TEXT           DEFAULT '',
  is_archived TINYINT(1)     NOT NULL DEFAULT 0,
  created_at  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---- Transactions ----
CREATE TABLE IF NOT EXISTS transactions (
  id         VARCHAR(60)   PRIMARY KEY,
  item_id    VARCHAR(60)   NOT NULL,
  item_name  VARCHAR(200)  NOT NULL,
  type       ENUM('Restock','Sale') NOT NULL,
  qty        INT           NOT NULL,
  price      DECIMAL(12,2) NOT NULL,
  amount     DECIMAL(14,2) NOT NULL,
  notes      TEXT          DEFAULT '',
  user_id    VARCHAR(60)   NOT NULL,
  user_name  VARCHAR(120)  NOT NULL,
  created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
--  Seed Data (matches SeedData.js)
-- ============================================================

-- Users (passwords stored as MD5 hashes)
INSERT IGNORE INTO users (id, name, email, password, role, created_at) VALUES
  ('u1', 'Naruto Uzumaki',  'demoadmin@login.com',  '0192023a7bbd73250516f069df18b500', 'Admin',    '2024-01-10 08:00:00'),
  ('u2', 'Levi Ackerman',   'demostaff@login.com',  'de9bf5643eabf80f4a56fda3bbb84483', 'Staff',    '2024-01-15 09:00:00'),
  ('u3', 'Zero Two',        'zerotwo@login.com',    '3b615550a12aafc90faeedf650fa03c9', 'Staff',    '2024-02-01 10:00:00'),
  ('u4', 'Monkey D. Luffy', 'demosupplier@login.com','cb17bd2285f26466a477579632350588', 'Supplier', '2024-02-10 08:30:00'),
  ('u5', 'Mikasa Ackerman', 'mikasa@login.com',     'b5a2fa983c03e9d006eb8da3dbad6104', 'Admin',    '2024-03-01 07:00:00');

-- Inventory Items
INSERT IGNORE INTO inventory_items (id, sku, name, category, price, stock, threshold, supplier, description, created_at) VALUES
  ('i1','ELEC-001','Wireless Mouse',  'Electronics',  850.00, 45, 10,'TechSupply PH', 'Bluetooth 5.0',        '2024-01-12 08:00:00'),
  ('i2','ELEC-002','USB-C Hub',       'Electronics', 1250.00,  8,  5,'TechSupply PH', '7-in-1 USB-C hub',     '2024-01-13 09:00:00'),
  ('i3','CLO-001', 'Polo Shirt',      'Clothing',     450.00,120, 20,'Fashion Depot', 'Dry-fit polo',         '2024-01-14 10:00:00'),
  ('i4','FOO-001', 'Instant Noodles', 'Food',          25.00,  6, 10,'PrimeFoods Inc','12-pack ramen',        '2024-01-15 11:00:00'),
  ('i5','OFF-001', 'Ballpen Set',     'Office',        90.00,200, 30,'OfficeWorld',   '12pcs black ballpens', '2024-01-16 12:00:00'),
  ('i6','HRD-001', 'Hammer',          'Hardware',     350.00, 30, 10,'BuildRight PH', '16oz claw hammer',     '2024-01-17 13:00:00'),
  ('i7','ELEC-003','HDMI Cable 2m',   'Electronics',  380.00,  3,  5,'TechSupply PH', '4K HDMI 2.0 cable',    '2024-01-18 14:00:00'),
  ('i8','OFF-002', 'Stapler',         'Office',       145.00, 50, 10,'OfficeWorld',   'Heavy duty stapler',   '2024-01-19 15:00:00');

-- Transactions
INSERT IGNORE INTO transactions (id, item_id, item_name, type, qty, price, amount, notes, user_id, user_name, created_at) VALUES
  ('t1','i1','Wireless Mouse', 'Restock',20, 850.00,17000.00,'Initial stock',   'u1','Naruto Uzumaki',  '2024-01-20 08:00:00'),
  ('t2','i3','Polo Shirt',     'Sale',    5, 450.00, 2250.00,'Walk-in customer','u2','Levi Ackerman',   '2024-01-21 09:30:00'),
  ('t3','i4','Instant Noodles','Restock',50,  25.00, 1250.00,'Weekly reorder',  'u4','Monkey D. Luffy', '2024-01-22 10:00:00'),
  ('t4','i2','USB-C Hub',      'Sale',    2,1250.00, 2500.00,'Online order',    'u2','Levi Ackerman',   '2024-01-23 11:00:00'),
  ('t5','i7','HDMI Cable 2m',  'Sale',    1, 380.00,  380.00,'Direct sale',     'u3','Zero Two',        '2024-01-24 14:00:00');
