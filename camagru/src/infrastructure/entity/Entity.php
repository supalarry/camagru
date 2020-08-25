<?php

require_once 'IEntity.php';
require_once '/var/www/camagru/src/infrastructure/MysqlConnection.php';

class Entity implements IEntity
{
    protected $table = '';
    protected $columns = [];
    protected $hidden = [];
    protected $connection;

    function __construct()
    {
        $this->connection = MysqlConnection::connect();
    }

    function save()
    {
        $query = $this->getSaveQuery();
        try {
            $stmt = $this->getConnection()->prepare($query);
            $this->connection->beginTransaction();
            $stmt->execute($this->getValuesAsArray());
            $this->connection->commit();
        } catch (PDOException $error) {
            $this->connection->rollBack();
            echo 'Error while saving entity: ' . $error->getMessage();
        }
    }

    function getSaveQuery()
    {
        $table = $this->getTable();
        $columnsAsString = $this->getColumnsAsString();
        $columns = $this->getColumns();
        $columnsCount = count($columns);
        $values = '';

        for ($x = 0; $x < $columnsCount; $x++) {
            $values = ($x === $columnsCount - 1 ? $values . "?" : $values . "?,");
        }

        $query = "INSERT INTO {$table} ({$columnsAsString}) VALUES ({$values})";
        return $query;
    }

    function getTable(): string
    {
        return $this->table;
    }

    function getColumns(): array
    {
        return $this->columns;
    }

    function getHidden(): array
    {
        return $this->hidden;
    }

    function getColumnsAsString(): string
    {
        return implode(", ", $this->getColumns());
    }

    function getValuesAsArray(): array
    {
        $valuesArray = [];
        $columns = $this->getColumns();
        foreach ($columns as $column) {
            array_push($valuesArray, $this->{$column});
        }
        return $valuesArray;
    }

    function getConnection()
    {
        return $this->connection;
    }

    function toArray()
    {
        $array = [];
        foreach ($this->getColumns() as $column) {
            if (!in_array($column, $this->getHidden())) {
                $array[$column] = $this->{$column};
            }
        }
        return $array;
    }
}