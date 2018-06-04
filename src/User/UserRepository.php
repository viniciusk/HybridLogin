<?php

namespace HybridLogin\User;


use HybridLogin\Model\AbstractEntity;
use HybridLogin\Model\Repository\RepositoryHandlerInterface;
use HybridLogin\Model\Repository\RepositoryInterface;


/**
 * Class UserRepository
 * @package HybridLogin\User
 */
class UserRepository implements RepositoryInterface
{
    private const REPOSITORY_ENTITY = 'user';

    /**
     * @var RepositoryHandlerInterface $handler
     */
    protected $handler;


    /**
     * UserRepository constructor.
     * @param RepositoryHandlerInterface $handler
     */
    public function __construct(RepositoryHandlerInterface $handler)
    {
        $this->handler = $handler;
    }


    /**
     * TODO: implement/use a third party proper UUID generator
     * @return int
     */
    public function generateUUID(): int
    {
        return (int) substr(abs((int) hexdec(uniqid('', true))), 0, 20);
    }


    /**
     * @param AbstractEntity $entity
     * @return bool
     */
    public function save(AbstractEntity $entity): bool
    {
        if (null === $entity->getUUID()) {
            $isNew = true;
            $entity->setUUID($this->generateUUID());
        }
        if (true === $this->handler->save(self::REPOSITORY_ENTITY, $entity->getAttributes())) {
            return true;
        }
        // in case of error, we need to restore the nullable state of new objects' id
        if ($isNew ?? false) {
            $entity->setUUID(null);
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|null
     */
    public function findOneById(int $id): ?array
    {
        return $this->handler->findOne(self::REPOSITORY_ENTITY, ['id' => $id]);
    }


    /**
     * @param array $attributes
     * @return array|null
     */
    public function findOneByAttributes(array $attributes): ?array
    {
        return $this->handler->findOne(self::REPOSITORY_ENTITY, $attributes);
    }
}
