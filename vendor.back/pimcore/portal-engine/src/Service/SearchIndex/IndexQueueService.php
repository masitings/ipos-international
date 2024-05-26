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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex;

use Carbon\Carbon;
use Pimcore\Bundle\PortalEngineBundle\Enum\Index\DatabaseConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Asset\IndexService as AssetIndexService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\IndexService as DataObjectIndexService;
use Pimcore\Db;
use Pimcore\Db\ConnectionInterface;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Tag;
use Psr\Log\LoggerInterface;

/**
 * Class IndexQueueService
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex
 */
class IndexQueueService
{
    /** @var LoggerInterface */
    protected $logger;
    /** @var ElasticSearchConfigService */
    protected $elasticSearchConfigService;
    /** @var DataObjectIndexService */
    protected $dataObjectIndexService;
    /** @var AssetIndexService */
    protected $assetIndexService;

    /** @var bool */
    protected $performIndexRefresh = false;

    /**
     * IndexQueueService constructor.
     *
     * @param LoggerInterface $logger
     * @param ElasticSearchConfigService $elasticSearchConfigService
     * @param DataObjectIndexService $dataObjectIndexService
     * @param AssetIndexService $assetIndexService
     */
    public function __construct(LoggerInterface $logger, ElasticSearchConfigService $elasticSearchConfigService, DataObjectIndexService $dataObjectIndexService, AssetIndexService $assetIndexService)
    {
        $this->logger = $logger;
        $this->elasticSearchConfigService = $elasticSearchConfigService;
        $this->dataObjectIndexService = $dataObjectIndexService;
        $this->assetIndexService = $assetIndexService;
    }

    /**
     * @return Db\Connection|ConnectionInterface
     */
    protected function getDb()
    {
        return Db::get();
    }

    /**
     * @param ElementInterface|Concrete|Asset $element
     * @param string $operation
     * @param bool $doIndexElement Index given element directly instead of add to queue
     *
     * @return $this
     */
    public function updateIndexQueue(ElementInterface $element, string $operation, bool $doIndexElement = false)
    {
        try {
            if (!$this->isOperationValid($operation)) {
                throw new \Exception(sprintf('operation %s not valid', $operation));
            }

            $oldFullPath = $element instanceof Asset\Folder ? $this->getCurrentIndexFullPath($element) : null;

            if ($doIndexElement) {
                $this->doHandleIndexData($element, $operation);
            }

            /** @var string $elementType */
            $elementType = $this->getElementType($element);
            /** @var int $currentQueueTableOperationTime */
            $currentQueueTableOperationTime = $this->getCurrentQueueTableOperationTime();

            /** @var string $tableName */
            if ($element instanceof AbstractObject) {
                $tableName = 'objects';
                $or = $doIndexElement ? '' : sprintf("o_id = '%s' OR", $element->getId());
                $sql = "SELECT o_id, '%s', o_className, '%s', '%s' FROM %s WHERE (%s o_path LIKE '%s') and o_type != 'folder'";
                $selectQuery = sprintf($sql,
                    $elementType,
                    $operation,
                    $currentQueueTableOperationTime,
                    $tableName,
                    $or,
                    $element->getRealFullPath() . '/%'
                );
            } else {
                $tableName = 'assets';
                $or = $doIndexElement ? '' : sprintf("id = '%s' OR", $element->getId());
                $sql = "SELECT id, '%s', '%s', '%s', '%s' FROM %s WHERE %s path LIKE '%s'";
                $selectQuery = sprintf($sql,
                    $elementType,
                    $this->getElementIndexName($element),
                    $operation,
                    $currentQueueTableOperationTime,
                    $tableName,
                    $or,
                    $element->getRealFullPath() . '/%'
                );
            }

            if (!$doIndexElement || !($element instanceof Asset) || $element instanceof Asset\Folder) {
                $this->getDb()->executeQuery(sprintf('INSERT INTO %s (%s) %s ON DUPLICATE KEY UPDATE operation = VALUES(operation), operationTime = VALUES(operationTime)',
                    DatabaseConfig::QUEUE_TABLE_NAME,
                    implode(',', ['elementId', 'elementType', 'elementIndexName', 'operation', 'operationTime']),
                    $selectQuery
                ));
            }

            if ($element instanceof Asset) {
                $this->updateAssetDependencies($element);
            }

            if ($element instanceof Asset\Folder && !empty($oldFullPath) && $oldFullPath !== $element->getRealFullPath()) {
                $this->rewriteChildrenIndexPaths($element, $oldFullPath);
            }
        } catch (\Exception $e) {
            $this->logger->warning('Update indexQueue in database-table' . DatabaseConfig::QUEUE_TABLE_NAME . ' failed! Error: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getUnhandledIndexQueueEntries()
    {
        /** @var array $unhandledIndexQueueEntries */
        $unhandledIndexQueueEntries = [];

        try {
            $unhandledIndexQueueEntries = $this->getDb()->executeQuery('SELECT elementId, elementType, elementIndexName, operation, operationTime FROM ' . DatabaseConfig::QUEUE_TABLE_NAME . ' ORDER BY operationTime')->fetchAllAssociative();
        } catch (\Exception $e) {
            $this->logger->info('getUnhandledIndexQueueEntries failed! Error: ' . $e->getMessage());
        }

        return $unhandledIndexQueueEntries;
    }

    /**
     * @param $entry
     *
     * @return $this
     */
    public function handleIndexQueueEntry($entry)
    {
        try {
            $this->logger->info(DatabaseConfig::QUEUE_TABLE_NAME . ' updating index for element ' . $entry['elementId'] . ' and type ' . $entry['elementType']);

            /** @var AbstractObject|Asset|null $element */
            $element = $this->getElement($entry['elementId'], $entry['elementType']);
            if ($element) {
                $this->doHandleIndexData($element, $entry['operation']);
            }

            //delete handled entry from queue table
            $this->getDb()->executeQuery('DELETE FROM ' . DatabaseConfig::QUEUE_TABLE_NAME . ' WHERE elementId = ? AND elementType = ? AND operation = ? AND operationTime = ?', [
                $entry['elementId'],
                $entry['elementType'],
                $entry['operation'],
                $entry['operationTime']
            ]);
        } catch (\Exception $e) {
            $this->logger->info('handleIndexQueueEntry failed! Error: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * @param ClassDefinition $classDefinition
     *
     * @return $this
     */
    public function updateDataObjects($classDefinition)
    {
        /** @var string $tableName */
        $dataObjectTableName = 'object_' . $classDefinition->getId();
        /** @var string $selectQuery */
        $selectQuery = sprintf("SELECT oo_id, '%s', '%s', '%s', '%s' FROM %s",
            DatabaseConfig::QUEUE_TABLE_COLUMN_ELEMENT_TYPE_DATA_OBJECT,
            $classDefinition->getName(),
            DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE,
            $this->getCurrentQueueTableOperationTime(),
            $dataObjectTableName
        );

        $this->updateBySelectQuery($selectQuery);

        return $this;
    }

    /**
     * @return $this
     */
    public function updateAssets()
    {
        /** @var string $selectQuery */
        $selectQuery = sprintf("SELECT id, '%s', '%s', '%s', '%s' FROM %s",
            DatabaseConfig::QUEUE_TABLE_COLUMN_ELEMENT_TYPE_ASSET,
            'asset',
            DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE,
            $this->getCurrentQueueTableOperationTime(),
            'assets'
        );
        $this->updateBySelectQuery($selectQuery);

        return $this;
    }

    /**
     * @return $this
     */
    public function updateByTag(Tag $tag)
    {
        //assets
        $selectQuery = sprintf("SELECT id, '%s', '%s', '%s', '%s' FROM assets where id in (select cid from tags_assignment where ctype='asset' and tagid = %s)",
            DatabaseConfig::QUEUE_TABLE_COLUMN_ELEMENT_TYPE_ASSET,
            'asset',
            DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE,
            $this->getCurrentQueueTableOperationTime(),
            $tag->getId()
        );
        $this->updateBySelectQuery($selectQuery);

        //data objects
        $selectQuery = sprintf("SELECT o_id, '%s', o_className, '%s', '%s' FROM objects where o_id in (select cid from tags_assignment where ctype='object' and tagid = %s)",
            DatabaseConfig::QUEUE_TABLE_COLUMN_ELEMENT_TYPE_DATA_OBJECT,
            DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE,
            $this->getCurrentQueueTableOperationTime(),
            $tag->getId()
        );
        $this->updateBySelectQuery($selectQuery);

        return $this;
    }

    /**
     * @param ElementInterface $element
     *
     * @return string|null
     *
     * @throws \Exception
     */
    protected function getCurrentIndexFullPath(ElementInterface $element)
    {
        if ($indexService = $this->getIndexServiceByElement($element)) {
            $indexName = $this->elasticSearchConfigService->getIndexName($this->getElementIndexName($element));

            return $indexService->getCurrentIndexFullPath($element, $indexName);
        }

        return null;
    }

    /**
     * Directly update children paths in elasticsearch for assets as otherwise you might get strange results if you rename a folder in the portal engine frontend.
     *
     * @param ElementInterface $element
     * @param string $oldFullPath
     *
     * @throws \Exception
     */
    protected function rewriteChildrenIndexPaths(ElementInterface $element, string $oldFullPath)
    {
        if ($element instanceof Asset && $indexService = $this->getIndexServiceByElement($element)) {
            $indexName = $this->elasticSearchConfigService->getIndexName($this->getElementIndexName($element));
            $indexService->rewriteChildrenIndexPaths($element, $indexName, $oldFullPath);
        }
    }

    protected function updateBySelectQuery(string $selectQuery)
    {
        try {
            $this->getDb()->executeQuery(sprintf('INSERT INTO %s (%s) %s ON DUPLICATE KEY UPDATE operation = VALUES(operation), operationTime = VALUES(operationTime)',
                DatabaseConfig::QUEUE_TABLE_NAME,
                implode(',', ['elementId', 'elementType', 'elementIndexName', 'operation', 'operationTime']),
                $selectQuery
            ));
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    /**
     * @param ElementInterface $element
     *
     * @return $this
     */
    public function refreshIndexByElement(ElementInterface $element)
    {
        try {
            /** @var string $indexName */
            $indexName = $this->elasticSearchConfigService->getIndexName($this->getElementIndexName($element));

            switch ($element) {
                case $element instanceof AbstractObject:
                    $this->dataObjectIndexService->refreshIndex($indexName);
                    break;
                case $element instanceof Asset:
                    $this->assetIndexService->refreshIndex($indexName);
                    break;
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return $this;
    }

    /**
     * @param Asset $asset
     *
     * @return $this
     */
    protected function updateAssetDependencies(Asset $asset)
    {
        foreach ($asset->getDependencies()->getRequiredBy() as $requiredByEntry) {

            /** @var ElementInterface $element */
            $element = null;

            if ('object' === $requiredByEntry['type']) {
                $element = AbstractObject::getById($requiredByEntry['id']);
            }
            if ('asset' === $requiredByEntry['type']) {
                $element = Asset::getById($requiredByEntry['id']);
            }
            if ($element) {
                $this->updateIndexQueue($element, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE);
            }
        }

        return $this;
    }

    /**
     * @param ElementInterface $element
     * @param string $operation
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function doHandleIndexData(ElementInterface $element, string $operation)
    {
        /** @var AbstractIndexService $indexService */
        $indexService = $this->getIndexServiceByElement($element);
        /** @var bool $indexServicePerformIndexRefreshBackup */
        $indexServicePerformIndexRefreshBackup = $indexService->isPerformIndexRefresh();

        $indexService->setPerformIndexRefresh($this->performIndexRefresh);

        switch ($operation) {
            case DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE:
                $this->doUpdateIndexData($element);
                break;
            case DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_DELETE:
                $this->doDeleteFromIndex($element);
                break;
        }

        $indexService->setPerformIndexRefresh($indexServicePerformIndexRefreshBackup);

        return $this;
    }

    /**
     * @param $element
     *
     * @return AbstractIndexService
     */
    protected function getIndexServiceByElement(ElementInterface $element)
    {
        /** @var AbstractIndexService $indexService */
        $indexService = null;

        switch ($element) {
            case $element instanceof AbstractObject:
                $indexService = $this->dataObjectIndexService;
                break;
            case $element instanceof Asset:
                $indexService = $this->assetIndexService;
                break;
        }

        return $indexService;
    }

    /**
     * @param ElementInterface $element
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function doUpdateIndexData(ElementInterface $element)
    {
        $this
            ->getIndexServiceByElement($element)
            ->doUpdateIndexData($element);

        return $this;
    }

    /**
     * @param ElementInterface $element
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function doDeleteFromIndex(ElementInterface $element)
    {
        /** @var int $elementId */
        $elementId = $element->getId();
        /** @var string $elementIndexName */
        $elementIndexName = $this->getElementIndexName($element);

        $this
            ->getIndexServiceByElement($element)
            ->doDeleteFromIndex($elementId, $elementIndexName);

        return $this;
    }

    /**
     * @param string $operation
     *
     * @return bool
     */
    protected function isOperationValid($operation)
    {
        return in_array($operation, [
            DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_UPDATE,
            DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_DELETE
        ]);
    }

    /**
     * Get current timestamp + milliseconds
     *
     * @return int
     */
    protected function getCurrentQueueTableOperationTime()
    {
        /** @var Carbon $carbonNow */
        $carbonNow = Carbon::now();

        return (int)($carbonNow->getTimestamp() . str_pad((string)$carbonNow->milli, 3, '0'));
    }

    /**
     * @param $id
     * @param $type
     *
     * @return Asset|AbstractObject|null
     *
     * @throws \Exception
     */
    protected function getElement($id, $type)
    {
        switch ($type) {
            case DatabaseConfig::QUEUE_TABLE_COLUMN_ELEMENT_TYPE_ASSET:
                return Asset::getById($id);
            case DatabaseConfig::QUEUE_TABLE_COLUMN_ELEMENT_TYPE_DATA_OBJECT:
                return AbstractObject::getById($id);
            default:
                throw new \Exception('elementType ' . $type . ' not supported');
        }
    }

    /**
     * @param ElementInterface $element
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function getElementType($element)
    {
        switch ($element) {
            case $element instanceof AbstractObject:
                return DatabaseConfig::QUEUE_TABLE_COLUMN_ELEMENT_TYPE_DATA_OBJECT;
            case $element instanceof Asset:
                return DatabaseConfig::QUEUE_TABLE_COLUMN_ELEMENT_TYPE_ASSET;
            default:
                throw new \Exception('element ' . get_class($element) . ' not supported');
        }
    }

    /**
     * @param ElementInterface $element
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function getElementIndexName($element)
    {
        switch ($element) {
            case $element instanceof Concrete:
                return $element->getClassName();
            case $element instanceof Asset:
                return 'asset';
            default:
                throw new \Exception('element ' . get_class($element) . ' not supported');
        }
    }

    /**
     * @return bool
     */
    public function isPerformIndexRefresh(): bool
    {
        return $this->performIndexRefresh;
    }

    /**
     * @param bool $performIndexRefresh
     *
     * @return IndexQueueService
     */
    public function setPerformIndexRefresh(bool $performIndexRefresh): self
    {
        $this->performIndexRefresh = $performIndexRefresh;

        return $this;
    }
}
