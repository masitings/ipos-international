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

use Pimcore\Tool;

/**
 * Class LanguageNameService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\DataObject
 */
class LanguageNameService
{
    /**
     * @param string $code
     *
     * @return string
     */
    public function getLanguageName($code)
    {
        /** @var string $translation */
        $translation = '';

        if (!empty($code)) {
            /** @var array $locales */
            $locales = Tool::getSupportedLocales();

            if (array_key_exists($code, $locales)) {
                $translation = $locales[$code];
            }
        }

        return $translation;
    }
}
