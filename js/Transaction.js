"use strict";

class Transaction {
  constructor(id, itemId, itemName, type, qty, price, notes, userId, userName, date) {
    this.id       = id;
    this.itemId   = itemId;
    this.itemName = itemName;
    this.type     = type;
    this.qty      = parseInt(qty);
    this.price    = parseFloat(price);
    this.amount   = this.qty * this.price; // auto-calculated
    this.notes    = notes    || "";
    this.userId   = userId;
    this.userName = userName || "System";
    this.date     = date     || new Date().toISOString();
  }

  toObject() {
    return {
      id: this.id, itemId: this.itemId, itemName: this.itemName,
      type: this.type, qty: this.qty, price: this.price,
      amount: this.amount, notes: this.notes,
      userId: this.userId, userName: this.userName, date: this.date
    };
  }

  static fromObject(obj) {
    return new Transaction(
      obj.id, obj.itemId, obj.itemName, obj.type,
      obj.qty, obj.price, obj.notes,
      obj.userId, obj.userName, obj.date
    );
  }
}
