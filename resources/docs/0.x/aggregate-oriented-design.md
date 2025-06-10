# Aggregated Oriented Design

To obtain the aggregated-oriented design, we need to follow these three steps:

## Step 1: Query modeling
In this step, we identify for each query in workload the entity, the attributes of projection and selction. 

### Query 1
Retrieve all B2B customers

- **E** = <span class="text-red-600">Customer</span>
- **LS** = <span class="text-green-500 font-mono">[Customer(type)_!]</span>
- **LP** = <span class="text-blue-500 font-mono">[Customer_!]</span>

```
Q1 → Q1(Customer, [Customer(type)_!], [Customer_!])
```

### Query 2
Retrieve orders with their details by B2C typed customers.

- **E** = <span class="text-red-600">Customer</span>
- **LS** = <span class="text-green-500 font-mono">[Customer(type)_!]</span>
- **LP** = <span class="text-blue-500 font-mono">[Order_P]</span>


```
Q2 → Q2(Customer, [Customer(type)_!], [Order_P])
```

### Query 3
Given a period of dates, retrieve the details of 5 most sold products.

- **E** = <span class="text-red-600">Order</span>
- **LS** = <span class="text-green-500 font-mono">[Order(date)_!]</span>
- **LP** = <span class="text-blue-500 font-mono">[Product_H(quantity)]</span>


```
Q3 → Q3(Order, [Order(date)_!], [Product_H(quantity)])
```

### Query 4
Given region "Saarland", retrieve the number of customers.

- **E** = <span class="text-red-600">Region</span>
- **LS** = <span class="text-green-500 font-mono">[Region(name)_!]</span>
- **LP** = <span class="text-blue-500 font-mono">[Customer(customer_id)_Bt]</span>

```
Q4 → Q4(Region, [Region(region)_!], [Customer(customer_id)_Bt])
```


### Query 5
Retrieve all details of products that have orders with discount more than 10%.

- **E** = <span class="text-red-600">Order</span>
- **LS** = <span class="text-green-500 font-mono">[order_H(discount)]</span>
- **LP** = <span class="text-blue-500 font-mono">[Product_H]</span>

```
Q5 → Q5(Order, [order_H(discount)], [Product_H])
```


### Query 6
Name and type of customers that ordered the product “Fanta”  

- **E** = <span class="text-red-600">Product</span>
- **LS** = <span class="text-green-500 font-mono">[Product(name)_!]</span>
- **LP** = <span class="text-blue-500 font-mono">[Customer(name, type)_PH]</span>

```
Q6 → Q6(Product, [Product(name)_!], [Customer(name, type)_PH])
```

### Query 7
Identify customers who haven’t placed an order in the last 6 months

- **E** = <span class="text-red-600">Order</span>
- **LS** = <span class="text-green-500 font-mono">[Order(date)_!]</span>
- **LP** = <span class="text-blue-500 font-mono">[Customer_P]</span>

```
Q7 → Q7(Order, [Order(date)_!], [Customer_P])
```

### Query 8
Retrieve the number of orders for each region within “2022-08-24”and “2023-08-24”

- **E** = <span class="text-red-600">Order</span>
- **LS** = <span class="text-green-500 font-mono">[Order(date)_!]</span>
- **LP** = <span class="text-blue-500 font-mono">[Order(order_id)_PBT, Region(name)_!]</span>

```
Q8 → Q8(Order, [Order(date)_!], [Order(order_id)_PBT, Region(name)_!])
```

### Query 9
Retrieve all Soft Drinks products.

- **E** = <span class="text-red-600">Category</span>
- **LS** = <span class="text-green-500 font-mono">[Category(name)_!]</span>
- **LP** = <span class="text-blue-500 font-mono">[Product_Bt]</span>

```
Q9 → Q9(Category, [Category(name)_!], [Product_Bt])
```

## Step 2: ER schema annotation
In this step, we annotate the ER schema with the query modeling results. The annotations are represented as follows:


<img src="/images/er-schema-annotation.svg" alt="Annotated ER Schema" class="w-full h-auto">

<a href="/images/er-schema-annotation.svg" class="text-center" target="_blank" rel="noopener noreferrer">
  Click to view full size
</a>

## Step 3: Json meta-notion
In this step, we traslate the annotated ER schema into a JSON meta-notion. The JSON meta-notion is structured as follows:

### Queries: Q3,Q5,Q7,Q8

```txt
order: {
    order_id, date, total_price,
    has: [
        {
            product: {
                product_id, name, unit_price, discount, quantity
            }
        }
    ],
    placed_by: {
        customer: {
            customer_id, name, type
        }
    },
    belongs_to: {
        region: {
            name
        }
    }
}
```     

### Queries: Q1, Q2

```txt
customer: {
    customer_id, name, type, 
    places: [
        {
            order: {
                order_id, total_price, date
            }
        }
    ]
} 
```

### Queries: 6

```txt
product: { 
    product_id, name, unit_price, 
    ordered_by: [
        { 
            customer: {
                name, type
            }
        }
    ]
} 
```
### Queries: Q9

```txt
category: { 
    category_id, name, 
    has: [
        {
            products: {
                product_id, name, unit_price
            }
        }
    ]
}
```

### Queries: Q4

```txt
region: {
    region_id,
    name, 
    has: [
        {
            customers: {
                customer_id,
            }
        }
    ]
}
```
