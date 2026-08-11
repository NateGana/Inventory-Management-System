"use strict";

class User {
  constructor(id, name, email, password, role, createdAt) {
    this.id        = id;
    this.name      = name;
    this.email     = email;
    this.password  = password;
    this.role      = role;
    this.createdAt = createdAt || new Date().toISOString();
  }

  toObject() {
    return {
      id: this.id, name: this.name, email: this.email,
      password: this.password, role: this.role, createdAt: this.createdAt
    };
  }

  static fromObject(obj) {
    return new User(obj.id, obj.name, obj.email, obj.password, obj.role, obj.createdAt);
  }
}
