# Model in RDFS/OWL

In this section, we try to model the system in to RDFS/OWL.

## Modeling

We modeled the entities from the Beverage Sales Management System as the main classes and their attributes as properties. The associations between classes are represented as object properties. For each property, we have specified its domain and range.


### Namespace Declarations

Here is the namespace declaration used in the model:

```turtle
@prefix ex:   <http://www.beveragesales.org/#> .
@prefix rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> .
@prefix owl:  <http://www.w3.org/2002/07/owl#> .
@prefix xsd:  <http://www.w3.org/2001/XMLSchema#> .
```

### Classes

```turtle
ex:Customer rdf:type rdfs:Class .
ex:Order rdf:type rdfs:Class .
ex:Product rdf:type rdfs:Class .
ex:Region rdf:type rdfs:Class .
ex:Category rdf:type rdfs:Class .
```

### Object Properties

```turtle
:placesOrder rdf:type owl:ObjectProperty ;
             rdfs:domain ex:Customer ;
             rdfs:range ex:Order ;
             owl:inverseOf :hasCustomer ;
             rdf:type owl:FunctionalProperty .

:hasCustomer rdf:type owl:ObjectProperty ;
             rdfs:domain ex:Order ;
             rdfs:range ex:Customer ;
             rdf:type owl:InverseFunctionalProperty .

:containsProduct rdf:type owl:ObjectProperty ;
                 rdfs:domain ex:Order ;
                 rdfs:range ex:Product .

:hasProductInOrder rdf:type owl:ObjectProperty ;
                   owl:inverseOf :containsProduct ;
                   rdfs:domain ex:Product ;
                   rdfs:range ex:Order .

:locatedInRegion rdf:type owl:ObjectProperty ;
                 rdfs:domain ex:Customer ;
                 rdfs:range ex:Region ;
                 rdf:type owl:FunctionalProperty .

:belongsToCategory rdf:type owl:ObjectProperty ;
                   rdfs:domain ex:Product ;
                   rdfs:range ex:Category ;
                   rdf:type owl:FunctionalProperty .
```

### Datatype Properties

```turtle
:customerId rdf:type owl:DatatypeProperty ;
            rdfs:domain ex:Customer ;
            rdfs:range xsd:integer ;
            rdf:type owl:FunctionalProperty .

:orderId rdf:type owl:DatatypeProperty ;
         rdfs:domain ex:Order ;
         rdfs:range xsd:integer ;
         rdf:type owl:FunctionalProperty .

:productId rdf:type owl:DatatypeProperty ;
           rdfs:domain ex:Product ;
           rdfs:range xsd:integer ;
           rdf:type owl:FunctionalProperty .

:discount rdf:type owl:DatatypeProperty ;
          rdfs:domain ex:Product ;
          rdfs:range xsd:float .

:orderDate rdf:type owl:DatatypeProperty ;
           rdfs:domain ex:Order ;
           rdfs:range xsd:date .

:totalPrice rdf:type owl:DatatypeProperty ;
            rdfs:domain ex:Order ;
            rdfs:range xsd:decimal .
```

## Equivalent Classes and Disjoint Classes

We don't have any equivalent classes in our system, but all the classes are disjoint as defined in RDF.

```turtle
ex:Customer owl:disjointWith ex:Product , ex:Order .
ex:Product owl:disjointWith ex:Order , ex:Customer .
ex:Order owl:disjointWith ex:Customer , ex:Product .
ex:Category owl:disjointWith ex:Customer , ex:Order , ex:Product .
ex:Region owl:disjointWith ex:Product .
```

## Inverse Property

We added the hasProductInOrder as an inverse property for the containsProduct property. This means an order contains products, and products are part of orders.

```turtle
:hasProductInOrder rdf:type owl:ObjectProperty ;
                   owl:inverseOf :containsProduct ;
                   rdfs:domain ex:Product ;
                   rdfs:range ex:Order .
```

## Functional and Inverse Functional Properties


For the properties related to the Customer class, we describe which are functional or inverse functional.

- customerId is a functional property because each customer has exactly one unique ID.
- orderId is also a functional property because each order has exactly one ID.
- placesOrder is a functional property because a customer places exactly one order at a time.
- hasCustomer is inverse functional because each order can be associated with only one customer.

```turtle
:customerId rdf:type owl:DatatypeProperty ;
    rdfs:domain ex:Customer ;
    rdfs:range xsd:integer ;
    rdf:type owl:FunctionalProperty .
:placesOrder rdf:type owl:ObjectProperty ;
    rdfs:domain ex:Customer ;
    rdfs:range ex:Order ;
    rdf:type owl:FunctionalProperty ;
    owl:inverseOf :hasCustomer .
:hasCustomer rdf:type owl:ObjectProperty ;
    rdfs:domain ex:Order ;
    rdfs:range ex:Customer ;
    rdf:type owl:InverseFunctionalProperty .
```


