# Logical Schema

In this step, we try to create the logical schema for the MongoDB database for our system.

## Collection's Creation

First we create the individual collections for each of the entities in our system. We will use the following collections:

### Order Collection

Based on the step 6 [MongoDB Design](/docs/0.x/mongodb-design), we will create the `orders` collection with the following fields:

```javascript
db.createCollection("orders", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["order_id", "date", "total_price", "products", "customer", "region"],
      properties: {
        order_id: { bsonType: "int" },
        date: { bsonType: "date" },
        total_price: { bsonType: ["double", "decimal"] },
        products: {
          bsonType: "array",
          items: {
            bsonType: "object",
            required: ["product_id", "name", "quantity", "unit_price", "discount"],
            properties: {
              product_id: { bsonType: "int" },
              name: { bsonType: "string" },
              quantity: { bsonType: "int" },
              unit_price: { bsonType: ["double", "decimal"] },
              discount: { bsonType: "int" }
            }
          }
        },
        customer: {
          bsonType: "object",
          required: ["customer_id", "name", "type"],
          properties: {
            customer_id: { bsonType: "int" },
            name: { bsonType: "string" },
            type: { bsonType: "string" }
          }
        },
        region: {
          bsonType: "object",
          required: ["name"],
          properties: {
            name: { bsonType: "string" }
          }
        }
      }
    }
  }
});
```


```javascript
db.orders.createIndex(
    { date: 1, "products.discount": 1 },
    { unique: false }
);
```

### Customer Collection
Here is the schema design for the `customers` collection:

```javascript
db.createCollection("customers", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["customer_id", "name", "type", "orders"],
      properties: {
        customer_id: { bsonType: "int" },
        name: { bsonType: "string" },
        type: { bsonType: "string" },
        orders: {
          bsonType: "array",
          items: {
            bsonType: "object",
            required: ["order_id", "total_price", "date"],
            properties: {
              order_id: { bsonType: "int" },
              total_price: { bsonType: ["double", "decimal", "string"] },
              date: { bsonType: "date" }
            }
          }
        }
      }
    }
  }
});
```

```javascript
db.customers.createIndex(
    { type: 1 },
    { unique: false }
);
```

### Product Collection
Here is the schema design for the `products` collection:

```javascript
db.createCollection("products", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["product_id", "name", "customers"],
      properties: {
        product_id: { bsonType: "int" },
        name: { bsonType: "string" },
        customers: {
          bsonType: "array",
          items: {
            bsonType: "object",
            required: ["customer_id", "name", "type"],
            properties: {
              customer_id: { bsonType: "int" },
              name: { bsonType: "string" },
              type: { bsonType: "string" }
            }
          }
        }
      }
    }
  }
});
```


```javascript
db.products.createIndex(
    { "customers.customer_id": 1 }
);
```

### Category Collection
Here is the schema design for the `categories` collection:

```javascript
db.createCollection("categories", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["category_id", "name", "products"],
      properties: {
        category_id: { bsonType: "int" },
        name: { bsonType: "string" },
        products: {
          bsonType: "array",
          items: {
            bsonType: "object",
            required: ["product_id", "name"],
            properties: {
              product_id: { bsonType: "int" },
              name: { bsonType: "string" }
            }
          }
        }
      }
    }
  }
});
```

```javascript
db.categories.createIndex({ category_id: 1 });
db.categories.createIndex({ "products.product_id": 1 });
```

### Region Collection
Here is the schema design for the `regions` collection:

```javascript
db.createCollection("regions", {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["region_id", "name", "customer"],
      properties: {
        region_id: { bsonType: "int" },
        name: { bsonType: "string" },
        customer: {
          bsonType: "array",
          items: {
            bsonType: "object",
            required: ["customer_id"],
            properties: {
              customer_id: { bsonType: "int" }
            }
          }
        }
      }
    }
  }
});
```

```javascript
db.regions.createIndex({ region_id: 1 });
db.regions.createIndex({ "customer.customer_id": 1 });
```
