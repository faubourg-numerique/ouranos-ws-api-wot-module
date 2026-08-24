<?php

namespace API\Modules\WoT\Controllers;

use API\Enums\MimeType;
use API\Enums\NgsiLdPropertyValueType;
use API\Managers\PropertyManager;
use API\Modules\WoT\Managers\WoTThingDescriptionManager;
use API\Modules\WoT\Managers\WoTPropertyManager;
use API\Modules\WoT\Managers\WoTActionManager;
use API\Modules\WoT\Managers\WoTActionInputPropertyManager;
use API\Modules\WoT\Managers\WoTEventManager;
use API\Modules\WoT\Models\WoTThingDescription;
use API\StaticClasses\Utils;
use Core\API;
use Core\Controller;
use Core\HttpResponseStatusCodes;

class WoTThingDescriptionRootController extends Controller
{
    private PropertyManager $propertyManager;
    private WoTThingDescriptionManager $woTThingDescriptionManager;
    private WoTPropertyManager $woTPropertyManager;
    private WoTActionManager $woTActionManager;
    private WoTActionInputPropertyManager $woTActionInputPropertyManager;
    private WoTEventManager $woTEventManager;

    public function __construct()
    {
        global $systemEntityManager;
        $this->propertyManager = new PropertyManager($systemEntityManager);
        $this->woTThingDescriptionManager = new WoTThingDescriptionManager($systemEntityManager);
        $this->woTPropertyManager = new WoTPropertyManager($systemEntityManager);
        $this->woTActionManager = new WoTActionManager($systemEntityManager);
        $this->woTActionInputPropertyManager = new WoTActionInputPropertyManager($systemEntityManager);
        $this->woTEventManager = new WoTEventManager($systemEntityManager);
    }

    public function index(): void
    {
        $woTThingDescriptions = $this->woTThingDescriptionManager->readMultiple();

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTThingDescriptions, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function store(): void
    {
        $data = API::request()->getDecodedJsonBody();

        $woTThingDescription = new WoTThingDescription($data);
        $woTThingDescription->id = Utils::generateUniqueNgsiLdUrn(WoTThingDescription::TYPE);

        $this->woTThingDescriptionManager->create($woTThingDescription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_CREATED);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTThingDescription, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function show(string $id): void
    {
        $woTThingDescription = $this->woTThingDescriptionManager->readOne($id);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTThingDescription, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function update(string $id): void
    {
        $woTThingDescription = $this->woTThingDescriptionManager->readOne($id);

        $data = API::request()->getDecodedJsonBody();

        $woTThingDescription->update($data);

        $this->woTThingDescriptionManager->update($woTThingDescription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_OK);
        API::response()->setHeader("Content-Type", MimeType::Json->value);
        API::response()->setJsonBody($woTThingDescription, JSON_UNESCAPED_SLASHES);
        API::response()->send();
    }

    public function destroy(string $id): void
    {
        $woTThingDescription = $this->woTThingDescriptionManager->readOne($id);

        $this->woTThingDescriptionManager->delete($woTThingDescription);

        API::response()->setStatusCode(HttpResponseStatusCodes::HTTP_NO_CONTENT);
        API::response()->send();
    }
}
