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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataPool;

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\ControllerReference;
use Pimcore\Bundle\PortalEngineBundle\Enum\Routing;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Document\LanguageVariantService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Db;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Site;

class DataPoolConfigService
{
    /**
     * @var DataPoolConfigInterface
     */
    protected $currentDataPoolConfig;

    /**
     * @var DocumentResolver $documentResolver
     */
    protected $documentResolver;

    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * @var LanguageVariantService
     */
    protected $languageVariantService;

    /**
     * @var array
     */
    protected $siteDataPoolConfigs = [];

    /**
     * DataPoolConfigService constructor.
     *
     * @param DocumentResolver $documentResolver
     * @param PortalConfigService $portalConfigService
     * @param LanguageVariantService $languageVariantService
     */
    public function __construct(DocumentResolver $documentResolver, PortalConfigService $portalConfigService, LanguageVariantService $languageVariantService)
    {
        $this->documentResolver = $documentResolver;
        $this->portalConfigService = $portalConfigService;
        $this->languageVariantService = $languageVariantService;
    }

    /**
     * @param Document $document
     *
     * @return DataPoolConfigInterface|null
     */
    public function getDataPoolConfigForDocument(Document $document = null): ?DataPoolConfigInterface
    {
        if (empty($document)) {
            return null;
        }

        $languageVariantDocument = null;
        if ($this->languageVariantService->isLanguageVariantDocument($document)) {
            $languageVariantDocument = $document;
            $document = $this->languageVariantService->getReferencedDocument($document);
        }

        if ($this->isDataObjectConfigDocument($document)) {
            return new DataObjectConfig($document, $languageVariantDocument);
        } elseif ($this->isAssetConfigDocument($document)) {
            return new AssetConfig($document, $languageVariantDocument);
        }

        return null;
    }

    /**
     * @return ClassDefinition|null
     *
     * @throws \Exception
     */
    public function getCurrentClassDefinition(): ?ClassDefinition
    {
        /** @var ClassDefinition $currentClassDefinition */
        $currentClassDefinition = null;
        /** @var DataObjectConfig $currentDataPoolConfig */
        $currentDataPoolConfig = $this->getCurrentDataPoolConfig();

        if ($currentDataPoolConfig instanceof DataObjectConfig && $currentDataPoolConfig->getDataObjectClass()) {
            $currentClassDefinition = ClassDefinition::getById($currentDataPoolConfig->getDataObjectClass());
        }

        return $currentClassDefinition;
    }

    /**
     * @return DataPoolConfigInterface|null
     */
    public function getCurrentDataPoolConfig(): ?DataPoolConfigInterface
    {
        if (empty($this->currentDataPoolConfig)) {
            $document = $this->documentResolver->getDocument();

            return $this->getDataPoolConfigForDocument($document);
        }

        return $this->currentDataPoolConfig;
    }

    /**
     * @param DataPoolConfigInterface $currentDataPoolConfig
     */
    public function setCurrentDataPoolConfig(DataPoolConfigInterface $currentDataPoolConfig): void
    {
        $this->currentDataPoolConfig = $currentDataPoolConfig;
    }

    /**
     * @param int $documentId
     */
    public function setCurrentDataPoolConfigById(int $documentId)
    {
        if (($config = $this->getDataPoolConfigById($documentId))) {
            $this->currentDataPoolConfig = $config;
        }
    }

    /**
     * @param int $documentId
     * @param bool $respectLanguageVariants
     *
     * @return DataPoolConfigInterface|null
     */
    public function getDataPoolConfigById(int $documentId, bool $respectLanguageVariants = false)
    {
        $document = Page::getById($documentId);

        if ($respectLanguageVariants) {
            $document = $this->languageVariantService->getLanguageVariant($document);
        }

        if ($document && ($config = $this->getDataPoolConfigForDocument($document))) {
            return $config;
        }

        return null;
    }

    /**
     * @param Document $page
     *
     * @return bool
     */
    public function isDataObjectConfigDocument(?Document $page): bool
    {
        if (!$page instanceof Page) {
            return false;
        }

        return $page->getController() === ControllerReference::DATA_POOL_DATA_OBJECTS_LIST;
    }

    /**
     * @param Document $page
     *
     * @return bool
     */
    public function isDataPoolConfigDocument(?Document $page, bool $respectLanguageVariants = false): bool
    {
        if ($respectLanguageVariants) {
            return $this->isDataObjectConfigDocument($page)
                || $this->isAssetConfigDocument($page)
                || $this->languageVariantService->isLanguageVariantDocument($page);
        }

        return $this->isDataObjectConfigDocument($page)
            || $this->isAssetConfigDocument($page);
    }

    /**
     * @param Document|null $page
     *
     * @return bool
     */
    protected function isAssetConfigDocument(?Document $page): bool
    {
        if (!$page instanceof Page) {
            return false;
        }

        return $page->getController() === ControllerReference::DATA_POOL_ASSETS_LIST;
    }

    /**
     * Get all document from given or current site which dataPool controller reference
     *
     * @return Document[]
     *
     * @throws \Exception
     */
    public function getDataPoolDocumentsFromSite(Site $site = null, bool $respectLanguageVariants = false)
    {
        $documentIds = $this->getDataPoolDocumentIdsFromSite($site, $respectLanguageVariants);

        /** @var Document[] $documents */
        $documents = array_map(function ($documentId) {
            return Document::getById((int)$documentId);
        }, $documentIds);

        return $documents;
    }

    /**
     * @param Site|null $site
     * @param bool $respectLanguageVariants
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getDataPoolDocumentIdsFromSite(Site $site = null, bool $respectLanguageVariants = false): array
    {
        $site = empty($site) ? Site::getCurrentSite() : $site;
        $parentPath = $site->getRootDocument()->getRealFullPath();
        $languageVariantsControllerCondition = '';

        if ($respectLanguageVariants) {
            $languageVariantsControllerCondition = ' or `controller` = :languageVariantController';
            $document = $this->documentResolver->getDocument();
            if ($document && $document->getProperty(Routing::NAVIGATION_ROOT_PROPERTY)) {
                $parentPath = $document->getProperty(Routing::NAVIGATION_ROOT_PROPERTY)->getRealFullPath();
            }
        }

        /** @var array $documentIds */
        return Db::get()->fetchCol(
            "
            select d.id
            from
                (select * from documents where `path` like :parentPath and `published` = 1) as d join
                (select * from documents_page where `controller` = :assetController or `controller` = :objectController {$languageVariantsControllerCondition}) as p on d.id = p.id
        ",
            [
                'parentPath' => "{$parentPath}/%",
                'assetController' => ControllerReference::DATA_POOL_ASSETS_LIST,
                'objectController' => ControllerReference::DATA_POOL_DATA_OBJECTS_LIST,
                'languageVariantController' => ControllerReference::LANGUAGE_VARIANT_DATA_POOL,
            ]
        );
    }

    /**
     * Get all dataPoolConfigs by documents from given or current site which dataPool controller reference
     *
     * @return DataPoolConfigInterface[]
     *
     * @throws \Exception
     */
    public function getDataPoolConfigsFromSite(Site $site = null, bool $respectLanguageVariants = false)
    {
        $site = empty($site) ? Site::getCurrentSite() : $site;
        if (!isset($this->siteDataPoolConfigs[$site->getId()])) {

            /** @var DataPoolConfigInterface[] $dataPoolConfigs */
            $dataPoolConfigs = [];

            foreach ($this->getDataPoolDocumentsFromSite($site, $respectLanguageVariants) as $document) {

                /** @var DataPoolConfigInterface|null $dataPoolConfig */
                $dataPoolConfig = $this->getDataPoolConfigForDocument($document);
                if ($dataPoolConfig instanceof AssetConfig || ($dataPoolConfig instanceof DataObjectConfig && $dataPoolConfig->getDataObjectClass())) {
                    $dataPoolConfigs[] = $dataPoolConfig;
                }
            }

            $this->siteDataPoolConfigs[$site->getId()] = $dataPoolConfigs;
        }

        return $this->siteDataPoolConfigs[$site->getId()];
    }

    /**
     * @param $id
     * @param DataPoolConfigInterface|null $dataPoolConfig
     *
     * @return ElementInterface
     */
    public function getElementById($id, ?DataPoolConfigInterface $dataPoolConfig = null)
    {
        $dataPoolConfig = $dataPoolConfig ?: $this->getCurrentDataPoolConfig();

        return Service::getElementById($dataPoolConfig->getElementType(), $id);
    }

    /**
     * @param int[] $ids
     *
     * @return ElementInterface[]
     */
    public function getElementsByIds($ids = [])
    {
        /** @var ElementInterface[] $elements */
        $elements = [];

        if (!empty($ids)) {
            foreach ($ids as $id) {
                if ($element = $this->getElementById($id)) {
                    $elements[] = $element;
                }
            }
        }

        return $elements;
    }
}
