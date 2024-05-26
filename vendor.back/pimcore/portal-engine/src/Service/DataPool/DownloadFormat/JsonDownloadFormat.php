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

class JsonDownloadFormat implements DownloadFormatInterface
{
    use ExportServiceResolver;

    const JSON_EXPORT_BASE_FOLDER = PIMCORE_SYSTEM_TEMP_DIRECTORY . '/portal-engine/json-tmp';

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
        return 'Portal Engine JSON';
    }

    /**
     * @inheritDoc
     */
    public function add(string $downloadUniqueId, $element)
    {
        $exportableFields = $this->getExportService()->getExportableFields($element);
        $exportData = $this->getExportData($exportableFields);
        $this->tmpStoreService->appendToTmpStoreData($downloadUniqueId, $exportData);
    }

    /**
     * @inheritDoc
     */
    public function bundle(string $downloadUniqueId): string
    {
        $data = $this->tmpStoreService->getTmpStoreData($downloadUniqueId);

        if (!file_exists(self::JSON_EXPORT_BASE_FOLDER)) {
            mkdir(self::JSON_EXPORT_BASE_FOLDER, 0777, true);
        }

        $exportFileName = self::JSON_EXPORT_BASE_FOLDER . '/' . $downloadUniqueId . '.json';
        file_put_contents($exportFileName, json_encode($data));

        $this->tmpStoreService->clearTmpSToreData($downloadUniqueId);

        return $exportFileName;
    }

    /**
     * @inheritDoc
     */
    public function getDownloadFilename(string $downloadUniqueId): string
    {
        return 'structured-data.json';
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
            $exportData[$exportableField->getName()] = $exportableField->getData();
        }

        return $exportData;
    }
}
