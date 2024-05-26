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

use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataObject\VersionPreviewValue;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\ThumbnailService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\ElementDataPoolConfigResolver;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\AbstractObject;

class HotspotImageAdapter extends DefaultAdapter
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

        $data = parent::getDataForDetail($object, $data, $params);

        if ($data) {
            $image = Image::getById($data['id']);
            $data = array_merge($data, ImageAdapter::extractNecessaryData(
                $this->dataPoolConfigService,
                $this->elementDataPoolConfigResolver,
                $this->permissionService,
                $this->securityService,
                $this->urlExtractorService,
                $this->thumbnailService,
                $object,
                $image
            ));
        }

        return $data;
    }

    public function getDataForVersionPreview(AbstractObject $object, $data, array $params = [])
    {
        if (!$data || !$data->getImage() instanceof Image) {
            return new VersionPreviewValue($this->fieldDefinition->getName(), $this->fieldDefinition->getTitle(), null);
        }

        return new VersionPreviewValue($this->fieldDefinition->getName(), $this->fieldDefinition->getTitle(), '<img src="' . $data->getThumbnail(['width' => 100, 'height' => 100, 'aspectratio' => true]) . '"/>');
    }
}
