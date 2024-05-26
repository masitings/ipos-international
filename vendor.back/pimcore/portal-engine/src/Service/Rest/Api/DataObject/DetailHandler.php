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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\ThumbnailService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\ClassificationStoreLayoutService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\CustomLayoutService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\FieldcollectionLayoutService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\ObjectbrickLayoutService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\NameExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataPool\AbstractDetailHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\VersionHandlerTrait;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\SearchService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Localization\IntlFormatter;
use Pimcore\Localization\LocaleService;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Version;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DetailHandler extends AbstractDetailHandler
{
    use VersionHandlerTrait;

    protected $thumbnailService;
    protected $customLayoutService;
    protected $fieldcollectionLayoutService;
    protected $objectbrickLayoutService;
    protected $classificationStoreLayoutService;
    protected $nameExtractorService;
    protected $urlExtractorService;
    protected $formatter;
    protected $localeService;
    protected $permissionService;
    protected $searchService;
    protected $securityService;
    protected $eventDispatcher;

    public function __construct(
        ThumbnailService $thumbnailService,
        NameExtractorService $nameExtractorService,
        UrlExtractorService $urlExtractorService,
        CustomLayoutService $customLayoutService,
        FieldcollectionLayoutService $fieldcollectionLayoutService,
        ObjectbrickLayoutService $objectbrickLayoutService,
        ClassificationStoreLayoutService $classificationStoreLayoutService,
        IntlFormatter $formatter,
        LocaleService $localeService,
        SearchService $searchService,
        PermissionService $permissionService,
        SecurityService $securityService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->thumbnailService = $thumbnailService;
        $this->customLayoutService = $customLayoutService;
        $this->fieldcollectionLayoutService = $fieldcollectionLayoutService;
        $this->objectbrickLayoutService = $objectbrickLayoutService;
        $this->classificationStoreLayoutService = $classificationStoreLayoutService;
        $this->nameExtractorService = $nameExtractorService;
        $this->urlExtractorService = $urlExtractorService;
        $this->formatter = $formatter;
        $this->localeService = $localeService;
        $this->searchService = $searchService;
        $this->permissionService = $permissionService;
        $this->securityService = $securityService;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param AbstractObject $dataObject
     *
     * @return array
     */
    public function getData(AbstractObject $dataObject, DataObjectConfig $config): array
    {
        $customLayout = $this->customLayoutService->getCustomLayout($config->getCustomLayoutId());

        return [
            'id' => $dataObject->getId(),
            'breadcrumbs' => $this->urlExtractorService->extractBreadcrumbs($dataObject),
            'data' => $this->customLayoutService->getCustomLayoutData($dataObject, $customLayout),
            'layout' => $this->customLayoutService->getCustomLayoutLayoutDefinitions($customLayout, $config, $dataObject),
            'fieldcollectionLayouts' => $this->fieldcollectionLayoutService->getLayoutDefinitionsForClassDefinition($config, $dataObject->getClass(), $dataObject),
            'objectbrickLayouts' => $this->objectbrickLayoutService->getLayoutDefinitionsForClassDefinition($config, $dataObject->getClass(), $dataObject),
            'classificationStoreLayouts' => $this->classificationStoreLayoutService->getUsedKeyDefinitionsForClassDefinition($dataObject->getClass()),
            'permissions' => $this->permissionService->getPermissionsForUser($this->securityService->getPortalUser(), $config->getId(), $dataObject->getRealFullPath())
        ];
    }

    public function getVersionHistory(AbstractObject $dataObject, DataObjectConfig $config): array
    {
        return $this->extractVersionsData($dataObject, $this->formatter, $this->eventDispatcher);
    }

    /**
     * @param Version[] $versions
     * @param DataObjectConfig $config
     *
     * @return array
     */
    public function getVersionData(array $versions, DataObjectConfig $config): array
    {
        try {
            $customLayout = $this->customLayoutService->getCustomLayout($config->getCustomLayoutId());
            $dataObjects = $this->extractVersionElements($versions);

            return $this->customLayoutService->getCustomLayoutVersionData($dataObjects, $customLayout);
        } catch (\Exception $e) {
            return [];
        }
    }
}
