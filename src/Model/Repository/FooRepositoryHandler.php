<?php

namespace HybridLogin\Model\Repository;

/**
 * Class FooRepositoryHandler
 * @package HybridLogin\Model\Repository
 */
class FooRepositoryHandler implements RepositoryHandlerInterface
{
    /**
     * @var array a list of the elements handled by this foo
     */
    private $fooList = [];


    /**
     * @param string $entity
     * @param array $attributes
     * @return bool
     */
    public function save(string $entity, array $attributes): bool
    {
        if (!empty($attributes)) {
            $this->fooList[$entity][] = $attributes;
            return true;
        }
        return false;
    }


    /**
     * @inheritdoc
     */
    public function findOne(string $entity, array $attributes): ?array
    {
        if (empty($this->fooList[$entity])) {
            return null;
        }
        foreach ($this->fooList[$entity] as $foo) {
            if (array_intersect_assoc($attributes, $foo) === $attributes) {
                return $foo;
            }
        }
        return null;
    }

}
