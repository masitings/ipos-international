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
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Document\LanguageVariantService;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Cache\Runtime;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Element\ElementInterface;

class ElementDataPoolConfigResolver
{
    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * @var LanguageVariantService
     */
    protected $languageVariantService;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @param PortalConfigService $portalConfigService
     * @param LanguageVariantService $languageVariantService
     * @param DataPoolConfigService $dataPoolConfigService
     * @param PermissionService $permissionService
     * @param SecurityService $securityService
     */
    public function __construct(
        PortalConfigService $portalConfigService,
        LanguageVariantService $languageVariantService,
        DataPoolConfigService $dataPoolConfigService,
        PermissionService $permissionService,
        SecurityService $securityService
    ) {
        $this->portalConfigService = $portalConfigService;
        $this->languageVariantService = $languageVariantService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->permissionService = $permissionService;
        $this->securityService = $securityService;
    }

    /**
     * @param ElementInterface $element
     *
     * @return DataPoolConfigInterface|null
     */
    public function getDataPoolConfigForElement(ElementInterface $element)
    {
        // do not link to other data pools in guest share mode
        if ($this->securityService->getPortalUser()->isPortalShareUser()) {
            return null;
        }

        $cacheKey = $this->getDataPoolConfigCacheKeyForElement($element);

        if ($cacheKey && Runtime::isRegistered($cacheKey)) {
            return Runtime::get($cacheKey);
        }

        $config = null;
        $id = null;

        $ids = [];
        if ($element instanceof AbstractObject) {
            $ids = $this->getMatchingDataPoolConfigDocumentIds(
                ControllerReference::DATA_POOL_DATA_OBJECTS_LIST,
                'join (select * from documents_editables where `name` = :elementName and `data` = :classId) as e on p.id = e.documentId',
                [
                    'elementName' => \Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\DataPool\DataObjectConfig::DATA_OBJECT_CLASS,
                    'classId' => $element->getClass()->getId()
                ]
            );
        } elseif ($element instanceof Asset) {
            $ids = $this->getMatchingDataPoolConfigDocumentIds(ControllerReference::DATA_POOL_ASSETS_LIST);
        }

        $config = null;
        foreach ($ids as $id) {
            $document = Page::getById($id);
            if ($document) {
                $document = $this->languageVariantService->getLanguageVariant($document);
            }
            if ($config = $this->dataPoolConfigService->getDataPoolConfigForDocument($document)) {
                if ($this->isViewAllowed($config, $element)) {
                    break;
                } else {
                    $config = null;
                }
            }
        }

        if ($config && $cacheKey) {
            Runtime::set($cacheKey, $config);
        }

        return $config;
    }

    protected function isViewAllowed(DataPoolConfigInterface $config, ElementInterface $element)
    {
        return $this->permissionService->isPermissionAllowed(
            Permission::VIEW,
            $this->securityService->getPortalUser(),
            $config->getId(),
            $element->getRealFullPath()
        );
    }

    /**
     * @param string $controllerReference
     * @param string $additionalQuery
     * @param array $additionalParams
     *
     * @return []
     *
     * @throws \Exception
     */
    protected function getMatchingDataPoolConfigDocumentIds(string $controllerReference, string $additionalQuery = '', array $additionalParams = [])
    {
        $cacheKey = 'portal-engine-matching-data-pool-config-ids_'. md5(json_encode([$controllerReference, $additionalQuery, $additionalParams]));

        if (Runtime::isRegistered($cacheKey)) {
            return Runtime::load($cacheKey);
        }

        $result = Db::get()->fetchCol("
            select d.id
            from
                (select * from documents where `path` like :parentPath) as d join
                (select * from documents_page where `controller` = :controller) as p on d.id = p.id
                {$additionalQuery}
        ", array_merge([
            'parentPath' => "{$this->portalConfigService->getCurrentPortalConfig()->getDocument()->getRealFullPath()}/%",
            'controller' => $controllerReference
        ], $additionalParams));

        Runtime::save($result, $cacheKey);

        return $result;
    }

    /**
     * @param ElementInterface $element
     *
     * @return string|null
     */
    protected function getDataPoolConfigCacheKeyForElement(ElementInterface $element)
    {
        $prefix = "data_pool_c_{$element->getType()}_{$element->getId()}";

        if ($element instanceof AbstractObject || $element instanceof Asset) {
            return preg_replace('/[^a-zA-Z0-9]/', '_', $prefix);
        }

        return null;
    }
}
