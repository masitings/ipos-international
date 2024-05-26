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

namespace Pimcore\AssetMetadataClassDefinitionsBundle;

use Pimcore\AssetMetadataClassDefinitionsBundle\Migrations\Version20210305134111;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration\Dao;
use Pimcore\Config;
use Pimcore\Db;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Pimcore\File;

class Installer extends SettingsStoreAwareInstaller
{
    public function needsReloadAfterInstall(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $db = Db::get();

        $db->query("ALTER TABLE `assets_metadata`
	        CHANGE COLUMN `type` `type` ENUM('input','textarea','asset','document',
	        'object','date','select','checkbox','wysiwyg',
	        'country','language','multiselect','numeric','calculatedValue','datetime','user','manyToManyRelation') NULL DEFAULT NULL AFTER `language`;");

        File::putPhpFile(Config::locateConfigFile(Dao::CONFIG_FILE), to_php_data_file_format([]));

        parent::install();

        return true;
    }

    public function uninstall()
    {
        //nothing to do due to potential data loss
        parent::uninstall();
    }

    public function getLastMigrationVersionClassName(): ?string
    {
        return Version20210305134111::class;
    }
}
