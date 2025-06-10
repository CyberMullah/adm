# System Configuration

While the dataset was in CSV format having more than 3M orders. First to make the import the dataset and mapping to JSON format we just take first 1K orders.

1. We imported the CSV data into a Postgres table.
2. Based on the relational scheme on step 2 of this file, we created the tables & relationships.
3. We wrote the SQL queries to fill each table with correct data.
4. We develop a small PHP (Laravel) application to map the SQL tables into JSON.
5. We created the MongoDB database online and imported the JSON files,
6. We introduced the required indexes in MongoDB database.
7. Installed the MongoDB compass locally in our machine and connected to the MongoDB

```
mongodb+srv://i:<password>@cluster0.cuj11b6.mongodb.net/
```

Replace the <password> with the password that we have sent along the project submittion.
