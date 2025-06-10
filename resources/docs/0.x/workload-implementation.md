# Workload Implementation

In this step we try to run the workload queries againt the MongoDB. 

## Query 1
Retrieve all B2B customers.

```javascript
db.customers.find({ type: "B2B" }).pretty();
```

<div class="rounded-md" style="background-color: #1f2937; border-radius: 0.375rem;">
    <img src="/images/queries/query-1.png" alt="Query 1 Result" class="w-full p-3" />
</div>

<p class="text-center my-0">
    <a href="/images/queries/query-1.png" target="_blank" class="underlined">Click to view full size </a>
</p>


## Query 2

Retrieve orders with their details by B2C typed customers

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
  { $unwind: "$customer" },
  { $match: { "customer.type": "B2C" } }
])
```

<div class="rounded-md" style="background-color: #1f2937; border-radius: 0.375rem;">
    <img src="/images/queries/query-2.png" alt="Query 1 Result" class="w-full p-3" />
</div>

<p class="text-center my-0">
    <a href="/images/queries/query-2.png" target="_blank" class="underlined">Click to view full size </a>
</p>


## Query 3

Given a period of dates, retrieve the details of 5 most sold products.

```javascript
db.orders.aggregate([{
    $match: {
      date: {
        $gte: "2022-01-01",
        $lte: "2022-06-01"
      }
    }
  },
  {
    $unwind: "$products"
  },
  {
    $group: {
      _id: "$products.product_id",
      totalSold: {
        $sum: "$products.quantity"
      }
    }
  },
  {
    $sort: {
      totalSold: -1
    }
  },
  {
    $limit: 5
  },
  {
    $lookup: {
      from: "products",
      localField: "_id",
      foreignField: "product_id",
      as: "product_details"
    }
  },
  {
    $unwind: "$product_details"
  },
  {
    $project: {
      product_id: "$_id",
      name: "$product_details.name",
      totalSold: 1
    }
  }
])
```

<div class="rounded-md" style="background-color: #1f2937; border-radius: 0.375rem;">
    <img src="/images/queries/query-3.png" alt="Query 1 Result" class="w-full p-3" />
</div>

<p class="text-center my-0">
    <a href="/images/queries/query-3.png" target="_blank" class="underlined">Click to view full size </a>
</p>


## Query 4

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
])
```

<div class="rounded-md" style="background-color: #1f2937; border-radius: 0.375rem;">
    <img src="/images/queries/query-4.png" alt="Query 1 Result" class="w-full p-3" />
</div>

<p class="text-center my-0">
    <a href="/images/queries/query-4.png" target="_blank" class="underlined">Click to view full size </a>
</p>



## Query 5

Retrieve all products that have orders with discount more than 10%.

```javascript
db.orders.aggregate([
  { $unwind: "$products" },
  { $match: { "products.discount": { $gt: 0.10 } } },
  {
    $lookup: {
      from: "products",
      localField: "products.product_id",
      foreignField: "product_id",
      as: "product_details"
    }
  },
  {
    $project: {
      _id: 0,
      product_id: "$products.product_id",
      name: { $arrayElemAt: ["$product_details.name", 0] },
      discount: "$products.discount"
    }
  }
])
```

<div class="rounded-md" style="background-color: #1f2937; border-radius: 0.375rem;">
    <img src="/images/queries/query-5.png" alt="Query 1 Result" class="w-full p-3" />
</div>

<p class="text-center my-0">
    <a href="/images/queries/query-5.png" target="_blank" class="underlined">Click to view full size </a>
</p>



## Query 6

Retrieve all products that have orders with discount more than 10%.

```javascript
db.products.aggregate([
  { 
    $match: { name: "Fanta" } 
  },
  { 
    $lookup: { 
      from: "customers", 
      localField: "customers", 
      foreignField: "customer_id", 
      as: "fanta_customers" 
    } 
  },
  { 
    $unwind: "$fanta_customers" 
  },
  { 
    $project: { 
      _id: 0, 
      customer_name: "$fanta_customers.name", 
      customer_type: "$fanta_customers.type" 
    } 
  }
])
```

<div class="rounded-md" style="background-color: #1f2937; border-radius: 0.375rem;">
    <img src="/images/queries/query-6.png" alt="Query 1 Result" class="w-full p-3" />
</div>

<p class="text-center my-0">
    <a href="/images/queries/query-6.png" target="_blank" class="underlined">Click to view full size </a>
</p>


## Query 6

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
        $lt: new Date(new Date().getTime() - 6 * 30 * 24 * 60 * 60 * 1000) // approx. 6 months ago
      }
    }
  },
  {
    $project: {
      _id: 0,
      name: 1,
      customer_id: "$customer_id"
    }
  }
])

```

<div class="rounded-md" style="background-color: #1f2937; border-radius: 0.375rem;">
    <img src="/images/queries/query-7.png" alt="Query 1 Result" class="w-full p-3" />
</div>

<p class="text-center my-0">
    <a href="/images/queries/query-7.png" target="_blank" class="underlined">Click to view full size </a>
</p>

## Query 8

Retrieve the number of orders for each region within **2022-08-24** and **2023-08-24**.

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
  },
  {
    $lookup: {
      from: "regions",
      localField: "_id",
      foreignField: "region_id",
      as: "region"
    }
  },
  {
    $unwind: "$region"
  },
  {
    $project: {
      _id: 0,
      region_id: "$_id",
      region_name: "$region.name",
      order_count: 1
    }
  }
])
```

<div class="rounded-md" style="background-color: #1f2937; border-radius: 0.375rem;">
    <img src="/images/queries/query-8.png" alt="Query 1 Result" class="w-full p-3" />
</div>
<p class="text-center my-0">
    <a href="/images/queries/query-8.png" target="_blank" class="underlined">Click to view full size </a>
</p>

## Query 9

Retrieve all **Soft Drinks** products. 

```javascript
db.categories.aggregate([
  {
    $match: {
      name: "Soft Drinks"
    }
  },
  {
    $lookup: {
      from: "products",
      localField: "products",
      foreignField: "product_id",
      as: "soft_drink_products"
    }
  },
  {
    $unwind: "$soft_drink_products"
  },
  {
    $replaceWith: {
      name: "$soft_drink_products.name",
      product_id: "$soft_drink_products.product_id"
    }
  }
])
```

<div class="rounded-md" style="background-color: #1f2937; border-radius: 0.375rem;">
    <img src="/images/queries/query-9.png" alt="Query 1 Result" class="w-full p-3" />
</div>
<p class="text-center my-0">
    <a href="/images/queries/query-9.png" target="_blank" class="underlined">Click to view full size </a>
</p>
