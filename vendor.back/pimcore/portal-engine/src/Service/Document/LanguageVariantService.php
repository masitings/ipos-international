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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Document;

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\ControllerReference;
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\LanguageVariant;
use Pimcore\Bundle\PortalEngineBundle\Enum\Routing;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Cache;
use Pimcore\Db;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Editable\Relation;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Site;

class LanguageVariantService
{
    const CACHE_TAG = 'portal-engine_language_variant';

    /**
     * @var DocumentResolver
     */
    protected $documentResolver;

    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * @var array|null
     */
    private $allLanguageVariantIds = null;

    /**
     * LanguageVariantService constructor
     *
     * @param DocumentResolver $documentResolver
     * @param PortalConfigService $portalConfigService
     */
    public function __construct(DocumentResolver $documentResolver, PortalConfigService $portalConfigService)
    {
        $this->documentResolver = $documentResolver;
        $this->portalConfigService = $portalConfigService;
    }

    public function getReferencedDocument(Page $document): ?Document
    {
        if (!$this->isLanguageVariantDocument($document)) {
            return null;
        }

        $editable = $document->getEditable(LanguageVariant::REFERENCED_DOCUMENT);
        $document = $editable instanceof Relation ? $editable->getElement() : null;

        return $document instanceof Document ? $document : null;
    }

    public function isLanguageVariantDocument(Document $document): bool
    {
        return $document instanceof Page
               && $document->getController() === ControllerReference::LANGUAGE_VARIANT_DATA_POOL;
    }

    public function getLanguageVariant(Page $document): ?Document
    {
        $navigationRoot = $this->getNavigationRoot();
        if (strpos($document->getRealFullPath(), $navigationRoot->getRealFullPath()) === 0) {
            return $document;
        }

        $ids = $this->getAllLanguageVariantIds();
        $languageVariantId = $ids[$document->getId()] ?? null;
        if (!empty($languageVariantId)) {
            return Document::getById($languageVariantId);
        }

        return null;
    }

    public function clearCache()
    {
        Cache::clearTag(self::CACHE_TAG);
    }

    public function getPortalLanguageVariants(): array
    {
        $portalConfig = $this->portalConfigService->getCurrentPortalConfig();
        if (!$portalConfig->getEnableLanguageRedirect()) {
            return [];
        }

        $cacheKey = 'portal-engine-portal-language-variants-' . $portalConfig->getPortalId() . '-' . (Site::isSiteRequest() ? 'site' : 'nosite');
        if (!$result = Cache::load($cacheKey)) {
            $result = [];
            $portalDocument = $portalConfig->getDocument();
            foreach ($portalDocument->getChildren() as $child) {
                $result[] = [
                    'language' => $child->getProperty('language'),
                    'path' => $child->getFullPath(),
                    'realFullPath' => $child->getRealFullPath(),
                    'navigation_name' => (string)$child->getProperty('navigation_name') ?: $child->getKey(),
                    'key' => $child->getKey()
                ];
            }

            Cache::save($result, $cacheKey, [self::CACHE_TAG]);
        }

        return $result;
    }

    public function getPreferredLanguageChoices(): array
    {
        $result = [];
        $variants = $this->getPortalLanguageVariants();
        foreach ($variants as $variant) {
            $result[$variant['language']] = $variant['navigation_name'];
        }

        return sizeof($result) ? array_merge(['-' => null], array_flip($result)) : [];
    }

    public function getPortalLanguageVariantsMapping(): array
    {
        $result = [];
        foreach ($this->getPortalLanguageVariants() as $row) {
            $result[$row['language']] = $result[$row['language']] ?? $row['path'];
        }

        return $result;
    }

    public function getCurrentLanguageSelectionDocumentPath(): string
    {
        $navigationRoot = $this->getNavigationRoot();

        return $navigationRoot->getRealFullPath();
    }

    protected function getAllLanguageVariantIds()
    {
        if (empty($this->allLanguageVariantIds)) {
            $navigationRoot = $this->getNavigationRoot();

            $cacheKey = 'portal-engine_language-variant-ids_' . $navigationRoot->getId();

            if (!$result = Cache::load($cacheKey)) {
                $ids = Db::get()->fetchCol('
                select d.id
                from
                    (select * from documents where published=1 and  `path` like :parentPath) as d join
                    (select * from documents_page where `controller` = :controller) as p on d.id = p.id
            ', array_merge([
                    'parentPath' => "{$navigationRoot->getRealFullPath()}/%",
                    'controller' => ControllerReference::LANGUAGE_VARIANT_DATA_POOL
                ]));

                $result = [];
                foreach ($ids as $id) {
                    $document = Page::getById($id);
                    if ($document && $this->getReferencedDocument($document)) {
                        $result[$this->getReferencedDocument($document)->getId()] = $document->getId();
                    }
                }

                Cache::save($result, $cacheKey, [self::CACHE_TAG]);
            }

            $this->allLanguageVariantIds = $result;
        }

        return $this->allLanguageVariantIds;
    }

    protected function getNavigationRoot(): ?Document
    {
        $navigationRoot = $this->documentResolver->getDocument();

        return $navigationRoot->getProperty(Routing::NAVIGATION_ROOT_PROPERTY) ?? $navigationRoot;
    }
}
