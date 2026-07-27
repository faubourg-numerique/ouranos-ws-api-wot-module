<?php

namespace API\Modules\WoT\Managers;

use API\Managers\EntityManager;
use API\Modules\WoT\Models\WoTGroup;

class WoTGroupManager
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(WoTGroup $woTGroup): void
    {
        $entity = $woTGroup->toEntity();
        $this->entityManager->create($entity);
    }

    public function readOne(string $id): WoTGroup
    {
        $entity = $this->entityManager->readOne($id);
        $woTGroup = new WoTGroup();
        $woTGroup->fromEntity($entity);
        return $woTGroup;
    }

    public function readMultiple(?string $query = null, bool $idAsKey = false): array
    {
        $entities = $this->entityManager->readMultiple(null, WoTGroup::TYPE, $query);

        $woTGroups = [];
        foreach ($entities as $entity) {
            $woTGroup = new WoTGroup();
            $woTGroup->fromEntity($entity);
            if ($idAsKey) $woTGroups[$woTGroup->id] = $woTGroup;
            else $woTGroups[] = $woTGroup;
        }

        return $woTGroups;
    }

    public function update(WoTGroup $woTGroup): void
    {
        $entity = $woTGroup->toEntity();
        $this->entityManager->update($entity);
    }

    public function delete(WoTGroup $woTGroup): void
    {
        $entity = $woTGroup->toEntity();
        $this->entityManager->delete($entity);
    }
}
