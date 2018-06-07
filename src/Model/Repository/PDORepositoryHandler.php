<?php

namespace HybridLogin\Model\Repository;


use configuration\DB;
use PDO;

/**
 * Class PDORepositoryHandler
 * @package HybridLogin\Model\Repository
 *
 * @property \PDO $pdo;
 */
class PDORepositoryHandler implements RepositoryHandlerInterface
{
    /**
     * @var PDO $pdo the database handler
     */
    private $pdo;


    public function __construct()
    {
        $this->pdo = new PDO('mysql:host='.DB::HOST.';dbname='.DB::DATABASE,DB::USER,DB::PASSWORD);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }


    /**
     * @param array $attributes
     * @return string
     */
    private function sqlfyAttributesForInsert(array $attributes): string
    {
        $attributesCounter = \count($attributes);
        $processedAttributesCounter = 0;
        $sqlfiedFields = '';
        $sqlfiedValues = '';
        foreach ($attributes as $key => $value) {
            $counter = ++$processedAttributesCounter;
            $sqlfiedFields .= "`{$key}`" . (($counter < $attributesCounter) ? ', ' : '');
            $sqlfiedValues .= $this->pdo->quote($value) . (($counter < $attributesCounter) ? ', ' : '');
        }
        return "({$sqlfiedFields}) VALUES ({$sqlfiedValues})";
    }


    /**
     * @param array $attributes
     * @return string
     */
    private function sqlfyAttributesForUpdate(array $attributes): string
    {
        $sqlfiedAttributes = '';
        foreach ($attributes as $key => $value) {
            $sqlfiedAttributes .= "`{$key}` = ". $this->pdo->quote($value);
        }
        return $sqlfiedAttributes;
    }


    /**
     * @param string $entity
     * @param array $attributes
     * @return bool
     */
    public function save(string $entity, array $attributes): bool
    {
        $sql = $this->sqlfyAttributesForInsert($attributes);
        $sql = "INSERT INTO `{$entity}` {$sql};";
        return $this->pdo->exec($sql) === 1;
    }


    /**
     * @inheritdoc
     */
    public function findOne(string $entity, array $attributes): ?array
    {
        $sql = "SELECT * FROM  `{$entity}` WHERE {$this->sqlfyAttributesForUpdate($attributes)}";
        $ret = $this->pdo->query($sql);
        $result = $ret->fetch(PDO::FETCH_ASSOC);
        return \is_array($result) ? $result : null;
    }
}
