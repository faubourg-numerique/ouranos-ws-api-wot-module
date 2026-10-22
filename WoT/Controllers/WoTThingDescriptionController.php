<?php

namespace API\Modules\WoT\Controllers;

use API\Enums\MimeType;
use API\Managers\PropertyManager;
use API\Modules\WoT\Managers\WoTThingDescriptionManager;
use API\Modules\WoT\Managers\WoTPropertyManager;
use API\Modules\WoT\Managers\WoTActionManager;
use API\Modules\WoT\Managers\WoTActionInputPropertyManager;
use API\Modules\WoT\Managers\WoTEventManager;
use API\Managers\WorkspaceManager;
use API\Modules\WoT\Models\WoTThingDescription;
use API\StaticClasses\Utils;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class WoTThingDescriptionController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private PropertyManager $propertyManager;
    private WoTThingDescriptionManager $woTThingDescriptionManager;
    private WoTPropertyManager $woTPropertyManager;
    private WoTActionManager $woTActionManager;
    private WoTActionInputPropertyManager $woTActionInputPropertyManager;
    private WoTEventManager $woTEventManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->propertyManager = new PropertyManager($systemEntityManager);
        $this->woTThingDescriptionManager = new WoTThingDescriptionManager($systemEntityManager);
        $this->woTPropertyManager = new WoTPropertyManager($systemEntityManager);
        $this->woTActionManager = new WoTActionManager($systemEntityManager);
        $this->woTActionInputPropertyManager = new WoTActionInputPropertyManager($systemEntityManager);
        $this->woTEventManager = new WoTEventManager($systemEntityManager);
    }

    public function index(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $query = "hasWorkspace==\"{$workspace->id}\"";
        $woTThingDescriptions = $this->woTThingDescriptionManager->readMultiple($query);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTThingDescriptions, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $data = API::request()->getDecodedJsonBody();

        $woTThingDescription = new WoTThingDescription($data);
        $woTThingDescription->id = Utils::generateUniqueNgsiLdUrn(WoTThingDescription::TYPE);

        $this->woTThingDescriptionManager->create($woTThingDescription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTThingDescription, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTThingDescription = $this->woTThingDescriptionManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTThingDescription, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTThingDescription = $this->woTThingDescriptionManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        $woTThingDescription->update($data);

        $this->woTThingDescriptionManager->update($woTThingDescription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTThingDescription, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTThingDescription = $this->woTThingDescriptionManager->readOne($id);

        $this->woTThingDescriptionManager->delete($woTThingDescription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }

    public function build(string $id): void
    {
        $woTThingDescription = $this->woTThingDescriptionManager->readOne($id);

        $workspace = $this->workspaceManager->readOne($woTThingDescription->hasWorkspace);

        $query = "hasWorkspace==\"{$workspace->id}\";hasWoTThingDescription==\"{$woTThingDescription->id}\"";
        $woTProperties = $this->woTPropertyManager->readMultiple($query);

        $query = "hasWorkspace==\"{$workspace->id}\";hasWoTThingDescription==\"{$woTThingDescription->id}\"";
        $woTActions = $this->woTActionManager->readMultiple($query);

        $query = "hasWorkspace==\"{$workspace->id}\";hasWoTThingDescription==\"{$woTThingDescription->id}\"";
        $woTEvents = $this->woTEventManager->readMultiple($query);

        $td = [
            "@context" => "https://www.w3.org/2022/wot/td/v1.1",
            "@type" => "tm:ThingModel",
            "title" => $woTThingDescription->name,
            "description" => $woTThingDescription->description ?: $woTThingDescription->name,
            "securityDefinitions" => [
                "nosec_sc" => [
                    "scheme" => "nosec"
                ]
            ],
            "security" => "nosec_sc"
        ];

        if ($woTProperties) {
            $td["properties"] = [];

            foreach ($woTProperties as $woTProperty) {
                $property = $this->propertyManager->readOne($woTProperty->hasProperty);
                $td["properties"][$property->name] = [
                    "title" => $woTProperty->name,
                    "description" => $woTProperty->description ?: $woTProperty->name,
                    "type" => $woTProperty->propertyType ?? strtolower($property->propertyNgsiLdValueType),
                    "observable" => $woTProperty->observable ?? true,
                    "readOnly" => $woTProperty->readonly ?? false
                ];

                if ($woTProperty->minimum !== null) {
                    $td["properties"][$property->name]["minimum"] = $woTProperty->minimum;
                }

                if ($woTProperty->maximum !== null) {
                    $td["properties"][$property->name]["maximum"] = $woTProperty->maximum;
                }

                if ($woTProperty->enum !== null) {
                    $td["properties"][$property->name]["enum"] = $woTProperty->enum;
                }

                if ($woTProperty->temporal !== null) {
                    $td["properties"][$property->name]["temporal"] = $woTProperty->temporal;
                }
            }
        }

        if ($woTActions) {
            $td["actions"] = [];

            foreach ($woTActions as $woTAction) {
                $td["actions"][$woTAction->name] = [
                    "title" => $woTAction->name,
                    "description" => $woTAction->description ?: $woTAction->name
                ];

                $query = "hasWorkspace==\"{$workspace->id}\";hasWoTAction==\"{$woTAction->id}\"";
                $woTActionInputProperties = $this->woTActionInputPropertyManager->readMultiple($query);

                if ($woTActionInputProperties) {
                    $td["actions"][$woTAction->name]["input"] = [
                        "type" => "object",
                        "properties" => []
                    ];

                    foreach ($woTActionInputProperties as $woTActionInputProperty) {
                        $td["actions"][$woTAction->name]["input"]["properties"][$woTActionInputProperty->name] = [
                            "description" => $woTActionInputProperty->description ?: $woTActionInputProperty->name,
                            "type" => $woTActionInputProperty->propertyType
                        ];

                        if ($woTActionInputProperty->metaType !== null) {
                            $td["actions"][$woTAction->name]["input"]["properties"][$woTActionInputProperty->name]["@type"] = $woTActionInputProperty->metaType;
                        }

                        if ($woTActionInputProperty->minimum !== null) {
                            $td["actions"][$woTAction->name]["input"]["properties"][$woTActionInputProperty->name]["minimum"] = $woTActionInputProperty->minimum;
                        }

                        if ($woTActionInputProperty->maximum !== null) {
                            $td["actions"][$woTAction->name]["input"]["properties"][$woTActionInputProperty->name]["maximum"] = $woTActionInputProperty->maximum;
                        }

                        if ($woTActionInputProperty->enum !== null) {
                            $td["actions"][$woTAction->name]["input"]["properties"][$woTActionInputProperty->name]["enum"] = $woTActionInputProperty->enum;
                        }

                        if ($woTActionInputProperty->required) {
                            $td["actions"][$woTAction->name]["input"]["required"][] = $woTActionInputProperty->name;
                        }
                    }
                }
            }
        }

        if ($woTEvents) {
            $td["events"] = [];

            foreach ($woTEvents as $woTEvent) {
                $woTProperty = $this->woTPropertyManager->readOne($woTEvent->hasWoTProperty);
                $property = $this->propertyManager->readOne($woTProperty->hasProperty);
                $td["events"][$woTEvent->name] = [
                    "title" => $woTEvent->name,
                    "description" => $woTEvent->description ?: $woTEvent->name,
                    "data" => [
                        "description" => $woTProperty->description ?: $woTProperty->name,
                        "type" => $woTProperty->propertyType ?? strtolower($property->propertyNgsiLdValueType)
                    ]
                ];

                if ($woTProperty->minimum !== null) {
                    $td["events"][$woTEvent->name]["data"]["minimum"] = $woTProperty->minimum;
                }

                if ($woTProperty->maximum !== null) {
                    $td["events"][$woTEvent->name]["data"]["maximum"] = $woTProperty->maximum;
                }

                if ($woTProperty->enum !== null) {
                    $td["events"][$woTEvent->name]["data"]["enum"] = $woTProperty->enum;
                }
            }
        }

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($td, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }
}
