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

use Carbon\Carbon;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataObject\VersionPreviewValue;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Localization\IntlFormatter;
use Pimcore\Model\DataObject\AbstractObject;

class DateAdapter extends DefaultAdapter
{
    protected $formatter;
    protected $dataPoolConfigService;

    public function __construct(IntlFormatter $intlFormatter, DataPoolConfigService $dataPoolConfigService)
    {
        $this->formatter = $intlFormatter;
        $this->dataPoolConfigService = $dataPoolConfigService;
    }

    /**
     * @param AbstractObject $object
     * @param Carbon $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = [])
    {
        if (empty($data)) {
            return null;
        }

        return $this->formatter->formatDateTime($data, $this->getFormat());
    }

    /**
     * @param AbstractObject $object
     * @param Carbon $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForVersionPreview(AbstractObject $object, $data, array $params = [])
    {
        $data = $this->getDataForDetail($object, $data, $params);

        return new VersionPreviewValue($this->fieldDefinition->getName(), $this->fieldDefinition->getTitle(), $data);
    }

    /**
     * @return string
     */
    protected function getFormat()
    {
        return IntlFormatter::DATE_SHORT;
    }
}
