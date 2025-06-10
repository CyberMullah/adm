# Proposal

The domain we have choosen is **E-commerce / Beverage Sales** which is a **Beverage Sales Management System** application.

This application manages the beverage product sales for business and customer clients, it will allow the generate reports based on customers types, orders, products, region, and categories. Here is the dataset for above application from Kaggle Hub: [Link to Dataset](https://www.kaggle.com/datasets/sebastianwillmann/beverage-sales)

This dataset was made to show realistic sales trends in the beverage industry, including things like regional tastes, seasonal changes, and different types of customers. It includes both B2B and B2C sales, so it can be used for many different kinds of analysis.

## Dataset

In particulare the dataset has the following columns:

- `Order_ID` Unique identifier for each order.
- `Customer_ID` Unique identifier for each customer.
- `Customer_Type` Indicates whether the customer is B2B or B2C.
- `Product` The name of the product purchased.
- `Category` The product category, .e.g., Soft Drinks.
- `Unit_Price` The price per unit of the product.
- `Quantity` The number of units purchased for the specified product in the order.
- `Discount` The discount applied to the product (e.g., 0.1 for 10%).
- `Total_Price` The total price for the product after applying discounts.
- `Region` The region of the customer, such as "Bayern" or "Berlin".
- `Order_Date` The date when the order was placed. 

> Discounts are only given to B2B customers.
