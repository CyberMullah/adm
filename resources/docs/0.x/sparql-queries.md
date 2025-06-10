# SPARQL queries

In this step, we write sone SPARQL queries to get some data form the RDF instance.


## Query 1

Retrieve all **B2B** customers.

```sparql
PREFIX : <http://www.example.org/ontology#>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>

SELECT ?customer ?order ?orderDate
WHERE {
  ?customer a :Customer ;
            :customerType "B2B" ;
            :placesOrder ?order .
  ?order :orderDate ?orderDate .
}
```

## Query 2
Retrieve orders with their details by **B2C** typed customers.

```sparql
CONSTRUCT {
  ?order a :Order ;
         :orderDate ?orderDate ;
         :totalPrice ?totalPrice ;
         :hasCustomer ?customer .

  ?customer a :Customer ;
            :customerType "B2C" ;
            :customerName ?name .
}
WHERE {
  ?customer a :Customer ;
            :customerType "B2C" ;
            :customerName ?name ;
            :placesOrder ?order .

  ?order a :Order ;
         :orderDate ?orderDate ;
         :totalPrice ?totalPrice .
}
```

## Query 3

Retrieve the B2B customers.

```sparql
ASK {
  ?customer a :Customer ;
            :customerType "B2B" .
}
```
