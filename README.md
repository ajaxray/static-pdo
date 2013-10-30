Simple PHP PDO Wrapper
=======================

It's a simple PHP PDO wrapper for small, single database php project.

### Features

* Easy to use.
* Works with MySQL and Sqlite.
* All pubic functions works statically.
* Connection string created internally.
* Simple functions to get Result set, single row and single value.


### How to use

To use this class, first you have to set the connection information using `Db::setConnectionInfo`. Here you have to pass the schema name, username, password, database type and hostname respectively. Database is mysql and hostname is localhost by default.

```
// Connecting to mysql. Using default hostname
Db::setConnectionInfo('schemaname','root', '123456');

// Connecting to mysql. With different host
Db::setConnectionInfo('basecamp','dbuser', 'password', 'mysql',  'http://mysql.abcd.com');

// Connecting to sqlite. Here, the 1st param is sqlite file path.
Db::setConnectionInfo('path/to/filename.db3', null, null,  'sqlite',);
```

Please remember that it will NOT create any connection with the database, yet. Connection will be made on the first time a first query is executed and will be used from them onwards. However, the class will take care of this and you need not bother about it.

You are now ready to run query. For the queries which don't return a result set, you can use Db::execute, this function returns the number of effected rows. The first argument is SQL query (PDO format) and 2nd is optional array of input parameters. Here is an example:

```
// Inserting a user
$user = array('name' =&gt; 'someone', 'pass' =&gt; '123456');
Db::execute('INSERT INTO users(username, password) VALUES(:name, :pass)', $user);

// An update query. You can pass the param directly for single parameter
$updated = Db::execute(“UPDATE users SET status = 'active' WHERE id = ?”, 4);
```

For retrieving result sets, you can use a number of functions: `Db::getValue`,  `Db::getRow` and `Db::getResult`. `Db::getValu`e returns value of a single field. `Db::getRow` and `Db::getResult` returns array of a single row (as single dimension) and multiple rows (as 2 dimension ) respectively.  Parameters are same as `Dd::execute` function.

```
$totlalUsers = Db::getValue('SELECT COUNT(*) FROM users');
$aUserName  =  Db::getValue(“SELECT name from users WHERE id = ?”, 4);
$aUser  =  Db::getRow(“SELECT name, status from users WHERE id = 1”);
$activeUsers = Db::getResult(“SELECT * from users WHERE status = 'active'”);
```

See the DbExamples.php file (in downloaded archive) for more examples. There are some other functions available in the class for using transaction, getting insert id etc. See function references for list of all public functions.

### Function references

Here is the list of public (and static as well) functions of this class.

* `setConnectionInfo($schema, $username = null, $password = null, $database = 'mysql', $hostname = 'localhost')`
* `execute($sql, $params = array())` - Execute a statement and returns number of effected rows
* `getValue($sql, $params = array())` - Execute a statement and returns a single value
* `getRow($sql, $params = array())` - Execute a statement and returns the first row as array
* `getResult($sql, $params = array())` - Execute a statement and returns row(s) as 2D array
* `getLastInsertId($sequenceName = "")` - Returns the last inserted id
* `setFetchMode($fetchMode)` – set the PDO fetch mode. Default is PDO::FETCH_ASSOC
* `getPDOObject()` - Returns a reference of connected PDO object.
* `BeginTransaction()`
* `commitTransaction()`
* `rollbackTransaction()`
* `setDriverOptions(array $options)` – Set PDO driver options to use while preparing statements.

---
Anis uddin Ahmad
http://ajaxray.com
