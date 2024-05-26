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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\Export;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ExportableField;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\SearchIndexFieldDefinitionService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataPool\FieldDefinitionAdapter\FieldDefinitionAdapterInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Export\AbstractExportService;
use Pimcore\Model\DataObject\ClassDefinition\Data\Input;
use Pimcore\Model\DataObject\Concrete;

class ExportService extends AbstractExportService
{
    /**
     * @var SearchIndexFieldDefinitionService
     */
    protected $fieldDefinitionService;

    /**
     * ExportService constructor.
     *
     * @param SearchIndexFieldDefinitionService $fieldDefinitionService
     */
    public function __construct(SearchIndexFieldDefinitionService $fieldDefinitionService)
    {
        $this->fieldDefinitionService = $fieldDefinitionService;
    }

    /**
     * @param \Pimcore\Model\Element\ElementInterface $element
     *
     * @return ExportableField[]
     */
    public function getExportableFields($element): array
    {
        if (!$element instanceof Concrete) {
            return [];
        }

        $exportableFields = [];

        $exportableFields[] = (new ExportableField())
                                ->setData($element->getId())
                                ->setTitle('Pimcore-ID')
                                ->setName('id')
                                ->setType('input')
                                ->setFieldDefinitionAdapter($this->getDefaultAdapter());

        foreach ($element->getClass()->getFieldDefinitions() as $fieldDefinition) {
            $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
            if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isExportable()) {
                foreach ($fieldDefinitionAdapter->getDataForExport($element) as $exportableField) {
                    $exportableFields[] = $exportableField;
                }
            }
        }

        return $exportableFields;
    }

    private function getDefaultAdapter(): FieldDefinitionAdapterInterface
    {
        return $this->fieldDefinitionService->getFieldDefinitionAdapter(new Input());
    }
}
