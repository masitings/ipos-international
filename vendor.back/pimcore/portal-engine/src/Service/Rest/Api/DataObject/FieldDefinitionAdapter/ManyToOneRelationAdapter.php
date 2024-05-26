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

use Pimcore\Bundle\PortalEngineBundle\Service\Element\NameExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element\ElementInterface;

class ManyToOneRelationAdapter extends DefaultAdapter
{
    protected $nameExtractorService;
    protected $urlExtractorService;

    public function __construct(
        NameExtractorService $nameExtractorService,
        UrlExtractorService $urlExtractorService
    ) {
        $this->nameExtractorService = $nameExtractorService;
        $this->urlExtractorService = $urlExtractorService;
    }

    /**
     *
     * @param AbstractObject $object
     * @param ElementInterface $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = [])
    {
        $dataArray = parent::getDataForDetail($object, $data, $params);

        if (empty($data)) {
            return null;
        }

        $dataArray['name'] = $this->nameExtractorService->extractName($data);

        if ($url = $this->urlExtractorService->extractUrl($data)) {
            $dataArray['url'] = $url;
        }

        return $dataArray;
    }
}
