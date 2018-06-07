<?php

namespace HybridLogin\Model\Repository;


interface RepositoryHandlerInterface
{
    public function save(string $entity, array $attributes): bool;

    /**
     * Search for an object based on its attributes and return an array with the results or null
     * @param string $entity
     * @param array $attributes
     * @return array|null
     */
    public function findOne(string $entity, array $attributes): ?array;
}
