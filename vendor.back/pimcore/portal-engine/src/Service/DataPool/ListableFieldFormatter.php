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

use Pimcore\Localization\LocaleServiceInterface;

class ListableFieldFormatter
{
    protected $localeService;

    public function __construct(LocaleServiceInterface $localeService)
    {
        $this->localeService = $localeService;
    }

    public function format($listViewAttribute, string $type): ?string
    {
        if ($type === 'localized') {
            $listViewAttribute = $listViewAttribute[$this->localeService->getLocale()] ?? null;
        }

        //display array values as string
        if (is_array($listViewAttribute) && !empty($listViewAttribute)) {
            if (is_array(reset($listViewAttribute)) && array_key_exists('name', reset($listViewAttribute))) {
                $listViewAttribute = implode(', ', array_map(function ($listViewAttributeEntry) {
                    return $listViewAttributeEntry['name'];
                }, $listViewAttribute));
            } elseif (!is_array(reset($listViewAttribute))) {
                $listViewAttribute = implode(', ', $listViewAttribute);
            }
        }

        if ($type === 'date') {
            $listViewAttribute = date('Y-m-d H:i:s', strtotime($listViewAttribute));
        }

        return is_array($listViewAttribute)
            ? null //not found for foreign asset metadata
            : (string)$listViewAttribute;
    }
}
