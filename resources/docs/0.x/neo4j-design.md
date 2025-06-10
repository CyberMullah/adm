# Neo4j Design

In this section, we try to design Neo4j schema for the workload we have described.

## Schema Design

For each entity, we will create a node that contains all the necessary information to retrieve the data efficiently.


### Nodes and Relationships

**Nodes:**

```txt
Customer (:Customer {customer_id, name, type})
Order (:Order {order_id, date, total_price})
Product (:Product {product_id, name, unit_price, discount, quantity})
Category (:Category {category_id, name})
Region (:Region {region_id, name})
```


**Relationships:**

```txt
(:Customer)-[:PLACED]->(:Order) → A customer places an order.
(:Order)-[:CONTAINS]->(:Product) → An order contains products.
(:Product)-[:BELONGS_TO]->(:Category) → A product belongs to a category.
(:Customer)-[:LIVES_IN]->(:Region) → A customer lives in a specific region.
(:Order)-[:DELIVERED_TO]->(:Region) → An order is delivered to a region.
```

In the below, you can find the diagram of the schema we have designed:

<img src="/images/design-neo4j.svg" alt="Neo4j Design"  class="w-full h-auto"/>


## Queries

Here Cypher Queries for Workload Operations:

### Query 1

Find all customers of type "B2B"

```cypher
MATCH (c:Customer {type: "B2B"})
RETURN c;
```


### Query 2

Find all orders placed by customers of type "B2C"

```cypher
MATCH (c:Customer {type: "B2C"})-[:PLACED]->(o:Order)
RETURN c, o;
```


### Query 3

Find the top 5 best-selling products in a given date range

```cypher
MATCH (o:Order)-[:CONTAINS]->(p:Product)
WHERE o.date >= date("2024-01-01") AND o.date <= date("2024-12-31")
RETURN p.name, SUM(p.quantity) AS total_sales
ORDER BY total_sales DESC
LIMIT 5;
```


### Query 4

Count customers in a specific region ("Saarland")

```cypher
MATCH (r:Region {name: "Saarland"})<-[:LIVES_IN]-(c:Customer)
RETURN COUNT(c) AS customerCount;
```


### Query 5

Find products with a discount greater than 10%

```cypher
MATCH (o:Order)-[:CONTAINS]->(p:Product)
WHERE p.discount > 10
RETURN DISTINCT p.name;
```


### Query 6

Find customers who ordered a specific product (e.g., "Fanta")

```cypher
MATCH (c:Customer)-[:PLACED]->(o:Order)-[:CONTAINS]->(p:Product {name: "Fanta"})
RETURN DISTINCT c.name, c.type;
```


### Query 7

Find customers who haven't placed an order in the last 6 months

```cypher
MATCH (c:Customer)
WHERE NOT EXISTS {
  MATCH (c)-[:PLACED]->(o:Order)
  WHERE o.date >= date() - duration("P6M")
}
RETURN c;
```


### Query 8

Count total orders per region in a given date range

```cypher
MATCH (o:Order)-[:DELIVERED_TO]->(r:Region)
WHERE o.date >= date("2022-08-24") AND o.date <= date("2023-08-24")
RETURN r.name AS region, COUNT(o) AS totalOrders;
```


### Query 9

Find all products in a given category (e.g., "Soft Drinks")

```cypher
MATCH (c:Category {name: "Soft Drinks"})<-[:BELONGS_TO]-(p:Product)
RETURN p;
```

