<?php

namespace API\Modules\WoT\Controllers;

use API\Enums\MimeType;
use API\Modules\WoT\Managers\WoTActionInputPropertyManager;
use API\Managers\WorkspaceManager;
use API\Modules\WoT\Models\WoTActionInputProperty;
use API\StaticClasses\Utils;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class WoTActionInputPropertyController extends Controller
{
    private WorkspaceManager $workspaceManager;
    private WoTActionInputPropertyManager $woTActionInputPropertyManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->workspaceManager = new WorkspaceManager($systemEntityManager);
        $this->woTActionInputPropertyManager = new WoTActionInputPropertyManager($systemEntityManager);
    }

    public function index(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $query = "hasWorkspace==\"{$workspace->id}\"";
        $woTActionInputProperties = $this->woTActionInputPropertyManager->readMultiple($query);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTActionInputProperties, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(string $workspaceId): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $data = API::request()->getDecodedJsonBody();

        $woTActionInputProperty = new WoTActionInputProperty($data);
        $woTActionInputProperty->id = Utils::generateUniqueNgsiLdUrn(WoTActionInputProperty::TYPE);

        $this->woTActionInputPropertyManager->create($woTActionInputProperty);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTActionInputProperty, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTActionInputProperty = $this->woTActionInputPropertyManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTActionInputProperty, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTActionInputProperty = $this->woTActionInputPropertyManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        $woTActionInputProperty->update($data);

        $this->woTActionInputPropertyManager->update($woTActionInputProperty);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTActionInputProperty, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $workspaceId, string $id): void
    {
        $workspace = $this->workspaceManager->readOne($workspaceId);

        $woTActionInputProperty = $this->woTActionInputPropertyManager->readOne($id);

        $this->woTActionInputPropertyManager->delete($woTActionInputProperty);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
