<?php

namespace API\Modules\WoT\Models;

use API\Models\Entity;
use API\Traits\Updatable;
use Core\Model;

class WoTActionInputProperty extends Model
{
    use Updatable;

    const TYPE = "WoTActionInputProperty";

    public string $id;
    public string $name;
    public ?string $description = null;
    public string $propertyType;
    public int|float|null $minimum = null;
    public int|float|null $maximum = null;
    public ?array $enum = null;
    public bool $required;
    public string $hasWoTAction;
    public string $hasWoTThingDescription;
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
        $entity->setProperty("propertyType", $this->propertyType);
        if (!is_null($this->minimum)) {
            $entity->setProperty("minimum", $this->minimum);
        }
        if (!is_null($this->maximum)) {
            $entity->setProperty("maximum", $this->maximum);
        }
        if (!is_null($this->enum)) {
            $entity->setProperty("enum", $this->enum);
        }
        $entity->setProperty("required", $this->required);
        $entity->setRelationship("hasWoTAction", $this->hasWoTAction);
        $entity->setRelationship("hasWoTThingDescription", $this->hasWoTThingDescription);
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
        $this->propertyType = $entity->getProperty("propertyType");
        if ($entity->propertyExists("minimum")) {
            $this->minimum = $entity->getProperty("minimum");
        }
        if ($entity->propertyExists("maximum")) {
            $this->maximum = $entity->getProperty("maximum");
        }
        if ($entity->propertyExists("enum")) {
            $this->enum = $entity->getProperty("enum");
        }
        $this->required = $entity->getProperty("required");
        $this->hasWoTAction = $entity->getRelationship("hasWoTAction");
        $this->hasWoTThingDescription = $entity->getRelationship("hasWoTThingDescription");
        $this->hasWorkspace = $entity->getRelationship("hasWorkspace");
    }
}
