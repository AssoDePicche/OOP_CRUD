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
