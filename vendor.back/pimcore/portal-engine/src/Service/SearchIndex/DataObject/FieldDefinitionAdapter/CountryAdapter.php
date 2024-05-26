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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter;

use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\CountryNameService;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class CountryAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter
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
     * @param Concrete $object
     *
     * @return string|array
     */
    protected function doGetIndexDataValue($object)
    {
        return $this->countryNameService->getCountryName($this->doGetRawIndexDataValue($object));
    }
}
