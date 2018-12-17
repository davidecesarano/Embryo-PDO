# Embryo PDO
A quick and light PHP query builder using PDO.

## Requirements
* PHP >= 7.1

## Installation
Using Composer:
```
$ composer require davidecesarano/embryo-pdo
```

## Usage
* Connection
* Retrieving results
* Insert
* Select
* Update
* Delete
* Where clauses
* Ordering, grouping, limit
* Simple Joins
* Raw Query

### Connection
Create a multidimensional array with database parameters and pass it at the `Database` object. Later, create connection with `connection` method. 
```php
$database = [
    'local' => [
        'engine'   => 'mysql',
        'host'     => '127.0.0.1',
        'name'     => 'db_name',
        'user'     => 'user',  
        'password' => 'password',
        'charset'  => 'utf8mb4',
        'options'  => [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]
    ]
];

$database = new Embryo\PDO\Database($database);
$pdo = $database->connection('local');
```

### Retrieving results

Now you can create a simple query:
```php
$users = $pdo->table('users')->select()->all();
```
The `table` method returns a fluent query builder instance for the given table. This would build the query below:
```sql
SELECT *
FROM users
```
To get data from the select, we can is loop through the returned array of objects:
```php
foreach ($users as $user) {
    echo $user->name;
}
```

### Insert

You can insert rows in database with `insert` method.
```php
$data = ['name' => 'Name', => 'surname' => 'Surname'];
$users = $pdo->table('users')->insert($data)->lastId();
```
This will return the last inserted id.

### Select
You can select row/s with `select` method.
```php
$users = $pdo->table('users')->where('id', 1)->select('name, surname')->get();
```

### Update

You can select row/s with `select` method.
```php
$data = ['name' => 'Name', => 'surname' => 'Surname'];
$users = $pdo->table('users')->where('id', 1)->update($data)->get();
```

### Delete

### Where clauses

### Ordering, grouping, limit

### Simple Joins

### Raw Query