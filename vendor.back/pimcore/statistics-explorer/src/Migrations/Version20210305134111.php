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

namespace Pimcore\Bundle\StatisticsExplorerBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Db;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\Tool\SettingsStore;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20210305134111 extends AbstractPimcoreMigration
{
    public function doesSqlMigrations(): bool
    {
        return false;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $db = Db::get();
        $entry = $db->fetchRow(
            'SELECT * FROM pimcore_migrations WHERE migration_set = ? AND version = ?',
            ['PimcoreStatisticsExplorerBundle', '00000001']
        );

        if ($entry && !empty($entry['migrated_at'])) {
            SettingsStore::set('BUNDLE_INSTALLED__Pimcore\\Bundle\\StatisticsExplorerBundle\\PimcoreStatisticsExplorerBundle', true, 'bool', 'pimcore');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
