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

namespace Pimcore\Bundle\PortalEngineBundle\Installer;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Bundle\PortalEngineBundle\Entity\BatchTask;
use Pimcore\Bundle\PortalEngineBundle\Entity\BatchTaskProcessedItem;
use Pimcore\Bundle\PortalEngineBundle\Entity\Collection;
use Pimcore\Bundle\PortalEngineBundle\Entity\CollectionItem;
use Pimcore\Bundle\PortalEngineBundle\Entity\CollectionShare;
use Pimcore\Bundle\PortalEngineBundle\Entity\DownloadCart;
use Pimcore\Bundle\PortalEngineBundle\Entity\DownloadCartItem;
use Pimcore\Bundle\PortalEngineBundle\Entity\PublicShare;
use Pimcore\Bundle\PortalEngineBundle\Entity\PublicShareItem;
use Pimcore\Bundle\PortalEngineBundle\Entity\SavedSearch;
use Pimcore\Bundle\PortalEngineBundle\Entity\SavedSearchShare;
use Pimcore\Bundle\PortalEngineBundle\Enum\Index\DatabaseConfig;

class TableInstaller
{
    public function installTables(Schema $schema)
    {
        $this
            ->addDownloadCartTable($schema)
            ->addDownloadCartItemTable($schema)
            ->addCollectionTable($schema)
            ->addCollectionItemTable($schema)
            ->addCollectionShareTable($schema)
            ->addBatchTaskTable($schema)
            ->addBatchTaskProcessedItemsTable($schema)
            ->addSavedSearchTable($schema)
            ->addSavedSearchShareTable($schema)
            ->addPublicShareTable($schema)
            ->addPublicShareItemTable($schema)
            ->addSearchIndexQueueTable($schema);
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function addDownloadCartTable(Schema $schema)
    {
        if (!$schema->hasTable(DownloadCart::TABLE)) {
            $downloadCart = $schema->createTable(DownloadCart::TABLE);

            $downloadCart->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true
            ]);

            $downloadCart->addColumn('userId', 'integer', [
                'notnull' => true
            ]);

            $downloadCart->setPrimaryKey(['id']);
        }

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function addDownloadCartItemTable(Schema $schema)
    {
        if (!$schema->hasTable(DownloadCartItem::TABLE)) {
            $downloadItem = $schema->createTable(DownloadCartItem::TABLE);

            $downloadItem->addColumn('cartId', 'integer', [
                'notnull' => true
            ]);

            $downloadItem->addColumn('elementId', 'integer', [
                'notnull' => true
            ]);

            $downloadItem->addColumn('elementType', 'string', [
                'notnull' => true,
                'length' => 20
            ]);

            $downloadItem->addColumn('elementSubType', 'string', [
                'notnull' => true,
                'length' => 20
            ]);

            $downloadItem->addColumn('dataPoolId', 'integer', [
                'notnull' => true
            ]);

            $downloadItem->addColumn('configs', 'text', [
                'notnull' => true
            ]);

            $downloadItem->addColumn('createdAt', 'integer', [
                'notnull' => true
            ]);

            $downloadItem->addColumn('modifiedAt', 'integer', [
                'notnull' => true
            ]);

            $downloadItem->setPrimaryKey(['cartId', 'elementId', 'elementType', 'dataPoolId']);
        }

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function addCollectionTable(Schema $schema)
    {
        if (!$schema->hasTable(Collection::TABLE)) {
            $collection = $schema->createTable(Collection::TABLE);

            $collection->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true
            ]);

            $collection->addColumn('userId', 'integer', [
                'notnull' => true
            ]);

            $collection->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 100
            ]);

            $collection->addColumn('currentSiteId', 'integer', [
                'notnull' => true
            ]);

            $collection->addColumn('creationDate', 'integer', [
                'notnull' => true
            ]);

            $collection->addColumn('modificationDate', 'integer', [
                'notnull' => true
            ]);

            $collection->setPrimaryKey(['id']);
        }

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function addCollectionItemTable(Schema $schema)
    {
        if (!$schema->hasTable(CollectionItem::TABLE)) {
            $collectionItem = $schema->createTable(CollectionItem::TABLE);

            $collectionItem->addColumn('collectionId', 'integer', [
                'notnull' => true
            ]);

            $collectionItem->addColumn('elementId', 'integer', [
                'notnull' => true
            ]);

            $collectionItem->addColumn('elementType', 'string', [
                'notnull' => true,
                'length' => 20
            ]);

            $collectionItem->addColumn('elementSubType', 'string', [
                'notnull' => true,
                'length' => 20
            ]);

            $collectionItem->addColumn('dataPoolId', 'integer', [
                'notnull' => true
            ]);

            $collectionItem->setPrimaryKey(['collectionId', 'elementId', 'elementType', 'dataPoolId']);
        }

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function addCollectionShareTable(Schema $schema)
    {
        if (!$schema->hasTable(CollectionShare::TABLE)) {
            $collectionShare = $schema->createTable(CollectionShare::TABLE);

            $collectionShare->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true
            ]);

            $collectionShare->addColumn('collectionId', 'integer', [
                'notnull' => true
            ]);

            $collectionShare->addColumn('userId', 'integer', [
                'notnull' => false
            ]);

            $collectionShare->addColumn('userGroupId', 'integer', [
                'notnull' => false
            ]);

            $collectionShare->addColumn('permission', 'string', [
                'notnull' => true,
                'length' => 10
            ]);

            $collectionShare->addColumn('creationDate', 'integer', [
                'notnull' => true
            ]);

            $collectionShare->addColumn('modificationDate', 'integer', [
                'notnull' => true
            ]);

            $collectionShare->setPrimaryKey(['id']);
        }

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function addBatchTaskTable(Schema $schema)
    {
        if (!$schema->hasTable(BatchTask::TABLE)) {
            $table = $schema->createTable(BatchTask::TABLE);

            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true
            ]);

            $table->addColumn('userId', 'string', [
                'notnull' => true,
                'length' => 255
            ]);

            $table->addIndex(['userId']);

            $table->addColumn('type', 'string', [
                'notnull' => true,
                'length' => 30
            ]);

            $table->addColumn('totalItems', 'integer', [
                'notnull' => true
            ]);

            $table->addColumn('payload', 'json', [
                'notnull' => true
            ]);

            $table->addColumn('state', 'string', [
                'notnull' => true,
                'length' => 20
            ]);

            $table->addColumn('disableNotificationAction', 'boolean', [
                'notnull' => true,
            ]);

            $table->addColumn('disableDeleteConfirmation', 'boolean', [
                'notnull' => true,
            ]);

            $table->addColumn('createdAt', 'integer', [
                'notnull' => true
            ]);

            $table->addColumn('modifiedAt', 'integer', [
                'notnull' => true
            ]);

            $table->setPrimaryKey(['id']);
        }

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function addBatchTaskProcessedItemsTable(Schema $schema)
    {
        if (!$schema->hasTable(BatchTaskProcessedItem::TABLE)) {
            $table = $schema->createTable(BatchTaskProcessedItem::TABLE);

            $table->addColumn('taskId', 'integer', [
                'notnull' => true
            ]);

            $table->addColumn('itemIndex', 'integer', [
                'notnull' => true
            ]);

            $table->setPrimaryKey(['taskId', 'itemIndex']);
        }

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function addSavedSearchTable(Schema $schema)
    {
        if (!$schema->hasTable(SavedSearch::TABLE)) {
            $savedSearch = $schema->createTable(SavedSearch::TABLE);

            $savedSearch->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true
            ]);

            $savedSearch->addColumn('userId', 'integer', [
                'notnull' => true
            ]);

            $savedSearch->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 100
            ]);

            $savedSearch->addColumn('currentSiteId', 'integer', [
                'notnull' => true
            ]);

            $savedSearch->addColumn('urlQuery', 'string', [
                'notnull' => true,
                'length' => 255
            ]);

            $savedSearch->addColumn('creationDate', 'integer', [
                'notnull' => true
            ]);

            $savedSearch->addColumn('modificationDate', 'integer', [
                'notnull' => true
            ]);

            $savedSearch->setPrimaryKey(['id']);
        }

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function addSavedSearchShareTable(Schema $schema)
    {
        if (!$schema->hasTable(SavedSearchShare::TABLE)) {
            $savedSearchShare = $schema->createTable(SavedSearchShare::TABLE);

            $savedSearchShare->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true
            ]);

            $savedSearchShare->addColumn('searchId', 'integer', [
                'notnull' => true
            ]);

            $savedSearchShare->addColumn('userId', 'integer', [
                'notnull' => false
            ]);

            $savedSearchShare->addColumn('userGroupId', 'integer', [
                'notnull' => false
            ]);

            $savedSearchShare->addColumn('creationDate', 'integer', [
                'notnull' => true
            ]);

            $savedSearchShare->addColumn('modificationDate', 'integer', [
                'notnull' => true
            ]);

            $savedSearchShare->setPrimaryKey(['id']);
        }

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function addPublicShareTable(Schema $schema)
    {
        if (!$schema->hasTable(PublicShare::TABLE)) {
            $publicShare = $schema->createTable(PublicShare::TABLE);

            $publicShare->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true
            ]);

            $publicShare->addColumn('collectionId', 'integer', [
                'notnull' => false
            ]);

            $publicShare->addColumn('hash', 'string', [
                'notnull' => true,
                'length' => 32
            ]);

            $publicShare->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 100
            ]);

            $publicShare->addColumn('userId', 'integer', [
                'notnull' => true
            ]);

            $publicShare->addColumn('currentSiteId', 'integer', [
                'notnull' => true
            ]);

            $publicShare->addColumn('expiryDate', 'integer', [
                'notnull' => false
            ]);

            $publicShare->addColumn('showTermsText', 'boolean', [
                'notnull' => true,
            ]);

            $publicShare->addColumn('termsText', 'text', [
                'notnull' => false
            ]);

            $publicShare->addColumn('configs', 'text', [
                'notnull' => true
            ]);

            $publicShare->addColumn('creationDate', 'integer', [
                'notnull' => true
            ]);

            $publicShare->addColumn('modificationDate', 'integer', [
                'notnull' => true
            ]);

            $publicShare->setPrimaryKey(['id']);
        }

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function addPublicShareItemTable(Schema $schema)
    {
        if (!$schema->hasTable(PublicShareItem::TABLE)) {
            $publicShareItem = $schema->createTable(PublicShareItem::TABLE);

            $publicShareItem->addColumn('publicShareId', 'integer', [
                'notnull' => true
            ]);

            $publicShareItem->addColumn('elementId', 'integer', [
                'notnull' => true
            ]);

            $publicShareItem->addColumn('elementType', 'string', [
                'notnull' => true,
                'length' => 20
            ]);

            $publicShareItem->addColumn('elementSubType', 'string', [
                'notnull' => true,
                'length' => 20
            ]);

            $publicShareItem->addColumn('dataPoolId', 'integer', [
                'notnull' => true
            ]);

            $publicShareItem->setPrimaryKey(['publicShareId', 'elementId', 'elementType', 'dataPoolId']);
        }

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    private function addSearchIndexQueueTable(Schema $schema)
    {
        if (!$schema->hasTable(DatabaseConfig::QUEUE_TABLE_NAME)) {
            $queueTable = $schema->createTable(DatabaseConfig::QUEUE_TABLE_NAME);
            $queueTable->addColumn('elementId', 'bigint', ['default' => 0, 'notnull' => true]);
            $queueTable->addColumn('elementType', 'string', ['length' => 20, 'notnull' => false]);
            $queueTable->addColumn('elementIndexName', 'string', ['length' => 100, 'notnull' => false]);
            $queueTable->addColumn('operation', 'string', ['length' => 20, 'notnull' => false]);
            $queueTable->addColumn('operationTime', 'integer', ['length' => 14, 'notnull' => false]);
            $queueTable->setPrimaryKey(['elementId', 'elementType']);
        }

        return $this;
    }
}
