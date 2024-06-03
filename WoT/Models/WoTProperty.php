<?php

namespace API\Modules\WoT\Models;

use API\Models\Entity;
use API\Traits\Updatable;
use Core\Model;

class WoTProperty extends Model
{
    use Updatable;

    const TYPE = "WoTProperty";

    public string $id;
    public string $name;
    public ?string $description = null;
    public string $hasWoTThingDescription;
    public string $hasProperty;
    public string $capacityType;
    public string $capacityValue;
    public string $hasWorkspace;

    public function toEntity(): Entity
    {
        $entity = new Entity();
        $entity->setId($this->id);
        $entity->setType(self::TYPE);
        $entity->setProperty("name", $this->name);
        if (!is_null($this->description)) {
            $entity->setProperty("description", $this->description);
        }
        $entity->setRelationship("hasWoTThingDescription", $this->hasWoTThingDescription);
        $entity->setRelationship("hasProperty", $this->hasProperty);
        $entity->setProperty("capacityType", $this->capacityType);
        $entity->setProperty("capacityValue", $this->capacityValue);
        $entity->setRelationship("hasWorkspace", $this->hasWorkspace);
        return $entity;
    }

    public function fromEntity(Entity $entity): void
    {
        $this->id = $entity->getId();
        $this->name = $entity->getProperty("name");
        if ($entity->propertyExists("description")) {
            $this->description = $entity->getProperty("description");
        }
        $this->hasWoTThingDescription = $entity->getRelationship("hasWoTThingDescription");
        $this->hasProperty = $entity->getRelationship("hasProperty");
        $this->capacityType = $entity->getProperty("capacityType");
        $this->capacityValue = $entity->getProperty("capacityValue");
        $this->hasWorkspace = $entity->getRelationship("hasWorkspace");
    }
}
