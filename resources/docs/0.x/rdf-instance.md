# RDF Instance

In this step, we try to create the RDF instance.

```turtle
@prefix : <http://www.example.org/ontology#> .
@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .
@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix owl: <http://www.w3.org/2002/07/owl#> .

# --- Classes
:Customer rdf:type owl:Class .
:Order rdf:type owl:Class .
:Product rdf:type owl:Class .
:Region rdf:type owl:Class .
:Category rdf:type owl:Class .

# --- Regions
:Saarland rdf:type :Region .

# --- Categories
:SoftDrinks rdf:type :Category .
:Snacks rdf:type :Category .

# --- Customers
:Cust001 rdf:type :Customer ;
         :customerId 1 ;
         :customerType "B2B" ;
         :customerName "Global Corp" ;
         :locatedInRegion :Saarland .

:Cust002 rdf:type :Customer ;
         :customerId 2 ;
         :customerType "B2C" ;
         :customerName "Anna Smith" ;
         :locatedInRegion :Saarland .

:Cust003 rdf:type :Customer ;
         :customerId 3 ;
         :customerType "B2C" ;
         :customerName "John Doe" ;
         :locatedInRegion :Saarland .

# --- Products
:Prod001 rdf:type :Product ;
         :productId 100 ;
         :productName "Fanta" ;
         :belongsToCategory :SoftDrinks .

:Prod002 rdf:type :Product ;
         :productId 101 ;
         :productName "Chips" ;
         :belongsToCategory :Snacks .

:Prod003 rdf:type :Product ;
         :productId 102 ;
         :productName "Cola" ;
         :belongsToCategory :SoftDrinks .

# --- Orders
:Order001 rdf:type :Order ;
          :orderId 5001 ;
          :orderDate "2023-02-01"^^xsd:date ;
          :totalPrice 15.00 ;
          :discount 0.05 ;
          :hasCustomer :Cust001 ;
          :containsProduct :Prod002 .

:Order002 rdf:type :Order ;
          :orderId 5002 ;
          :orderDate "2023-05-12"^^xsd:date ;
          :totalPrice 10.00 ;
          :discount 0.12 ;
          :hasCustomer :Cust002 ;
          :containsProduct :Prod001 .

:Order003 rdf:type :Order ;
          :orderId 5003 ;
          :orderDate "2022-12-10"^^xsd:date ;
          :totalPrice 8.00 ;
          :discount 0.00 ;
          :hasCustomer :Cust003 ;
          :containsProduct :Prod001, :Prod003 .

:Order004 rdf:type :Order ;
          :orderId 5004 ;
          :orderDate "2023-07-10"^^xsd:date ;
          :totalPrice 9.00 ;
          :discount 0.15 ;
          :hasCustomer :Cust001 ;
          :containsProduct :Prod001 .

# --- Place Orders (Inverse of hasCustomer)
:Cust001 :placesOrder :Order001 , :Order004 .
:Cust002 :placesOrder :Order002 .
:Cust003 :placesOrder :Order003 .
```
