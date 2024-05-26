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

namespace Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig;

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\ControllerReference;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\PortalConfig;
use Pimcore\Db\Connection;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Site;
use Symfony\Component\HttpFoundation\RequestStack;

class PortalConfigService
{
    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var DocumentResolver
     */
    protected $documentResolver;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var PortalConfig
     */
    protected $currentPortalConfig;

    /**
     * PortalConfigService constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db, DocumentResolver $documentResolver, RequestStack $requestStack)
    {
        $this->db = $db;
        $this->documentResolver = $documentResolver;
        $this->requestStack = $requestStack;
    }

    public function isPortalEngineSite(): bool
    {
        if (Site::isSiteRequest()) {
            $site = Site::getCurrentSite();

            return $this->isPortalEnginePortal($site->getRootDocument());
        }

        return false;
    }

    public function getPortalConfigById(int $id): ?PortalConfig
    {
        foreach ($this->getAllPortalConfigs() as $portalConfig) {
            if ($portalConfig->getPortalId() === $id) {
                return $portalConfig;
            }
        }

        return null;
    }

    public function getCurrentPortalConfig(): ?PortalConfig
    {
        $document = $this->documentResolver->getDocument();

        if (empty($document)) {
            return null;
        }

        if (empty($this->currentPortalConfig)) {
            foreach ($this->getAllPortalRoots() as $id => $path) {
                if (strpos($document->getRealFullPath(), $path) === 0) {
                    $this->currentPortalConfig = $this->createPortalConfigByDocument(Page::getById($id));
                    break;
                }
            }

            $this->currentPortalConfig = $this->currentPortalConfig ?? new PortalConfig(new Page());
        }

        return $this->currentPortalConfig;
    }

    /**
     * @return PortalConfig[]
     */
    public function getAllPortalConfigs(): array
    {
        $configs = [];

        foreach ($this->getAllPortalRoots() as $id => $fullPath) {
            $configs[] = $this->createPortalConfigByDocument(Page::getById($id));
        }

        return $configs;
    }

    public function getAllPortalRoots(): array
    {
        $sql = <<<SQL
    select documents.id, concat(documents.path, documents.`key`) as fullPath
    from documents,
         documents_page,
         sites
    where sites.rootId = documents_page.id
      and documents.id = documents_page.id
      and documents_page.controller = ?
SQL;

        $result = [];
        foreach ($this->db->fetchAllAssociative($sql, [ControllerReference::PORTAL_PAGE]) as $row) {
            $result[$row['id']] = $row['fullPath'];
        }

        return $result;
    }

    public function getPortalName(): string
    {
        $portalConfig = $this->getCurrentPortalConfig();

        return $portalConfig ? $portalConfig->getPortalName() : '';
    }

    public function isPortalEnginePortal(Page $document)
    {
        return $document->getController() === ControllerReference::PORTAL_PAGE;
    }

    protected function createPortalConfigByDocument(Page $document): PortalConfig
    {
        return new PortalConfig($document);
    }
}
