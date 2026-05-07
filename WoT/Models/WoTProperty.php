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
    public ?array $enum = null;
    public ?float $maximum = null;
    public ?float $minimum = null;
    public ?bool $observable = null;
    public ?string $propertyType = null;
    public ?bool $readonly = null;
    public ?bool $temporal = null;

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

        if (!is_null($this->enum)) {
            $entity->setProperty("enum", $this->enum);
        }
        if (!is_null($this->maximum)) {
            $entity->setProperty("maximum", $this->maximum);
        }
        if (!is_null($this->minimum)) {
            $entity->setProperty("minimum", $this->minimum);
        }
        if (!is_null($this->observable)) {
            $entity->setProperty("observable", $this->observable);
        }
        if (!is_null($this->propertyType)) {
            $entity->setProperty("propertyType", $this->propertyType);
        }
        if (!is_null($this->readonly)) {
            $entity->setProperty("readonly", $this->readonly);
        }
        if (!is_null($this->temporal)) {
            $entity->setProperty("temporal", $this->temporal);
        }

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

        if ($entity->propertyExists("enum")) {
            $this->enum = $entity->getProperty("enum");
        }
        if ($entity->propertyExists("maximum")) {
            $this->maximum = $entity->getProperty("maximum");
        }
        if ($entity->propertyExists("minimum")) {
            $this->minimum = $entity->getProperty("minimum");
        }
        if ($entity->propertyExists("observable")) {
            $this->observable = $entity->getProperty("observable");
        }
        if ($entity->propertyExists("propertyType")) {
            $this->propertyType = $entity->getProperty("propertyType");
        }
        if ($entity->propertyExists("readonly")) {
            $this->readonly = $entity->getProperty("readonly");
        }
        if ($entity->propertyExists("temporal")) {
            $this->temporal = $entity->getProperty("temporal");
        }
    }
}
