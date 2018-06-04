<?php

namespace HybridLogin\Model\Repository;

use HybridLogin\Model\AbstractEntity;

interface RepositoryInterface
{
    public function generateUUID(): int;

    public function save(AbstractEntity $entity): bool;

    public function findOneById(int $id): ?array;

    public function findOneByAttributes(array $attributes): ?array;
}
