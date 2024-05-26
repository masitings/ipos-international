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

namespace Pimcore\AssetMetadataClassDefinitionsBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration\Dao;
use Pimcore\Config;
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
        $file = Config::locateConfigFile(Dao::CONFIG_FILE);

        $installed = true;
        if (!file_exists($file)) {
            $installed = false;
        }
        SettingsStore::set('BUNDLE_INSTALLED__Pimcore\\AssetMetadataClassDefinitionsBundle\\PimcoreAssetMetadataClassDefinitionsBundle', $installed, 'bool', 'pimcore');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
