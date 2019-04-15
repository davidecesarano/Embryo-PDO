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
* [Connection](#connection)
* [Retrieving results](#retriving-results)
* [Insert](#insert)
* [Select](#select)
* [Update](#update)
* [Delete](#delete)
* [Where clauses](#where-clauses)
* [Ordering, limit, grouping](#ordering-limit-grouping)
* [Simple Joins](#simple-joins)
* [Raw Query](#raw-query)

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
$data = ['name' => 'Name', 'surname' => 'Surname'];
$users = $pdo->table('users')->insert($data)->lastId();
```
This will return the last inserted id.

### Select
You can select row/s with `select` method.
```php
$user = $pdo->table('users')->where('id', 1)->select('name, surname')->get();
```
The `get()` method returns a `stdClass` object:
```php
echo $user->name.' '.$user->surname;
```

### Update

You can update row/s with `update` method.
```php
$data = ['name' => 'Name', 'surname' => 'Surname'];
$query = $pdo->table('users')->where('id', 1)->update($data)->count();
```
The `count()` method returns the number of updated rows. The update method also accepts the `exec()` method and it will return true on success or false on failure.

### Delete
You can delete row/s with `delete` method.
```php
$query = $pdo->table('users')->where('id', 1)->delete()->exec();
```

### Where clauses

You may use the `where` method to add where clauses to the query. The most basic call to where requires three arguments. The first argument is the name of the column. The second argument is an operator, which can be any of the database's supported operators. Finally, the third argument is the value to evaluate against the column.
```php
$users = $pdo->table('users')->where('id', '>', 1)->select()->all();
```
For convenience, if you want to verify that a column is equal to a given value, you may pass the value directly as the second argument to the where method:
```php
$user = $pdo->table('users')->where('id', 1)->select()->get();
```

You may use `andWhere`, `orWhere` or `rawWhere` methods for adding clauses to query:
```php
// andWhere
$users = $pdo->table('users')
    ->where('city', 'Naples')
    ->andWhere('role', 1)
    ->select()
    ->all();

// orWhere
$users = $pdo->table('users')
    ->where('city', 'Naples')
    ->orWhere('city', 'Rome')
    ->select()
    ->all();

// rawWhere
$users = $pdo->table('users')
    ->where('city', 'Naples')
    ->rawWhere('OR (age >= :age AND age <= :age)', ['age' => 30])
    ->select()
    ->all();
```

### Ordering, limit, grouping
The `orderBy` method allows you to sort the result of the query by a given column:
```php
$users = $pdo->table('users')->orderBy('id DESC')->select()->all();
```
You may use the `limit` method to limit the number of results returned from the query:
```php
$users = $pdo->table('users')->limit('0,10')->select()->all();
```
You may use the `groupBy` method to group the query results.
```php
$users = $pdo->table('users')->groupBy('role')->select()->all();
```
### Simple Joins
The query builder may also be used to write simple join statements with `leftJoin`, `rightJoin`, `crossJoin`, `innerJoin` or `rawJoin` methods:
```php
// left join
$users = $pdo->table('users')
    ->leftJoin('roles ON roles.id = users.role_id')
    ->select('users.*', 'roles.name')
    ->all();

// raw join
$users = $pdo->table('users')
    ->rawJoin('LEFT JOIN roles ON roles.id = users.role_id')
    ->select('users.*', 'roles.name')
    ->all();
```

### Raw Query
Sometimes you may need to use a raw expression in a query. To create a raw expression, you may use the `query` method:
```php
$users = $pdo->query("
    SELECT
        users.*,
        roles.name
    FROM users
    LEFT JOIN roles ON roles.id = users.role_id
    WHERE users.city = :city
    ORDER BY users.id DESC
")->values([
    'city' => 'Naples'
])->all();
```
The `values` method binds a value to a parameter. Binds a value to a corresponding named placeholder in the SQL statement that was used to prepare the statement.  