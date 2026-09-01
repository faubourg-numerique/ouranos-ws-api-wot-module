<?php

namespace API\Modules\WoT\Controllers;

use API\Enums\MimeType;
use API\Modules\WoT\Managers\WoTPropertyManager;
use API\Modules\WoT\Managers\WoTThingDescriptionManager;
use API\Managers\WorkspaceManager;
use API\Modules\WoT\Models\WoTProperty;
use API\StaticClasses\Utils;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class WoTPropertyController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private WoTPropertyManager $woTPropertyManager;
    private WoTThingDescriptionManager $woTThingDescriptionManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->woTPropertyManager = new WoTPropertyManager($systemEntityManager);
        $this->woTThingDescriptionManager = new WoTThingDescriptionManager($systemEntityManager);
    }

    public function index(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $query = "hasWorkspace==\"{$workspace->id}\"";
        $woTProperties = $this->woTPropertyManager->readMultiple($query);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTProperties, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $data = API::request()->getDecodedJsonBody();

        $woTProperty = new WoTProperty($data);
        $woTProperty->id = Utils::generateUniqueNgsiLdUrn(WoTProperty::TYPE);

        $woTThingDescription = $this->woTThingDescriptionManager->readOne($woTProperty->hasWoTThingDescription);

        $this->woTPropertyManager->create($woTProperty);

        $woTThingDescription->publishRequired = true;
        $woTThingDescription->deployRequired = true;

        $this->woTThingDescriptionManager->update($woTThingDescription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTProperty, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTProperty = $this->woTPropertyManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTProperty, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTProperty = $this->woTPropertyManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        $woTProperty->update($data);

        $woTThingDescription = $this->woTThingDescriptionManager->readOne($woTProperty->hasWoTThingDescription);

        $this->woTPropertyManager->update($woTProperty);

        $woTThingDescription->publishRequired = true;
        $woTThingDescription->deployRequired = true;

        $this->woTThingDescriptionManager->update($woTThingDescription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTProperty, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTProperty = $this->woTPropertyManager->readOne($id);

        $woTThingDescription = $this->woTThingDescriptionManager->readOne($woTProperty->hasWoTThingDescription);

        $this->woTPropertyManager->delete($woTProperty);

        $woTThingDescription->publishRequired = true;
        $woTThingDescription->deployRequired = true;

        $this->woTThingDescriptionManager->update($woTThingDescription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
