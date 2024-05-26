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

use Carbon\Carbon;
use Pimcore\Localization\IntlFormatter;

/**
 * Class DateTimeAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter
 */
class DateTimeAdapter extends DateAdapter implements FieldDefinitionAdapterInterface
{
    /**
     * @param string $filterLabel
     * @param string $filterDataOptionValue
     *
     * @return string
     */
    public function formatFilterDataOptionLabel(string $filterLabel, string $filterDataOptionValue): string
    {
        return $this->intlFormatter->formatDateTime(
            Carbon::createFromTimestamp(substr($filterDataOptionValue, 0, -3)),
            IntlFormatter::DATETIME_SHORT
        );
    }
}
