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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataObject;

use Pimcore\Localization\LocaleServiceInterface;

/**
 * Class CountryNameService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\DataObject
 */
class CountryNameService
{
    /**
     * @var LocaleServiceInterface
     */
    protected $localeService;

    /**
     * CountryNameService constructor.
     *
     * @param LocaleServiceInterface $localeService
     */
    public function __construct(LocaleServiceInterface $localeService)
    {
        $this->localeService = $localeService;
    }

    /**
     * @param string $code
     *
     * @return string
     */
    public function getCountryName($code)
    {
        /** @var string $translation */
        $translation = '';

        if (!empty($code)) {
            /** @var array $countries */
            $countries = $this->localeService->getDisplayRegions();

            if (array_key_exists($code, $countries)) {
                $translation = $countries[$code];
            }
        }

        return $translation;
    }
}
