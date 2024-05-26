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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\Export;

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Data;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data\Input;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration\Dao;
use Pimcore\AssetMetadataClassDefinitionsBundle\Service;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ExportableField;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\SearchIndexFieldDefinitionService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter\DefaultAdapter;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\FieldDefinitionAdapter\FieldDefinitionAdapterInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Export\AbstractExportService;
use Pimcore\Model\Asset;

class ExportService extends AbstractExportService
{
    /**
     * @var SearchIndexFieldDefinitionService
     */
    protected $fieldDefinitionService;

    /**
     * @var DefaultAdapter
     */
    protected $defaultAdapter;

    /**
     * ExportService constructor.
     *
     * @param SearchIndexFieldDefinitionService $fieldDefinitionService
     * @param DefaultAdapter $defaultAdapter
     */
    public function __construct(SearchIndexFieldDefinitionService $fieldDefinitionService)
    {
        $this->fieldDefinitionService = $fieldDefinitionService;
        //  $this->defaultAdapter = $defaultAdapter;
    }

    /**
     * @param \Pimcore\Model\Element\ElementInterface $element
     *
     * @return ExportableField[]
     */
    public function getExportableFields($element): array
    {
        if (!$element instanceof Asset) {
            return [];
        }

        $exportableFields = [];

        $exportableFields[] = (new ExportableField())
                                ->setData($element->getId())
                                ->setTitle('Pimcore-ID')
                                ->setName('id')
                                ->setType('input')
                                ->setFieldDefinitionAdapter($this->getDefaultAdapter());

        $exportableFields[] = (new ExportableField())
                                ->setData($element->getFilename())
                                ->setTitle('Filename')
                                ->setName('filename')
                                ->setType('input')
                                ->setFieldDefinitionAdapter($this->getDefaultAdapter());

        $exportableFields[] = (new ExportableField())
                                ->setData($element->getType())
                                ->setTitle('Type')
                                ->setName('type')
                                ->setType('input')
                                ->setFieldDefinitionAdapter($this->getDefaultAdapter());

        $exportableFields[] = (new ExportableField())
                                ->setData($element->getMimetype())
                                ->setTitle('Mimetype')
                                ->setName('mimetype')
                                ->setType('input')
                                ->setFieldDefinitionAdapter($this->getDefaultAdapter());

        foreach (Dao::getList(true) as $configuration) {

            /** @var ExportableField[] $configurationExportableFields */
            $configurationExportableFields = [];
            /** @var Data[] $fieldDefinitions */
            $fieldDefinitions = [];
            /** @var Data[] $localizedFieldDefinitions */
            $localizedFieldDefinitions = [];

            Service::extractDataDefinitions($configuration->getLayoutDefinitions(), false, $fieldDefinitions, $localizedFieldDefinitions);

            foreach ($fieldDefinitions as $fieldDefinition) {

                /** @var FieldDefinitionAdapterInterface|null $fieldDefinitionAdapter */
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isExportable()) {
                    $configurationExportableFields[] = $fieldDefinitionAdapter
                        ->getDataForExport($element, $configuration);
                }
            }
            foreach ($localizedFieldDefinitions as $fieldDefinition) {

                /** @var FieldDefinitionAdapterInterface|null $fieldDefinitionAdapter */
                $fieldDefinitionAdapter = $this->fieldDefinitionService->getFieldDefinitionAdapter($fieldDefinition);
                if ($fieldDefinitionAdapter && $fieldDefinitionAdapter->isExportable()) {
                    $configurationExportableFields[] = $fieldDefinitionAdapter
                        ->getDataForExport($element, $configuration, true)
                        ->setLocalized(true);
                }
            }

            $exportableFields = array_merge($exportableFields, $configurationExportableFields);
        }

        return $exportableFields;
    }

    private function getDefaultAdapter(): FieldDefinitionAdapterInterface
    {
        return $this->fieldDefinitionService->getFieldDefinitionAdapter(new Input());
    }
}
