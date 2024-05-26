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
use Pimcore\Bundle\PortalEngineBundle\Enum\DataPool\FilterSort;
use Pimcore\Bundle\PortalEngineBundle\Enum\ElasticSearchFields;
use Pimcore\Localization\IntlFormatter;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class DateAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter
 */
class DateAdapter extends DefaultAdapter implements FieldDefinitionAdapterInterface
{
    /** @var IntlFormatter */
    protected $intlFormatter;

    /**
     * @param IntlFormatter $intlFormatter
     * @required
     */
    public function setIntlFormatter(IntlFormatter $intlFormatter): void
    {
        $this->intlFormatter = $intlFormatter;
    }

    /**
     * @return array
     */
    public function getESMapping()
    {
        return [
            $this->fieldDefinition->getName(),
            [
                'type' => ElasticSearchFields::TYPE_DATE,
            ]
        ];
    }

    /**
     * @param Concrete $object
     *
     * @return string|array
     */
    protected function doGetIndexDataValue($object)
    {
        /** @var $value */
        $value = null;
        /** @var Carbon $carbonDate */
        $carbonDate = $this->doGetRawIndexDataValue($object);

        if ($carbonDate instanceof Carbon) {
            $value = $carbonDate->format(\DateTime::ISO8601);
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return '';
    }

    /**
     * @return bool
     */
    public function isFilterable()
    {
        return true;
    }

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
            IntlFormatter::DATE_SHORT
        );
    }

    /**
     * @return string
     */
    public function getFilterDataOptionSort(): string
    {
        return FilterSort::SORT_BY_VALUE;
    }
}
