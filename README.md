# OOP CRUD
CRUD operations performed with the object-oriented programming paradigm in order to challenge my knowledge

## Technologies
- PHP
- MySQL

## How to use
Instantiate the class entity and pass the name of the table you want to handle in its constructor. The database connection settings (such as username, database, host, etc.) can be modified in a file called .env in the root directory


### Index
```php
<?php

declare(strict_types=1);

require_once __DIR__ .  "/vendor/autoload.php";

use App\Common\Enviroment;
use App\Database\Entity;

Enviroment::load(__DIR__);
```

### Enviroment File
```php
<?php

namespace App\Common;

class Enviroment
{
    public static function load(string $directory)
    {
        if (!file_exists("{$directory}/.env")) {
            return false;
        }

        $lines = file("{$directory}/.env");

        foreach ($lines as $line) {
            putenv(trim($line));
        }
    }
}
```

### Entity Class
```php
<?php

namespace App\Database;

use PDO;
use PDOException;
use PDOStatement;

class Entity
{
    private string $host;
    private string $user;
    private string $password;
    private string $database;
    private string $table;
    private PDO $connection;

    public function __construct(string $table)
    {
        $this->host = getenv("DB_HOST");
        $this->user = getenv("DB_USERNAME");
        $this->password = getenv("DB_PASSWORD");
        $this->database = getenv("DB_DATABASE");
        $this->connection = $this->connect();
        $this->table = $table;
    }

    private function connect(): PDO
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8";
            $pdo = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            return $pdo;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    private function execute(string $query, array $params = []): PDOStatement
    {
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);

            return $statement;
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function count(string $where = "", string $fields = "*"): int
    {
        $where = strlen($where) ? "WHERE $where" : "";
        $sql = "SELECT {$fields} FROM {$this->table} {$where}";

        return $this->execute($sql)->rowCount();
    }

    public function create(array $values): int
    {
        $fields = implode(",", array_keys($values));
        $binds = implode(",", array_pad([], count($values), "?"));
        $sql = "INSERT INTO {$this->table}({$fields}) VALUES({$binds})";
        $this->execute($sql, array_values($values));

        return $this->connection->lastInsertId();
    }

    public function read(string $where = "", string $order = "", string $limit = "", $fields = "*"): PDOStatement
    {
        $where = strlen($where) ? "WHERE {$where}" : "";
        $order = strlen($order) ? "ORDER BY {$order}" : "";
        $limit = strlen($limit) ? "LIMIT {$limit}" : "";
        $sql = "SELECT {$fields} FROM {$this->table} {$where} {$order} {$limit}";

        return $this->execute($sql);
    }

    public function update(string $where, array $values): bool
    {
        $fields = implode("= ?, ", array_keys($values));
        $params = array_values($values);
        $sql = "UPDATE {$this->table} SET {$fields} = ? WHERE {$where}";
        $this->execute($sql, $params);

        return true;
    }

    public function delete(string $where): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$where}";
        $this->execute($sql);

        return true;
    }
}
```
