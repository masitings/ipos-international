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

use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\CountryNameService;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\AbstractObject;

/**
 * Class CountryAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\FieldDefinitionAdapter
 */
class CountryAdapter extends DefaultAdapter
{
    /** @var CountryNameService */
    protected $countryNameService;

    /**
     * CountryAdapter constructor.
     *
     * @param CountryNameService $countryNameService
     */
    public function __construct(CountryNameService $countryNameService)
    {
        $this->countryNameService = $countryNameService;
    }

    /**
     *
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

        return [
            'label' => $this->countryNameService->getCountryName($data),
            'value' => $data,
        ];
    }
}
