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

namespace Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormat;

use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Field\ExportableField;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DownloadFormat\Traits\ExportServiceResolver;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadTmpStoreService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\Export\ExportService;

class CsvDownloadFormat implements DownloadFormatInterface
{
    use ExportServiceResolver;

    const CSV_EXPORT_BASE_FOLDER = PIMCORE_SYSTEM_TEMP_DIRECTORY . '/portal-engine/csv-tmp';

    /**
     * @var DownloadTmpStoreService
     */
    protected $tmpStoreService;

    /**
     * @param ExportService $exportService
     */
    public function __construct(DownloadTmpStoreService $tmpStoreService)
    {
        $this->tmpStoreService = $tmpStoreService;
    }

    /**
     * @inheritDoc
     */
    public function supports(DataPoolConfigInterface $dataPoolConfig): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getDisplayName(): string
    {
        return 'Portal Engine CSV';
    }

    /**
     * @inheritDoc
     */
    public function add(string $downloadUniqueId, $element)
    {
        $exportableFields = $this->getExportService()->getExportableFields($element);

        $existingData = $this->tmpStoreService->getTmpStoreData($downloadUniqueId);
        if (empty($existingData)) {
            $labels = $this->getLabels($exportableFields);
            $this->tmpStoreService->appendToTmpStoreData($downloadUniqueId, $labels);
        }

        $exportData = $this->getExportData($exportableFields);
        $this->tmpStoreService->appendToTmpStoreData($downloadUniqueId, $exportData);
    }

    /**
     * @inheritDoc
     */
    public function bundle(string $downloadUniqueId): string
    {
        if (!file_exists(self::CSV_EXPORT_BASE_FOLDER)) {
            mkdir(self::CSV_EXPORT_BASE_FOLDER, 0777, true);
        }
        $data = $this->tmpStoreService->getTmpStoreData($downloadUniqueId);

        $exportFileName = self::CSV_EXPORT_BASE_FOLDER . '/' . $downloadUniqueId . '.csv';
        $fp = fopen($exportFileName, 'a');

        foreach ($data as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);

        $this->tmpStoreService->clearTmpSToreData($downloadUniqueId);

        return $exportFileName;
    }

    /**
     * @inheritDoc
     */
    public function getDownloadFilename(string $downloadUniqueId): string
    {
        return 'structured-data.csv';
    }

    /**
     * @param ExportableField[] $exportableFields
     *
     * @return array
     */
    protected function getLabels(array $exportableFields): array
    {
        $labels = [];
        foreach ($exportableFields as $exportableField) {
            if ($exportableField->isLocalized()) {
                foreach ($exportableField->getData() as $language => $data) {
                    $labels[] = $exportableField->getTitle() . ' (' . $language . ')';
                }
            } else {
                $labels[] = $exportableField->getTitle();
            }
        }

        return $labels;
    }

    /**
     * @param ExportableField[] $exportableFields
     *
     * @return array
     */
    protected function getExportData(array $exportableFields): array
    {
        $exportData = [];
        foreach ($exportableFields as $exportableField) {
            if ($exportableField->isLocalized()) {
                foreach ($exportableField->getData() as $language => $data) {
                    $exportData[] = $exportableField->getFieldDefinitionAdapter()->exportDataToString($data);
                }
            } else {
                $exportData[] = $exportableField->getFieldDefinitionAdapter()->exportDataToString(
                    $exportableField->getData()
                );
            }
        }

        return $exportData;
    }
}
