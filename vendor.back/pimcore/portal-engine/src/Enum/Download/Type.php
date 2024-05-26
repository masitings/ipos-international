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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\Download;

use MyCLabs\Enum\Enum;

class Type extends Enum
{
    const STRUCTURED_DATA = 'structuredData';
    const IMAGE = 'image';
    const ASSET = 'asset';

    /**
     * @param string|null $type
     *
     * @return bool
     */
    public static function isAssetType(?string $type): bool
    {
        return in_array($type, self::getAssetTypes());
    }

    /**
     * @return string[]
     */
    public static function getAssetTypes(): array
    {
        return [
            self::ASSET,
            self::IMAGE
        ];
    }
}
