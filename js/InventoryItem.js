"use strict";

class InventoryItem {
  constructor(id, sku, name, category, price, stock, threshold, supplier, description, createdAt) {
    this.id          = id;
    this.sku         = sku;
    this.name        = name;
    this.category    = category;
    this.price       = parseFloat(price);
    this.stock       = parseInt(stock);
    this.threshold   = parseInt(threshold) || 10;
    this.supplier    = supplier    || "";
    this.description = description || "";
    this.createdAt   = createdAt   || new Date().toISOString();
  }

  // "In Stock" | "Low Stock" | "Out of Stock"
  getStatus() {
    if (this.stock === 0)             return "Out of Stock";
    if (this.stock <= this.threshold) return "Low Stock";
    return "In Stock";
  }

  restock(qty) {
    this.stock += parseInt(qty);
  }

  // Throws if qty exceeds available stock
  sell(qty) {
    const q = parseInt(qty);
    if (q > this.stock) throw new Error(`Not enough stock. Available: ${this.stock}`);
    this.stock -= q;
  }

  toObject() {
    return {
      id: this.id, sku: this.sku, name: this.name,
      category: this.category, price: this.price, stock: this.stock,
      threshold: this.threshold, supplier: this.supplier,
      description: this.description, createdAt: this.createdAt
    };
  }

  static fromObject(obj) {
    return new InventoryItem(
      obj.id, obj.sku, obj.name, obj.category,
      obj.price, obj.stock, obj.threshold,
      obj.supplier, obj.description, obj.createdAt
    );
  }
}
