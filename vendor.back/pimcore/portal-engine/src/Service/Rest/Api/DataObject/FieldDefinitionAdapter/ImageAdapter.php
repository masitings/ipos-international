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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\FieldDefinitionAdapter;

use Pimcore\Bundle\PortalEngineBundle\Enum\ImageThumbnails;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataObject\VersionPreviewValue;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\ThumbnailService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\ElementDataPoolConfigResolver;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\AbstractObject;

class ImageAdapter extends DefaultAdapter
{
    protected $thumbnailService;
    protected $dataPoolConfigService;
    protected $elementDataPoolConfigResolver;
    protected $urlExtractorService;
    protected $permissionService;
    protected $securityService;

    public function __construct(
        ThumbnailService $thumbnailService,
        DataPoolConfigService $dataPoolConfigService,
        ElementDataPoolConfigResolver $elementDataPoolConfigResolver,
        UrlExtractorService $urlExtractorService,
        PermissionService $permissionService,
        SecurityService $securityService
    ) {
        $this->thumbnailService = $thumbnailService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->elementDataPoolConfigResolver = $elementDataPoolConfigResolver;
        $this->urlExtractorService = $urlExtractorService;
        $this->permissionService = $permissionService;
        $this->securityService = $securityService;
    }

    /**
     * @param DataPoolConfigService $dataPoolConfigService
     * @param UrlExtractorService $urlExtractorService
     * @param Asset $asset
     *
     * @return array
     */
    public static function extractNecessaryData(
        DataPoolConfigService $dataPoolConfigService,
        ElementDataPoolConfigResolver $elementDataPoolConfigResolver,
        PermissionService $permissionService,
        SecurityService $securityService,
        UrlExtractorService $urlExtractorService,
        ThumbnailService $thumbnailService,
        AbstractObject $object,
        ?Asset $asset
    ) {
        if (!$asset) {
            return [];
        }

        $config = $dataPoolConfigService->getCurrentDataPoolConfig() ?: $elementDataPoolConfigResolver->getDataPoolConfigForElement($asset);

        $result = [
            'id' => $asset->getId(),
            'thumbnail' => $thumbnailService->getThumbnailPath($asset, ImageThumbnails::DETAIL_PAGE),
            'filename' => $asset->getFilename(),
            'path' => $asset->getPath()
        ];

        if ($url = $urlExtractorService->extractUrl($asset)) {
            $result['url'] = $url;
        }

        if ($permissionService->isPermissionAllowed(Permission::DOWNLOAD, $securityService->getPortalUser(), $config->getId(), $object->getRealFullPath())) {
            $result['downloadId'] = $object->getId();
            $result['dataPoolId'] = $config ? $config->getId() : null;
        }

        return $result;
    }

    /**
     * @param AbstractObject $object
     * @param Image $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = [])
    {
        if (empty($data)) {
            return null;
        }

        return self::extractNecessaryData(
            $this->dataPoolConfigService,
            $this->elementDataPoolConfigResolver,
            $this->permissionService,
            $this->securityService,
            $this->urlExtractorService,
            $this->thumbnailService,
            $object,
            $data
        );
    }

    /**
     * @param AbstractObject $object
     * @param Image $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForVersionPreview(AbstractObject $object, $data, array $params = [])
    {
        if (!$data) {
            return new VersionPreviewValue($this->fieldDefinition->getName(), $this->fieldDefinition->getTitle(), null);
        }

        return new VersionPreviewValue($this->fieldDefinition->getName(), $this->fieldDefinition->getTitle(), '<img src="' . $data->getThumbnail(['width' => 100, 'height' => 100, 'aspectratio' => true]) . '"/>');
    }
}
