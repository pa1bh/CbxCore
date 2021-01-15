<?php


namespace app\core\db;

use app\core\Application;
use app\core\Model;

abstract class DbModal extends Model
{
    abstract public static function tableName(): string;

    abstract public function attributes(): array;

    abstract public static function primaryKey(): string;

    public function save(): bool
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);

        $statement = self::prepare("INSERT INTO $tableName
                    (" . implode(',', $attributes) . ")
                    VALUES
                    (" . implode(',', $params) . ")
        ");

        //Debug::dump($statement, $params, $attributes);
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        $statement->execute();
        return true;
    }

    public static function prepare(string $query): bool|\PDOStatement
    {
        return Application::$app->db->pdo->prepare($query);
    }

    public static function findOne($where) // [email =? bas@cybox.l, firstname => bas]
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);

        // SELECT * FROM tabel WHERE email => :email NAD firstname = :firstname
        $whereSql = implode( " AND ", array_map( fn($attr) => "$attr = :$attr", $attributes));

        $statement = self::prepare("SELECT * FROM $tableName WHERE $whereSql");

        foreach ($where as $key => $value){
            $statement->bindParam(":$key", $value);
        }

        $statement->execute();
        return $statement->fetchObject(static::class);
    }
}