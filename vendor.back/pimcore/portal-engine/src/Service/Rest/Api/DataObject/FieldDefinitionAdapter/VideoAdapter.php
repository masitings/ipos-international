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

use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\ElementDataPoolConfigResolver;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Data\Video;

class VideoAdapter extends DefaultAdapter
{
    protected $dataPoolConfigService;
    protected $elementDataPoolConfigResolver;
    protected $urlExtractorService;
    protected $permissionService;
    protected $securityService;

    public function __construct(
        DataPoolConfigService $dataPoolConfigService,
        ElementDataPoolConfigResolver $elementDataPoolConfigResolver,
        UrlExtractorService $urlExtractorService,
        PermissionService $permissionService,
        SecurityService $securityService
    ) {
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->elementDataPoolConfigResolver = $elementDataPoolConfigResolver;
        $this->urlExtractorService = $urlExtractorService;
        $this->permissionService = $permissionService;
        $this->securityService = $securityService;
    }

    /**
     * @param AbstractObject $object
     * @param Video $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = [])
    {
        if (empty($data)) {
            return null;
        }

        $result = parent::getDataForDetail($object, $data, $params);

        if ($data->getData() instanceof Asset) {
            $asset = $data->getData();
            $config = $this->dataPoolConfigService->getCurrentDataPoolConfig() ?: $this->elementDataPoolConfigResolver->getDataPoolConfigForElement($asset);

            $result['id'] = $asset->getId();

            if ($url = $this->urlExtractorService->extractUrl($asset)) {
                $result['url'] = $url;
            }

            if ($this->permissionService->isPermissionAllowed(Permission::DOWNLOAD, $this->securityService->getPortalUser(), $config->getId(), $asset->getRealFullPath())) {
                $result['downloadId'] = $object->getId();
                $result['dataPoolId'] = $config ? $config->getId() : null;
            }

            $result['filename'] = $asset->getFilename();
            $result['path'] = $asset->getPath();
        }

        return $result;
    }
}
