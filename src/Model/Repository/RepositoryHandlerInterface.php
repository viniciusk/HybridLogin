<?php

namespace HybridLogin\Model\Repository;


interface RepositoryHandlerInterface
{
    public function save(string $entity, array $attributes): bool;

    public function findOne(string $entity, array $attributes): array;
}
