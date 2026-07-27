<?php

namespace API\Modules\WoT\Controllers;

use API\Enums\MimeType;
use API\Modules\WoT\Managers\WoTGroupManager;
use API\Modules\WoT\Models\WoTGroup;
use API\StaticClasses\Utils;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class WoTGroupController extends Controller
{
    private WoTGroupManager $woTGroupManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->woTGroupManager = new WoTGroupManager($systemEntityManager);
    }

    public function index(): void
    {
        $woTGroups = $this->woTGroupManager->readMultiple();

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTGroups, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(): void
    {
        $data = API::request()->getDecodedJsonBody();

        $woTGroup = new WoTGroup($data);
        $woTGroup->id = Utils::generateUniqueNgsiLdUrn(WoTGroup::TYPE);

        $this->woTGroupManager->create($woTGroup);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTGroup, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $id): void
    {
        $woTGroup = $this->woTGroupManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTGroup, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $id): void
    {
        $woTGroup = $this->woTGroupManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        $woTGroup->update($data);

        $this->woTGroupManager->update($woTGroup);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTGroup, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $id): void
    {
        $woTGroup = $this->woTGroupManager->readOne($id);

        $this->woTGroupManager->delete($woTGroup);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
