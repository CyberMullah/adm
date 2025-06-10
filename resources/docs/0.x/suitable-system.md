# Suitable System

We chose MongoDB for our application with respect to the following aspects:

- Our application is analytical (read-intensive), and MongoDB is well-suited for analytics and e-commerce applications. It efficiently handles queries involving aggregations, filtering, and sorting, which are crucial for our workload.

- MongoDB has a flexible schema, which is essential in an e-commerce setting where data structures may vary. For example, some customers might leave reviews while others do not, making a rigid schema impractical.

- MongoDB’s rich aggregation framework aligns well with our analytical queries, which involve complex operations such as grouping, ordering, filtering, and computing aggregates (e.g., top-selling products, inactive customers, and regional sales reports).

- High availability and eventual consistency are key requirements for our application. MongoDB provides replica sets to ensure availability, and its read/write concerns allow us to balance consistency and performance.

- Cassandra’s query language is limited for analytical queries, making it a less suitable choice. It does not support complex joins, aggregations, and filtering at the system level, which are essential for our workload.

- Neo4j is strong for relationship-heavy queries, but its partitioning limitations make it difficult to scale for our use case. While it excels in graph-based analysis, our workload requires a more document-oriented approach for handling orders, customers, and products.
