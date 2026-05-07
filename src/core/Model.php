<?php
class Model
{
    protected static function db(): PDO
    {
        return Database::getInstance();
    }
}