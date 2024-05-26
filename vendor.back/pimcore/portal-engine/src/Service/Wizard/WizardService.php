<?php

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Commercial License (PCL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PCL
 */

namespace Pimcore\Bundle\PortalEngineBundle\Service\Wizard;

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\ControllerReference;
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\ContentConfig;
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\DataPool\DataPoolConfig;
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\DataPool\WorkspaceConfig;
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\LanguageVariant;
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\PortalConfig;
use Pimcore\Bundle\PortalEngineBundle\Enum\Routing;
use Pimcore\Bundle\PortalEngineBundle\Enum\Wizard;
use Pimcore\Bundle\PortalEngineBundle\EventSubscriber\DocumentConfigSubscriber;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\FrontendBuildService;
use Pimcore\Event\Model\DocumentEvent;
use Pimcore\Model\Asset;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Editable\Checkbox;
use Pimcore\Model\Document\Editable\Relation;
use Pimcore\Model\Document\Folder;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Document\Snippet;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Site;
use Pimcore\Model\Tool\TmpStore;
use Pimcore\Tool\Console;

class WizardService
{
    /**
     * @var FrontendBuildService
     */
    protected $frontendBuildService;

    /**
     * @var DocumentConfigSubscriber
     */
    protected $documentConfigSubscriber;

    /**
     * @param FrontendBuildService $frontendBuildService
     * @param DocumentConfigSubscriber $documentConfigSubscriber
     */
    public function __construct(FrontendBuildService $frontendBuildService, DocumentConfigSubscriber $documentConfigSubscriber)
    {
        $this->frontendBuildService = $frontendBuildService;
        $this->documentConfigSubscriber = $documentConfigSubscriber;
    }

    /**
     * @param array $data
     *
     * @return string
     *
     * @throws \Exception
     */
    public function startWizard(array $data): string
    {
        $tmpStorekey = 'portal-engine_wizard_' . uniqid();
        TmpStore::add($tmpStorekey, [Wizard::PORTAL_CONFIG => $data], Wizard::TMP_STORE_TAG, Wizard::LIFETIME);
        Console::runPhpScriptInBackground(PIMCORE_PROJECT_ROOT . '/bin/console', ['portal-engine:wizard', $tmpStorekey], PIMCORE_LOG_DIRECTORY . '/portal-wizard-output.log');

        return $tmpStorekey;
    }

    /**
     * @param string $tmpStoreKey
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function isFinished(string $tmpStoreKey)
    {
        $data = $this->getTmpStoreData($tmpStoreKey);

        if (empty($data)) {
            return true;
        }

        return $data[Wizard::FINISHED] ?? false;
    }

    /**
     * @param string $tmpStoreKey
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function isSuccess(string $tmpStoreKey)
    {
        $data = $this->getTmpStoreData($tmpStoreKey);

        return $data[Wizard::SUCCESS] ?? false;
    }

    /**
     * @param string $tmpStoreKey
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getStatusMessage(string $tmpStoreKey): ?string
    {
        $data = $this->getTmpStoreData($tmpStoreKey);

        return $data[Wizard::WIZARD_STATUS_MESSAGE] ?? null;
    }

    /**
     * @param string $tmpStoreKey
     *
     * @return |null
     *
     * @throws \Exception
     */
    public function getPortalDocumentId(string $tmpStoreKey)
    {
        $data = $this->getTmpStoreData($tmpStoreKey);

        return $data[Wizard::PORT_DOCUMENT_ID] ?? null;
    }

    /**
     * @param string $tmpStoreKey
     *
     * @throws \Exception
     */
    public function createPortal(string $tmpStoreKey)
    {
        $data = $this->getTmpStoreData($tmpStoreKey);
        $config = $data[Wizard::PORTAL_CONFIG] ?? null;

        if (!is_array($config)) {
            return;
        }

        $portalData = $this->getFirstDataByType($config, Wizard::TYPE_PORTAL);

        $this->updateTmpStoreData($tmpStoreKey, $config, 'Creating Portal Document');
        $portal = $this->createPortalDocument($portalData, $tmpStoreKey, $config);
        if (!$portal) {
            // portal could not be created => trigger error
            $this->updateTmpStoreData(
                $tmpStoreKey,
                $config,
                'Portal Document could not be created',
                null,
                true,
                false,
                true
            );
        } else {
            $this->updateTmpStoreData($tmpStoreKey, $config, 'Creating Language Roots');
            $this->createLanguageRoots($portal, $portalData);

            $this->updateTmpStoreData($tmpStoreKey, $config, 'Creating Footer Snippets');
            $this->createFooterSnippets($portal, $portalData);

            $this->createDataPools($portal, $config, $tmpStoreKey);

            $this->updateTmpStoreData(
                $tmpStoreKey,
                $config,
                'Portal created',
                $portal->getId(),
                true,
                true
            );
        }
    }

    protected function getTmpStoreData(string $tmpStoreKey)
    {
        if (strpos($tmpStoreKey, 'portal-engine_wizard_') !== 0) {
            throw new \Exception('invalid tmp store key');
        }
        if (!$tmpStore = TmpStore::get($tmpStoreKey)) {
            throw new \Exception('tmp store data not found');
        }

        return $tmpStore->getData();
    }

    protected function updateTmpStoreData(
        string $tmpStoreKey,
        array $config,
        ?string $message = null,
        ?int $documentId = null,
        bool $finished = false,
        bool $success = false,
        bool $error = false
    ) {
        TmpStore::set(
            $tmpStoreKey,
            [
                Wizard::PORTAL_CONFIG => $config,
                Wizard::PORT_DOCUMENT_ID => $documentId,
                Wizard::WIZARD_STATUS_MESSAGE => $message,
                Wizard::FINISHED => $finished,
                Wizard::SUCCESS => $success,
                Wizard::ERROR => $error
            ],
            Wizard::TMP_STORE_TAG,
            Wizard::LIFETIME
        );
    }

    protected function createPortalDocument(array $data, string $tmpStoreKey, array $config): ?Page
    {
        $portalName = $data['portalName'] ?? null;
        $domain = $data['domain'] ?? null;
        $availableLanguages = $this->getLanguages($data);
        $logo = $data['logo'] ?? null;
        $backgroundImage = $data['loginBackgroundImage'] ?? null;

        if (empty($portalName) || empty($domain) || empty($availableLanguages)) {
            return null;
        }
        $hasMultipleLanguages = $this->hasMultipleLanguages($data);

        $page = new Page();
        $page->setPublished(true);
        $page->setKey(Service::getValidKey($portalName, 'document'));
        $page->setParentId(1);
        $page->setController(ControllerReference::PORTAL_PAGE);
        $page->setKey(Service::getUniqueKey($page));
        $page->setRawEditable(PortalConfig::PORTAL_NAME, 'input', $portalName);
        if ($logo && $logoAsset = Asset::getByPath($logo)) {
            $page->setRawEditable(PortalConfig::LOGO, 'image', ['id' => $logoAsset->getId()]);
        }
        if ($backgroundImage && $backgroundImageAsset = Asset::getByPath($backgroundImage)) {
            $page->setRawEditable(PortalConfig::BACKGROUND_IMAGE, 'image', ['id' => $backgroundImageAsset->getId()]);
        }
        $page->setRawEditable(PortalConfig::ENABLE_LANGUAGE_REDIRECT, 'checkbox', $hasMultipleLanguages);
        $page->save();
        $site = new Site();
        $site->setMainDomain($domain);
        $site->setDomains([]);
        $site->setRootId($page->getId());
        $site->save();

        $this->documentConfigSubscriber->triggerUpdatePortalsJson(new DocumentEvent($page));

        //needed due to some strange cache behavior
        $this->updateTmpStoreData($tmpStoreKey, $config, 'Clearing site cache');
        $site->clearDependentCache();

        return $page;
    }

    protected function createLanguageRoots(Page $portalPage, array $data)
    {
        if (!$this->hasMultipleLanguages($data)) {
            $portalPage->setProperty(Routing::NAVIGATION_ROOT_PROPERTY, 'document', $portalPage, false, true);
            $portalPage->save();

            return;
        }

        $service = new Document\Service();

        $firstLanguageRoot = null;
        foreach ($this->getLanguages($data) as $language) {
            $languageRoot = (new Page())
                ->setController(ControllerReference::PORTAL_CONTENT_PAGE)
                ->setPublished(true)
                ->setParent($portalPage)
                ->setProperty('language', 'select', $language, false, true)
                ->setKey(Service::getValidKey($language, 'document'))
                ->save()
            ;

            if (is_null($firstLanguageRoot)) {
                $portalPage->setProperty(Routing::NAVIGATION_ROOT_PROPERTY, 'document', $languageRoot, false, true);
                $firstLanguageRoot = $languageRoot;
            } else {
                $languageRoot->setProperty(Routing::NAVIGATION_ROOT_PROPERTY, 'document', $languageRoot, false, true);
                $service->addTranslation($firstLanguageRoot, $languageRoot);
            }
            $languageRoot->save();
        }
    }

    protected function createFooterSnippets(Page $portalPage, array $data)
    {
        $hasMultipleLanguages = $this->hasMultipleLanguages($data);

        $i = 0;
        foreach ($this->getLanguages($data) as $language) {
            $i++;
            $parentFolder = new Folder();
            $parentFolder->setKey('snippets');
            $parentFolder->setParent(
                $hasMultipleLanguages
                    ? Page::getByPath($portalPage->getRealFullPath() . '/' . $this->languageToKey($language))
                    : $portalPage
            );
            $parentFolder->save();

            $footerSnippet = (new Snippet())
                ->setKey('footer')
                ->setPublished(true)
                ->setController(ControllerReference::SNIPPET_FOOTER)
                ->setParent($parentFolder)
                ->save();

            $publicFooterSnippet = (new Snippet())
                ->setKey('public-footer')
                ->setPublished(true)
                ->setController(ControllerReference::SNIPPET_FOOTER)
                ->setParent($parentFolder)
                ->save();

            if ($i == 1) {
                $portalPage->setRawEditable(PortalConfig::FOOTER_SNIPPET, 'relation', [
                    'id' => $footerSnippet->getId(),
                    'type' => 'document',
                    'subtype' => 'snippet',
                ]);

                $portalPage->setRawEditable(PortalConfig::PUBLIC_FOOTER_SNIPPET, 'relation', [
                    'id' => $publicFooterSnippet->getId(),
                    'type' => 'document',
                    'subtype' => 'snippet',
                ]);

                $portalPage->save();
            }
        }
    }

    protected function createDataPools(Page $portalPage, array $data, string $tmpStoreKey)
    {
        $languages = $this->getLanguages($this->getFirstDataByType($data, Wizard::TYPE_PORTAL));

        foreach ($data as $item) {
            $this->updateTmpStoreData($tmpStoreKey, $data, 'Creating Data Pool ' . ($item['dataPoolName'] ?? ''));
            $type = $item['type'] ?? null;
            if ($type === Wizard::TYPE_ASSET) {
                $this->createAssetDataPool($portalPage, $item, $languages);
            } elseif ($type === Wizard::TYPE_OBJECT) {
                $this->createObjectDataPool($portalPage, $item, $languages);
            }
        }
    }

    protected function createAssetDataPool(Page $portalPage, array $data, array $languages)
    {
        $dataPoolName = $data['dataPoolName'] ?? null;
        $icon = $data['icon'] ?? null;
        $enableFolderNavigation = $data['enableFolderNavigation'] ?? null;
        $enableTagNavigation = $data['enableTagNavigation'] ?? null;
        $availabeDownloadThumbnails = $data['availableDownloadThumbnails'] ?? null;
        $directDownloadShortcuts = $data['directDownloadShortcuts'] ?? null;
        $availabeDownloadFormats = $data['availableDownloadFormats'] ?? null;
        $visibleLanguages = $data['visibleLanguages'] ?? null;
        $editableLanguages = $data['editableLanguages'] ?? null;

        if (empty($dataPoolName)) {
            return;
        }

        $i = 0;
        $dataPoolDocument = null;
        foreach ($languages as $language) {
            $i++;
            if ($i === 1) {
                $dataPoolDocument = (new Page())
                    ->setController(ControllerReference::DATA_POOL_ASSETS_LIST)
                    ->setPublished(true)
                    ->setKey(Service::getValidKey($dataPoolName, 'document'))
                    ->setParent($this->getLanguageRoot($portalPage, $languages, $language))
                    ->setProperty('language', 'select', $language, false, true)
                    ->setProperty('navigation_name', 'input', $dataPoolName)
                    ->setTitle($dataPoolName)
                    ->setRawEditable(ContentConfig::NAVIGATION_ICON, 'select', $icon)
                    ->setRawEditable(DataPoolConfig::ENABLE_FOLDER_NAVIGATION, 'checkbox', $enableFolderNavigation)
                    ->setRawEditable(DataPoolConfig::ENABLE_TAG_NAVIGATION, 'checkbox', $enableTagNavigation)
                    ->setRawEditable(DataPoolConfig::AVAILABLE_DOWNLOAD_THUMBNAILS, 'multiselect', $availabeDownloadThumbnails)
                    ->setRawEditable(AssetConfig::DIRECT_DOWNLOAD_SHORTCUTS, 'multiselect', $directDownloadShortcuts)
                    ->setRawEditable(DataPoolConfig::AVAILABLE_DOWNLOAD_FORMATS, 'multiselect', $availabeDownloadFormats)
                    ->setRawEditable(DataPoolConfig::VISIBLE_LANGUAGES, 'multiselect', $visibleLanguages)
                    ->setRawEditable(DataPoolConfig::EDITABLE_LANGUAGES, 'multiselect', $editableLanguages)
                    ->setProperty('navigation_name', 'text', $dataPoolName)
                ;

                $this->addWorkspaceBlock($dataPoolDocument, 'asset', 'folder');
                $this->addWorkspacePermissions($dataPoolDocument, [
                    WorkspaceConfig::PERMISSION_VIEW,
                    WorkspaceConfig::PERMISSION_DOWNLOAD,
                    WorkspaceConfig::PERMISSION_EDIT,
                    WorkspaceConfig::PERMISSION_UPDATE,
                    WorkspaceConfig::PERMISSION_CREATE,
                    WorkspaceConfig::PERMISSION_DELETE,
                    WorkspaceConfig::PERMISSION_SUBFOLDER,
                ]);
                $dataPoolDocument->save();

                continue;
            }

            // Create reference document in case of multiple languages
            $this->createLanguageReference($portalPage, $dataPoolDocument, $languages, $language, $dataPoolName, $icon);
        }
    }

    protected function createObjectDataPool(Page $portalPage, array $data, array $languages)
    {
        $dataPoolName = $data['dataPoolName'] ?? null;
        $icon = $data['icon'] ?? null;
        $classDefinition = $data['classDefinition'] ?? null;
        $detailPageLayout = $data['detailPageLayout'] ?? null;
        $enableFolderNavigation = $data['enableFolderNavigation'] ?? null;
        $enableTagNavigation = $data['enableTagNavigation'] ?? null;
        $availabeDownloadThumbnails = $data['availableDownloadThumbnails'] ?? null;
        $availabeDownloadFormats = $data['availableDownloadFormats'] ?? null;
        $visibleLanguages = $data['visibleLanguages'] ?? null;

        if (empty($dataPoolName)) {
            return;
        }

        $i = 0;
        $dataPoolDocument = null;
        foreach ($languages as $language) {
            $i++;
            if ($i === 1) {
                /**
                 * @var Page $dataPoolDocument
                 */
                $dataPoolDocument = (new Page())
                    ->setController(ControllerReference::DATA_POOL_DATA_OBJECTS_LIST)
                    ->setPublished(true)
                    ->setKey(Service::getValidKey($dataPoolName, 'document'))
                    ->setParent($this->getLanguageRoot($portalPage, $languages, $language))
                    ->setProperty('language', 'select', $language, false, true)
                    ->setProperty('navigation_name', 'input', $dataPoolName)
                    ->setTitle($dataPoolName)
                    ->setRawEditable(ContentConfig::NAVIGATION_ICON, 'select', $icon)
                    ->setRawEditable(DataObjectConfig::DATA_OBJECT_CLASS, 'select', $classDefinition)
                    ->setRawEditable(DataObjectConfig::DETAIL_PAGE_LAYOUT, 'select', $detailPageLayout)
                    ->setRawEditable(DataPoolConfig::ENABLE_FOLDER_NAVIGATION, 'checkbox', $enableFolderNavigation)
                    ->setRawEditable(DataPoolConfig::ENABLE_TAG_NAVIGATION, 'checkbox', $enableTagNavigation)
                    ->setRawEditable(DataPoolConfig::AVAILABLE_DOWNLOAD_THUMBNAILS, 'multiselect', $availabeDownloadThumbnails)
                    ->setRawEditable(DataPoolConfig::AVAILABLE_DOWNLOAD_FORMATS, 'multiselect', $availabeDownloadFormats)
                    ->setRawEditable(DataPoolConfig::VISIBLE_LANGUAGES, 'multiselect', $visibleLanguages)
                    ->setProperty('navigation_name', 'text', $dataPoolName)
                ;

                $this->addWorkspaceBlock($dataPoolDocument, 'object', 'folder');
                $this->addWorkspacePermissions($dataPoolDocument, [
                    WorkspaceConfig::PERMISSION_VIEW,
                    WorkspaceConfig::PERMISSION_DOWNLOAD,
                ]);
                $dataPoolDocument->save();

                continue;
            }

            // Create reference document in case of multiple languages
            $this->createLanguageReference($portalPage, $dataPoolDocument, $languages, $language, $dataPoolName, $icon);
        }
    }

    protected function addWorkspaceBlock(Page $dataPoolDocument, string $type, string $subtype)
    {
        $parentBlockNames = [DataPoolConfig::WORKSPACE_DEFINITION];
        $id = Document\Editable::buildChildEditableName(WorkspaceConfig::WORKSPACE_PATH, 'relation', $parentBlockNames, 1);
        $editable = new Relation();
        $editable->setDataFromEditmode([
            'id' => 1,
            'type' => $type,
            'subtype' => $subtype,
        ]);
        $editable->setParentBlockNames($parentBlockNames);
        $editable->setName($id);
        $dataPoolDocument->setEditable($editable);
        $dataPoolDocument->setRawEditable(DataPoolConfig::WORKSPACE_DEFINITION, 'block', [1]);
    }

    protected function addWorkspacePermissions(Page $dataPoolDocument, array $permissions)
    {
        $parentBlockNames = [DataPoolConfig::WORKSPACE_DEFINITION];
        foreach ($permissions as $permission) {
            $id = Document\Editable::buildChildEditableName($permission, 'checkbox', $parentBlockNames, 1);
            $editable = new Checkbox();
            $editable->setDataFromEditmode(true);
            $editable->setParentBlockNames($parentBlockNames);
            $editable->setName($id);
            $dataPoolDocument->setEditable($editable);
        }
    }

    protected function createLanguageReference(Page $portalPage, Page $targetPage, array $languages, string $language, string $key, string $icon = null): Page
    {
        $languageReference = (new Page())
            ->setController(ControllerReference::LANGUAGE_VARIANT_DATA_POOL)
            ->setPublished(true)
            ->setParent($this->getLanguageRoot($portalPage, $languages, $language))
            ->setKey(Service::getValidKey($key, 'document'))
            ->setProperty('navigation_name', 'text', $key)
            ->setRawEditable(ContentConfig::NAVIGATION_ICON, 'select', $icon)
            ->setRawEditable(LanguageVariant::REFERENCED_DOCUMENT, 'relation', [
                'id' => $targetPage->getId(),
                'type' => 'document',
                'subtype' => 'snippet',
            ])
            ->save();

        $service = new Document\Service;
        $service->addTranslation($targetPage, $languageReference);

        return $languageReference;
    }

    protected function getLanguageRoot(Page $portalPage, array $allLanguages, string $language): Page
    {
        if (sizeof($allLanguages) < 2) {
            return $portalPage;
        }

        return Page::getByPath($portalPage->getRealFullPath() . '/' . $this->languageToKey($language));
    }

    protected function hasMultipleLanguages(array $data): bool
    {
        return sizeof($this->getLanguages($data)) > 1;
    }

    protected function getLanguages(array $data): array
    {
        return $data['availableLanguages'] ?? [];
    }

    protected function languageToKey(string $language): string
    {
        return str_replace('_', '-', $language);
    }

    protected function getFirstDataByType(array $data, string $type): array
    {
        foreach ($data as $item) {
            if ($item['type'] ?? null === $type) {
                return $item;
            }
        }

        return [];
    }
}
