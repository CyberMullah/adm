# Conceptual Schema Design

According the selected [dataset](https://www.kaggle.com/datasets/sebastianwillmann/beverage-sales) here is the **entities** and **Associations**.

## Entities
Application main entities:

<img src="/images/conceptual-schema.png" alt="Conceptual Schema Design"/>

### Orders
The `orders` table will store all information about orders which placed by customers. It is one of the main entity in the sytem.

- `order_id` - Unique identifier for each order.
- `total_price` - The total cost of the order.
- `date` - The date whhen the customer was placed by customer.


### Customers
The `customers` table will store the basic information inclueing the type.

- `customer_id` - Unique identifier for each customer.
- `type` - Specifies if the customer is B2B or B2C.
- `name` - The full name of customer

> The name wasn't in the dataset, so we added a fake one to make query results easier to read.


### Products
The `products` table store all details of products & has the following columns:

- `product_id` - Unique identifier for each product.
- `name` - The name product (e.g., cocal-cola)


### Categories
The `categories` table will store the product categories.

- `category_id` - Unique identifier for each category.
- `name` - The name of the category (e.g., Soft Drinks)

### Regions
The `regions` table will store the regions of customers.

- `region_id` - Unique identifier for each region.
- `name` - The name of the region (e.g., Bayern, Berlin)


## Associations
Here is the associations between the entities:

- **Customers** to **Orders**: One-to-Many
  - A customer can place multiple orders, but each order is associated with only one customer.
- **Orders** to **Products**: Many-to-Many
  - An order can contain multiple products, and a product can be part of multiple orders.
  - This is typically implemented using a junction table (e.g., `order_products`) that includes the `order_id` and `product_id`.
  - With having the `quantity` and `discount` columns in the junction table.
- **Products** to **Categories**: Many-to-One
  - A product belongs to one category, but a category can have multiple products.
- **Customers** to **Regions**: Many-to-One
  - A customer belongs to one region, but a region can have multiple customers.
