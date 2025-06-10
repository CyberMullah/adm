# MongoDB Design

In this step, using the aggregated-oriented design we design the MongoDB schema.

## Schema Design

In the following sections, we will define the MongoDB schema for the collections involved in the queries.

### Order collection

The following queries are asscociated with the `Order` collection:

- Query 3: Selection attributes `[Order(date)_!]`
- Query 5: Selection attributes `[Order_H(discount)]`
- Query 7: Selection attributes `[Order(date)_!]`
- Query 8: Selection attributes `[Order(date)_!]`

Partition key = `{date, discount}` with non-unique index  
`_id` field automatically assigned could be partition key as well. 


```txt
order: {
    _id, date, order_id, total_price,
    has: [
        {
            product:{
                product_id, name,unit_price, discount, quantity
            }
        }
    ],
    placed_by: [customer: {customer_id, name, type}],
    belongs_to: [{region: {name}}]
}

db.orders.createIndex(
    {“date”: 1, "has. product. discount": 1},
    {unique: false}
)
```


### Customer collection
The following queries are associated with the `Customer` collection:

- Query 1: Selection attributes `[Customer(type)_!]`
- Query 2: Selection attributes `[Customer(type)_!]`

Partition key = `{type}` with non-unique index <br>
`_id` field automatically assigned could be partition key as well.


```txt
Customer: {
    _id, customer_id, name, type, 
    places: [
        {
            order: {
                order_id, total_price, date
            }
        }
    ]
}


db.customers.createIndex(
    {“type”: 1}, {unique: false}
) 
```

### Product collection
The following queries are associated with the `Product` collection:

- Query 6: Selection attributes `[Product(name)_!]`

Partition key = `{name}` with unique index <br>
`_id` field automatically assigned could be partition key as well. 


```txt
Product: {
    _id, product_id, name, unit_price, 
    ordered_by: [{customer: {name, type}}]
}

db.products.createIndex(
    {“name”: 1}, {unique: true}
)
```

### Category collection
The following queries are associated with the `Category` collection:

- Query 9: Selection attributes `[Category(name)_!]`

Partition key = `{name}` with unique index <br>
`_id` field automatically assigned could be partition key as well. 

```txt
Category: {
    _id, category_id, name,
    has: [
        {
            products: {product_id, name, unit_price}
        }
    ]
} 

db.categoies.createIndex(
    {“name”: 1}, {unique: true}
)
```


### Region collection
The following queries are associated with the `Region` collection:

- Query 4: Selection attributes `[Region(name)_!]`

Partition key = `{name}` with non-unique index <br>
`_id` field automatically assigned could be partition key as well. 

```txt
Region: {
    _id, region_id, name,
    has: [
        {
            customer: {customer_id}
        }
    ]
}

db.regions.createIndex(
    {“name”: 1}, {unique: true}
)
```

## MongoDB Queries
In this section, we specify each operation of the workload in MongoDB syntax.


### Query 1

Retrieve all B2B customers.

```javascript
db.customers.find(
    { type: "B2B" },
    { _id: 0, customer_id: 1, name: 1 }
)
```

### Query 2
Retrieve orders with their details by B2C typed customers.

```javascript
db.orders.aggregate([
  {
    $lookup: {
      from: "customers",
      localField: "customer_id",
      foreignField: "customer_id",
      as: "customer"
    }
  },
  {
    $unwind: "$customer"
  },
  {
    $match: {
      "customer.type": "B2C"
    }
  }
]);
```

### Query 3

Given a period of dates, retrieve the details of 5 most sold products.

```javascript
db.orders.aggregate([
  {
    $match: {
      date: { $gte: "2022-01-01", $lte: "2022-12-31" }
    }
  },
  { $unwind: "$products" },
  {
    $group: {
      _id: "$products",
      total_sold: { $sum: 1 }
    }
  },
  { $sort: { total_sold: -1 } },
  { $limit: 5 },
  {
    $lookup: {
      from: "products",
      localField: "_id",
      foreignField: "product_id",
      as: "product_details"
    }
  },
  { $unwind: "$product_details" },
  {
    $project: {
      _id: 0,
      product_id: "$_id",
      name: "$product_details.name",
      total_sold: 1
    }
  }
]);
```


### Query 4
Given region "Saarland", retrieve the number of customers.


```javascript
db.regions.aggregate([
  {
    $match: {
      name: "Saarland"
    }
  },
  {
    $project: {
      customer_count: {
        $size: "$customer"
      }
    }
  }
]);
```

### Query 5

Retrieve all products that have orders with discount more than 10%.


```javascript
db.products.aggregate([
  { $match: { name: "Fanta" } },
  {
    $lookup: {
      from: "customers",
      localField: "customers",
      foreignField: "customer_id",
      as: "fanta_customers"
    }
  },
  { $unwind: "$fanta_customers" },
  {
    $project: {
      _id: 0,
      customer_name: "$fanta_customers.name",
      customer_type: "$fanta_customers.type"
    }
  }
]);
```

### Query 6

Name and type of customers that ordered the product “Fanta”.

```javascript
db.products.aggregate([
  { $match: { name: "Fanta" } },
  {
    $lookup: {
      from: "customers",
      localField: "customers",
      foreignField: "customer_id",
      as: "fanta_customers"
    }
  },
  { $unwind: "$fanta_customers" },
  {
    $project: {
      _id: 0,
      customer_name: "$fanta_customers.name",
      customer_type: "$fanta_customers.type"
    }
  }
]);
```

### Query 7

Identify customers who haven’t placed an order in the last 6 months.

```javascript
db.customers.aggregate([
  {
    $lookup: {
      from: "orders",
      localField: "customer_id",
      foreignField: "customer_id",
      as: "customer_orders"
    }
  },
  {
    $addFields: {
      last_order_date: {
        $max: {
          $map: {
            input: "$customer_orders",
            as: "order",
            in: {
              $cond: {
                if: { $gte: [{ $type: "$$order.date" }, "string"] },
                then: { $toDate: "$$order.date" },
                else: "$$order.date"
              }
            }
          }
        }
      }
    }
  },
  {
    $match: {
      last_order_date: {
        $lt: new Date(new Date().getTime() - 6 * 30 * 24 * 60 * 60 * 1000)
      }
    }
  },
  {
    $project: {
      _id: 0,
      customer_name: 1,
      customer_id: "$customer_id"
    }
  }
]);
```

### Query 8
Retrieve the number of orders for each region within 2022-08-24 and 2023-08-24.

```javascript
db.orders.aggregate([
  {
    $match: {
      date: {
        $gte: "2022-08-24",
        $lte: "2023-08-24"
      }
    }
  },
  {
    $group: {
      _id: "$region_id",
      order_count: { $sum: 1 }
    }
  }
]);
```

### Query 9

Retrieve all Soft Drinks products.

```javascript
db.categories.aggregate([
  { $match: { name: "Soft Drinks" } },
  {
    $lookup: {
      from: "products",
      localField: "products",
      foreignField: "product_id",
      as: "soft_drink_products"
    }
  },
  { $unwind: "$soft_drink_products" },
  {
    $replaceWith: {
      name: "$soft_drink_products.name",
      product_id: "$soft_drink_products.product_id"
    }
  }
]);
```
