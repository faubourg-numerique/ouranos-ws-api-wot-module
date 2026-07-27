<?php

namespace API\Modules\WoT\Models;

use API\Models\Entity;
use API\Traits\Updatable;
use Core\Model;

class WoTGroup extends Model
{
    use Updatable;

    const TYPE = "WoTGroup";

    public string $id;
    public string $name;

    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setId($this->id);
        $entity->setType(self::TYPE);
        $entity->setProperty("name", $this->name);
        return $entity;
    }

    public function fromEntity(Entity $entity): void
    {
        $this->id = $entity->getId();
        $this->name = $entity->getProperty("name");
    }
}
