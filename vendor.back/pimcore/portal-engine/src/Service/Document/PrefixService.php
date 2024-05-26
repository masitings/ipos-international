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

use Pimcore\Bundle\PortalEngineBundle\Enum\Routing;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Model\Document;
use Symfony\Component\Routing\RouterInterface;

class PrefixService
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var DocumentResolver
     */
    protected $documentResolver;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * PrefixService constructor.
     *
     * @param RouterInterface $router
     * @param DocumentResolver $documentResolver
     * @param DataPoolConfigService $dataPoolConfigService
     */
    public function __construct(
        RouterInterface $router,
        DocumentResolver $documentResolver,
        DataPoolConfigService $dataPoolConfigService
    ) {
        $this->router = $router;
        $this->documentResolver = $documentResolver;
        $this->dataPoolConfigService = $dataPoolConfigService;
    }

    public function setupRoutingPrefix(bool $force = false)
    {
        if ($force || !$this->router->getContext()->hasParameter(Routing::PREFIX_PARAM)) {
            $this->router->getContext()->setParameter(Routing::PREFIX_PARAM, $this->getRoutingPrefix());
        }
    }

    public function getLocalizedVersionByRelativePath(Document $document): Document
    {
        $navigationRoot = $this->documentResolver->getDocument()->getProperty(Routing::NAVIGATION_ROOT_PROPERTY);
        $realFullPath = $document->getRealFullPath();
        if ($navigationRoot instanceof Document && strpos($realFullPath, $navigationRoot->getRealFullPath()) !== 0) {
            $includeNavigationRoot = $document->getProperty(Routing::NAVIGATION_ROOT_PROPERTY);
            $relativeDocumentPath = substr($realFullPath, strlen($includeNavigationRoot->getRealFullPath()));
            $localizedFullPath = $navigationRoot->getRealFullPath() . $relativeDocumentPath;
            $localizedDocument = $document::getByPath($localizedFullPath);

            return $localizedDocument ?? $document;
        }

        return $document;
    }

    /**
     * @return string|null
     */
    protected function getRoutingPrefix(): string
    {
        if ($dataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig()) {
            $document = $dataPoolConfig->getLanguageVariantOrDocument();
        } else {
            $document = $this->documentResolver->getDocument();
        }

        $navigationRoot = $document ? $document->getProperty(Routing::NAVIGATION_ROOT_PROPERTY) : null;

        if (empty($navigationRoot)) {
            return '_';
        }

        $prefix = ltrim($navigationRoot, '/') . '/';

        return $prefix !== '/' ? $prefix : '_';
    }
}
