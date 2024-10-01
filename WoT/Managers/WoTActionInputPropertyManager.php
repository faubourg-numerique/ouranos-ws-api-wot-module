<?php

namespace API\Modules\WoT\Managers;

use API\Managers\EntityManager;
use API\Modules\WoT\Models\WoTActionInputProperty;

class WoTActionInputPropertyManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(WoTActionInputProperty $woTActionInputProperty): void
    {
        $entity = $woTActionInputProperty->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): WoTActionInputProperty
    {
        $entity = $this->entityManager->readOne($id);
        $woTActionInputProperty = new WoTActionInputProperty();
        $woTActionInputProperty->fromEntity($entity);
        return $woTActionInputProperty;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, WoTActionInputProperty::TYPE, $query);

        $woTActionInputProperties = [];
        foreach ($entities as $entity) {
            $woTActionInputProperty = new WoTActionInputProperty();
            $woTActionInputProperty->fromEntity($entity);
            if ($idAsKey) $woTActionInputProperties[$woTActionInputProperty->id] = $woTActionInputProperty;
            else $woTActionInputProperties[] = $woTActionInputProperty;
        }

        return $woTActionInputProperties;
    }

    public function update(WoTActionInputProperty $woTActionInputProperty): void
    {
        $entity = $woTActionInputProperty->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(WoTActionInputProperty $woTActionInputProperty): void
    {
        $entity = $woTActionInputProperty->toEntity();
        $this->entityManager->delete($entity);
    }
}
