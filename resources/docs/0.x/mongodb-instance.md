# Creation of MongoDB Instance

We have used the orginal dataset from Kaggle Hub: [Link to Dataset](https://www.kaggle.com/datasets/sebastianwillmann/beverage-sales) to create a MongoDB instance.

We reduced the dataset size to a manageable amount (e.g. 3,000,000 rows) to make easy the import and transformation. The reduced dataset is data about 1000 orders which is arround 3,000,000 rows in total.

## Import & Transformation

While the dataset is in CSV format, we have written a PHP database seeder to first import the data into PostgreSQL tables and then export it to JSON.

Here you can find the PHP database seeder for each table:

Step 0:

We have converted the CSV file to SQL insert statements and using the following command we import the data into table called `data` in PostgreSQL:

```php
use Illuminate\Support\Facades\DB;

DB::unprepared(file_get_contents(database_path('/seeders/data.sql')));
```

Step 1: 

We seed the `regoin` table data using the following command:

```php
use Illuminate\Support\Facades\DB;

DB::unprepared(<<<TEXT
    INSERT INTO regions (name)
    SELECT DISTINCT region_name
    FROM data;
TEXT
);
```

Step 2:

We seed the `customers` table data using the following command:

```php
use Illuminate\Support\Facades\DB;

DB::unprepared(<<<TEXT
    INSERT INTO customers (id, type,name, region_id)
    SELECT DISTINCT
        CAST(SUBSTRING(d.customer_id FROM 4) AS INTEGER) AS id,
        d.customer_type,
        CONCAT('Customer ', CAST(SUBSTRING(d.customer_id FROM 4) AS INTEGER)) AS name,
        r.id AS region_id
    FROM data d
    JOIN regions r ON r.name = d.region_name;
TEXT
);
```

Step 3:

We seed the `orders` table data using the following command:

```php
use Illuminate\Support\Facades\DB;

DB::unprepared(<<<TEXT
    INSERT INTO orders (id, customer_id, total_price, date)
    SELECT DISTINCT
        CAST(SUBSTRING(d.order_id FROM 4) AS INTEGER) AS order_id,
        CAST(SUBSTRING(d.customer_id FROM 4) AS INTEGER) AS customer_id,
        SUM(d.total_price) AS total_price,
        d.date
    FROM data d
    GROUP BY order_id, d.customer_id, d.date;
TEXT
);
```

Step 4:

We seed the `order_items` table data using the following command:

```php
use Illuminate\Support\Facades\DB;

DB::unprepared(<<<TEXT
    INSERT INTO categories (name)
    SELECT DISTINCT category_name
    FROM data;
TEXT
);
```

Step 5:

We seed the `products` table data using the following command:

```php
use Illuminate\Support\Facades\DB;

DB::unprepared(<<<TEXT
    INSERT INTO products (name, category_id)
    SELECT DISTINCT
        d.product_name,
        c.id AS category_id
    FROM data d
    JOIN categories c ON c.name = d.category_name;
TEXT
);
```

Step 6:
We seed the `order_product` table data using the following command:

```php
use Illuminate\Support\Facades\DB;

DB::unprepared(<<<TEXT
    INSERT INTO order_product (order_id, product_id, quantity, discount, unit_price, total_price)
    SELECT
        CAST(SUBSTRING(d.order_id FROM 4) AS INTEGER) AS order_id,
        p.id AS product_id,
        d.quantity,
        d.discount,
        d.unit_price,
        d.total_price
    FROM data d
    JOIN products p ON p.name = d.product_name;
TEXT
);
```


## Generate JSON

After seeding the PostgreSQL tables, using the [Laravel (PHP Framework)](https://laravel.com) API resource we prepared the each collection data to JSON format and then imported it into MongoDB instance.

### Order collection

```php
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;

Storage::put(
    'order.json',
    json_encode(
        OrderResource::collection(Order::all()),
        JSON_PRETTY_PRINT
    )
);
```

```php
// OrderResource.php
return [
    'order_id' => $this->id,
    'date' => $this->date,
    'total_price' => (float) $this->total_price,
    'customer_id' => $this->customer_id,
    'region_id' => $this->customer->region_id,
    'products' => OrderProductResource::collection($this->products),
];

// OrderProductResource.php
return [
    'product_id' => $this->id,
    'quantity' => (int) $this->pivot->quantity,
    'unit_price' => (float) $this->pivot->unit_price,
    'discount' => (float) $this->pivot->discount
];
```

```json
// Order JSON
[
    {
        "order_id": 479,
        "date": "2022-01-21",
        "total_price": 38.53,
        "customer_id": 3356,
        "region_id": 3,
        "products": [
            {
                "product_id": 13,
                "quantity": 13,
                "unit_price": 1.37,
                "discount": 0
            },
            // ... other products
        ]
    }
    // ... other orders
]
```


### Customer collection

```php
Storage::put(
    'customer.json',
    json_encode(
        CustomerResource::collection(Customer::all()),
        JSON_PRETTY_PRINT
    )
);
```


```php
// CustomerResource.php
return [
    'customer_id' => $this->id,
    'name' => $this->name,
    'type' => $this->type,
    'orders' => CustomerOrderResource::collection($this->orders)
];


// CustomerOrderResource.php
return [
    'order_id' => $this->id,
    'total_price' => $this->total_price,
    'date' => $this->date
];
```


```json
// Customer JSON
[
    {
        "customer_id": 4579,
        "name": "Customer 4579",
        "type": "B2B",
        "orders": [
            {
                "order_id": 642,
                "total_price": "405.5",
                "date": "2022-02-03"
            }
        ]
    },
    // ... other customers
]
```

### Product collection

```php
Storage::put(
    'products.json',
    json_encode(
        ProductResource::collection(Product::all()),
        JSON_PRETTY_PRINT
    )
);
```

```php
// ProductResource.php
return [
    'product_id' => $this->id,
    'name' => $this->name,
    'customers' => $this->orders->pluck('customer_id')->unique(),
];
```

```json
// Product JSON
[
    {
        "product_id": 1,
        "name": "Rauch Multivitamin",
        "customers": [
            8950,
            1187,
            // ... other customers
        ]
    },
    // ... other products
]
```

### Region collection

```php
Storage::put(
    'regions.json',
    json_encode(
        RegionResource::collection(Region::all()),
        JSON_PRETTY_PRINT
    )
);
```

```php
// RegionResource.php
return [
    'region_id' => $this->id,
    'name' => $this->name,
    'customer' => $this->customers->pluck('id'),
];
```

```json
// Region JSON
[
    {
        "region_id": 1,
        "name": "Mecklenburg-Vorpommern",
        "customer": [
            5627,
            7147,
            7170,
            // ... other customers
        ]
    },
    // ... other regions
]
```


### Category collection

```php
Storage::put(
    'categories.json',
    json_encode(
        CategoryResource::collection(Category::all()),
        JSON_PRETTY_PRINT
    )
);
```

```php
// CategoryResource.php
return [
    'region_id' => $this->id,
    'name' => $this->name,
    'customer' => $this->customers->pluck('id'),
];
```

```json
// Category JSON
[
    {
        "category_id": 1,
        "name": "Soft Drinks",
        "products": [
            10,
            12,
            19,
            22,
            // ... other products
        ]
    },
    // ... other categories
]
```
