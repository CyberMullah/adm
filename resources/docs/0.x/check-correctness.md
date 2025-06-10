# Check the correctness

We used RDF Playground to check the correctness of our model and executing the SPARQL queries.

<img src="/images/graph.png" alt="RDF Playground" class="w-full">

<p class="text-center my-0">
    <a href="/images/graph.png" target="_blank" class="underlined">Click to view full size </a>
</p>

## Query Results

We run the queries over the RDF instance and got the following results:

### Query 1

Retrieve B2B Customers and their Orders

```turtle
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

<img src="/images/rdf/query-1.png" alt="The first query result" class="w-full">

<p class="text-center my-0">
    <a href="/images/rdf/query-1.png" target="_blank" class="underlined">Click to view full size </a>
</p>


### Query 2

Retrieve orders with their details by B2C typed customers.

```turtle
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

<img src="/images/rdf/query-2.png" alt="The first query result" class="w-full">

<p class="text-center my-0">
    <a href="/images/rdf/query-2.png" target="_blank" class="underlined">Click to view full size </a>
</p>


### Query 3

Given region "Saarland", retrieve the number of customers


```turtle
SELECT (COUNT(?customer) AS ?customerCount)
WHERE {
  ?customer a :Customer ;
            :locatedInRegion :Saarland .
}
```

<img src="/images/rdf/query-3.png" alt="The first query result" class="w-full">
<p class="text-center my-0">
    <a href="/images/rdf/query-3.png" target="_blank" class="underlined">Click to view full size </a>
</p>


### Query 4

Retrieve all products that have orders with discount more than 10%.

```turtle
SELECT DISTINCT ?product
WHERE {
  ?order a :Order ;
         :discount ?discount ;
         :containsProduct ?product .
  FILTER(?discount > 0.10)}
```

<img src="/images/rdf/query-4.png" alt="The first query result" class="w-full">
<p class="text-center my-0">
    <a href="/images/rdf/query-4.png" target="_blank" class="underlined">Click to view full size </a>
</p>


### Query 5
Are there any B2B customers?

```turtle
ASK {
  ?customer a :Customer ;
            :customerType "B2B" .
}

```
<img src="/images/rdf/query-5.png" alt="The first query result" class="w-full">
<p class="text-center my-0">
    <a href="/images/rdf/query-5.png" target="_blank" class="underlined">Click to view full size </a>
</p>
