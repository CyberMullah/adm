# Cassandra Design

Here we try by using the aggregate oriented design to create the schema in Cassandra.

## Schema Design

For each entity, we will create a table that contains all the necessary information to retrieve the data efficiently.


### Order Table

As we have already mentioned in MongoDB Design, the following queries are asscociated with the `Order` table:

- Query 3: Selection attributes `[Order(date)_!]`
- Query 5: Selection attributes `[Order_H(discount)]`
- Query 7: Selection attributes `[Order(date)_!]`
- Query 8: Selection attributes `[Order(date)_!]`

There are some considerations about the order table design, first `date` is used as partition key since most queries are time-bounded, `order_id` ensures row uniqueness per order.

Also, filtering on nested fields (like `discount`) is not directly supported in Cassandra so we moved to top level field.

```txt
Partition key = {date} 
Primary key ={order_id} 
````

```cql
CREATE TABLE orders (
    date date,
    order_id uuid,
    product_id int,
    quantity int,
    discount double,
    customer_id int,
    PRIMARY KEY ((date), product_id, order_id)
)
```

### Customer Table

As we have already discussed in MongoDB design section the following queries are associated with the `Customer` entity:

- Query 1: Selection attributes `[Customer(type)_!]`
- Query 2: Selection attributes `[Customer(type)_!]`

```txt
Partition key = {type}
```

```cql
CREATE TABLE customers (
    type text,
    customer_id int,
    name text,
    region text,
    PRIMARY KEY ((type), customer_id)
);
```


### Product Table

As you may find in the MongoDB design section as well, the following queries are associated with the `Product` entity:

- Query 6: Selection attributes `[Product(name)_!]`

```txt
Partition key = {name}
index = {name}
```


```cql
CREATE TABLE products (
    product_id int PRIMARY KEY,
    name text,
    unit_price double
);

CREATE INDEX ON products(name);
```


### Category Table
As already mentioned in the MongoDB design section, the following queries are associated with the `Category` collection:

- Query 9: Selection attributes `[Category(name)_!]`


```txt
Partition key = {name}
```

```cql
CREATE TABLE categories (
    name text PRIMARY KEY,
    category_id int,
    description text
);
```


### Region Table
The following queries are associated with the `Region` entity:

- Query 4: Selection attributes `[Region(name)_!]`

```txt
Partition key = {name}
```

```cql
CREATE TABLE regions (
    region_id int PRIMARY KEY,
    name text,
    description text
);
```

## CQL Queries

### Query 1:
Retrieve all B2B customers.

```sql
SELECT * FROM customers_by_type WHERE type = 'B2B';
```

### Query 2:

Retrieve orders with their details by B2C typed customers.

While we don't have join support in Cassandra, then we can perform two separate queries to achieve this: 
The first query retrieves B2C customers, and the second retrieves their orders.

```sql
SELECT customer_id FROM customers_by_type WHERE type = 'B2C';
```

```sql
SELECT * FROM orders WHERE customer_id IN (FIRST_QUERY_RESULT);
```

### Query 3:

Given a period of dates, retrieve the details of 5 most sold products.

This query requires scanning multiple partitions, which is not efficient in Cassandra. Instead, we can achieve this in application client side.

```sql
SELECT product_id, quantity FROM orders WHERE date = '2023-01-01';
```

### Query 4:
Given region "Saarland", retrieve the number of customers.

We need to filter customer by type (because the type is not part of the partition key) and region. This requires filtering, which is not efficient in Cassandra but can be done with `ALLOW FILTERING`.

```sql
SELECT COUNT(*) FROM customers WHERE type = 'B2B' AND region = 'Saarland' ALLOW FILTERING;
SELECT COUNT(*) FROM customers WHERE type = 'B2C' AND region = 'Saarland' ALLOW FILTERING;
```

### Query 5:

Retrieve all products that have orders with discount more than 10%.

The Cassandra doesn't support filtering on non-primary keys without allow filering and it would require a full table scan.
So we decided to create a separated table to store these products.

```cql
CREATE TABLE orders_with_discount (
    date date,
    product_id int,
    order_id uuid,
    quantity int,
    discount double,
    customer_id int,
    PRIMARY KEY ((date), product_id, order_id)
);
```

So now the qeuery will be:

```sql
SELECT * FROM orders_with_discount WHERE discount > 10;
```


### Query 6

Name and type of customers that ordered the product “Fanta”.

While there is no join in Cassandra, then we first need to fidn the "Fanta" `product_id` and then retrieve the customers who ordered it.

```sql
SELECT product_id FROM products WHERE name = 'Fanta' ALLOW FILTERING;
```

Here we need to give the `date` becuase it is the partition key in the `orders` table.

```sql
SELECT customer_id FROM orders WHERE date = '2023-01-01' AND product_id = 7;
```

```sql
SELECT * FROM customers_by_type 
WHERE customer_id IN (/* SECOND_QUERY_RESULT */);
```


### Query 7
Identify customers who haven’t placed an order in the last 6 months.


Cassandra does not support full scans or anti-joins. To achieve this, we create an extra column for customer table.

```cql
CREATE TABLE customers (
    type text,
    customer_id int,
    name text,
    region text,
    last_order_date date,
    PRIMARY KEY ((type), customer_id)
);
```

Then we can query: (assuming today date is '2024-12-08')

```sql
SELECT * FROM customers 
WHERE type = 'B2B' AND last_order_date < '2024-12-08' ALLOW FILTERING;
```

### Query 8

Retrieve the number of orders for each region within 2022-08-24 and 2023-08-24.

While we have the `region` in the `customers` table, and the Cassandra does not support joins, we can use a separate table to store the region-wise order count.

```cql
CREATE TABLE orders_by_region_date (
    region text,
    date date,
    order_id uuid,
    PRIMARY KEY ((region, date), order_id)
);
```

So the query will be:

```sql
SELECT COUNT(*) FROM orders_by_region_date 
WHERE region = 'Saarland' AND date >= '2022-08-24' AND date <= '2023-08-24';
```

### Query 9

Retrieve all Soft Drinks products.

The same as above query, while the Cassandra does not support joins, we can create a separate table to store the category-product table.

```cql
CREATE TABLE products_by_category (
    category text,
    product_id int,
    name text,
    PRIMARY KEY ((category), product_id)
);
```


```sql
SELECT * FROM products_by_category WHERE category = 'Soft Drinks';
```
