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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter;

use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\CountryNameService;

/**
 * Class CountryAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter
 */
class CountryAdapter extends SelectAdapter implements FieldDefinitionAdapterInterface
{
    /** @var CountryNameService */
    protected $countryNameService;

    /**
     * @param CountryNameService $countryNameService
     * @required
     */
    public function setCountryNameService(CountryNameService $countryNameService): void
    {
        $this->countryNameService = $countryNameService;
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function castMetaData($data)
    {
        return $this->countryNameService->getCountryName($data);
    }
}
