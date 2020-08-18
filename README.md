# Embryo PDO
A quick and light PHP query builder using PDO.
```php
$user = $pdo->table('users')
    ->where('country', 'Italy')
    ->and('city', 'Naples')
    ->and(function($query) {
        $query
            ->where('age', 20)
            ->or('age', 30)
    })
    ->andIsNotNull('updated_at')
    ->andIn('roles', [1, 2, 3])
    ->get();
```

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
    * [Retrieving a single row](#)
    * [Forcing array](#)
    * [Aggregates](#)
* [Where conditions](#where-conditions)
    * [Simple Where](#)
    * [OR condition](#)
    * [AND/OR closure](#)
    * [BETWEEN condition](#)
    * [IN condition](#)
    * [IS NULL condition](#)
* [Joins](#joins)
* [Insert](#insert)
* [Update](#update)
* [Delete](#delete)
* [Ordering, limit, grouping](#ordering-limit-grouping)
* [Raw Query](#raw-query)
* [Security](#security)

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

You can create a simple query:
```php
$users = $pdo->table('users')->get();
```
The `table` method returns a fluent query builder instance for the given table. This would build the query below:
```sql
SELECT * FROM users
```
To get data from the select, we can is loop through the returned array of objects:
```php
foreach ($users as $user) {
    echo $user->name;
}
```

#### Retrieving a single row

If you just need to retrieve a single row from the database table, you may use the same (`get`) method.
```php
$user = $pdo->table('users')
    ->where('id', 1)
    ->get();

echo $user->name;
```
If you don't even need an entire row, you may extract one or more values from a record using the `select` method.
```php
$user = $pdo->table('users')
    ->where('id', 1)
    ->select('name, surname')
    ->get();

echo "Hi, i am $user->name $user->surname";
```

#### Forcing array
If you want to force return array of objects, you can use `all` method
```php
$user = $pdo->table('users')
    ->where('id', 1)
    ->all();

foreach ($users as $user) {
    echo $user->name;
}
```

#### Aggregates

The query builder also provides a variety of aggregate methods such as `count`, `max`, `min`, `avg`, and `sum`.
```php
$avg = $pdo->table('orders')->avg('price');
echo $avg;
```

### Where conditions

#### Simple Where

You may use the `where` method to add where clauses to the query. 
The most basic call to where requires three arguments. The first argument is the name of the column. The second argument is an operator, which can be any of the database's supported operators. Finally, the third argument is the value to evaluate against the column.
```php
$users = $pdo->table('users')
    ->where('id', '>', 1)
    ->get();
```
For convenience, if you want to verify that a column is equal to a given value, you may pass the value directly as the second argument to the where method:
```php
$user = $pdo->table('users')
    ->where('id', 1)
    ->get();
```
You may use a variety of other operators when writing a where clause:
```php
$user = $pdo->table('users')
    ->where('country', 'Italy')
    ->and('name', 'LIKE', 'David%')
    ->get();
```

#### OR condition
You may chain where constraints together as well as add "or" clauses to the query.
```php
$user = $pdo->table('users')
    ->where('country', 'Italy')
    ->or('country', 'Spain')
    ->get();
```

#### AND/OR closure

If you need to group an "or" or "and" condition within parentheses, you may pass a Closure as the first argument to the method:
```php
$user = $pdo->table('users')
    ->where('country', 'Italy')
    ->and(function($query){
        $query
            ->where('country', 'Spain')
            ->or('country', 'France')
    })
    ->get();
```
This would build the query below:
```sql
SELECT * 
FROM users
WHERE country = 'Italy'
AND (
    country = 'Spain'
    OR country = 'France'
)
```

#### BETWEEN condition
The `whereBetween` / `whereNotBetween` method verifies that a column's value is between / not between two values:
```php
$user = $pdo->table('users')
    ->whereBetween('age', [20, 30])
    ->get();

$user = $pdo->table('users')
    ->whereNotBetween('age', [20, 30])
    ->get();
```

#### IN condition
The `whereIn` / `whereNotIn` method verifies that a given column's value is contained / not contained within the given array:
```php
$user = $pdo->table('users')
    ->whereIn('age', [20, 30])
    ->get();

$user = $pdo->table('users')
    ->whereNotIn('age', [20, 30])
    ->get();
```

#### IS NULL condition
The `whereNull` / `whereNotNull` method verifies that the value of the given column is `NULL` / not NULL:

```php
$user = $pdo->table('users')
    ->whereNull('updated_at')
    ->get();

$user = $pdo->table('users')
    ->whereNotNull('updated_at')
    ->get()
```

#### Method aliases
Below is a table with all the methods of the where conditions and their aliases.

| Method              	| Alias                                                                                          	|
|---------------------	|------------------------------------------------------------------------------------------------	|
| where()             	| and()<br>andWhere()                                                                            	|
| orWhere()           	| or()                                                                                           	|
| whereBetween()      	| andBetween()<br>andWhereBetween()                                                              	|
| orWhereBetween()    	| orBetween()                                                                                    	|
| whereNotBetween()   	| andNotBetween()<br>andWhereNotBetween()                                                        	|
| orWhereNotBetween() 	| orNotBetween()                                                                                 	|
| whereIn()           	| andIn()<br>andWhereIn()                                                                        	|
| orWhereIn()         	| orIn()                                                                                         	|
| whereNotIn()        	| andNotIn()<br>andWhereNotIn()                                                                  	|
| orWhereNotIn()      	| orNotIn()                                                                                      	|
| whereNull()         	| andNull()<br>andWhereNull()<br>whereIsNull()<br>andIsNull()<br>andWhereIsNull()                	|
| orWhereNull()       	| orNull()<br>orWhereIsNull()<br>orIsNull()                                                      	|
| whereNotNull()      	| andNotNull()<br>andWhereNotNull()<br>whereIsNotNull()<br>andIsNotNull()<br>andWhereIsNotNull() 	|
| orWhereNotNull()    	| orNotNull()<br>orWhereIsNotNull()<br>orIsNotNull()                                             	|
### Joins

The query builder may also be used to write simple join statements with `leftJoin`, `rightJoin`, `crossJoin`, `innerJoin` or `rawJoin` methods:
```php
// left join
$users = $pdo->table('users')
    ->leftJoin('roles ON roles.id = users.role_id')
    ->select('users.*', 'roles.name')
    ->get();

// right join
$users = $pdo->table('users')
    ->rightJoin('roles ON roles.id = users.role_id')
    ->select('users.*', 'roles.name')
    ->get();

// cross join
$users = $pdo->table('users')
    ->crossJoin('roles ON roles.id = users.role_id')
    ->select('users.*', 'roles.name')
    ->get();

// inner join
$users = $pdo->table('users')
    ->innerJoin('roles ON roles.id = users.role_id')
    ->select('users.*', 'roles.name')
    ->get();

// raw join
$users = $pdo->table('users')
    ->rawJoin('LEFT JOIN roles ON roles.id = users.role_id')
    ->select('users.*', 'roles.name')
    ->get();
```
### Insert

You can insert row/s in database with `insert` method.
```php
$lastInsertedId = $pdo->table('users')
    ->insert([
        'name' => 'Name', 
        'surname' => 'Surname'
    ])
    ->lastId();
```
This will return the last inserted id. The insert method also accepts the `exec()` method and it will return true on success or false on failure.

### Update

You can update row/s with `update` method.
```php
$update = $pdo->table('users')
    ->where('id', 1)
    ->update([
        'name' => 'Name', 
        'surname' => 'Surname'
    ])
    ->exec();

// $update return TRUE or FALSE
```

### Delete
You can delete row/s with `delete` method.
```php
$delete = $pdo->table('users')
    ->where('id', 1)
    ->delete()
    ->exec();

// $delete return TRUE or FALSE
```

### Ordering, limit, grouping
The `orderBy` method allows you to sort the result of the query by a given column:
```php
$users = $pdo->table('users')
    ->orderBy('id DESC')
    ->get();
```
You may use the `limit` method to limit the number of results returned from the query:
```php
$users = $pdo->table('users')
    ->limit('0,10')
    ->get();
```
You may use the `groupBy` method to group the query results.
```php
$users = $pdo->table('users')
    ->groupBy('role')
    ->get();
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
])->get();
```
The `values` method binds a value to a parameter. Binds a value to a corresponding named placeholder in the SQL statement that was used to prepare the statement.  

### Security
Embryo PDO uses **PDO parameter binding** to protect your application against SQL injection attacks. There is no need to clean strings being passed as bindings.