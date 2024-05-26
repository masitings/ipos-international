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

namespace Pimcore\Bundle\PortalEngineBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\Asset\Image\Thumbnail\Config;

class Version20210830112511 extends AbstractPimcoreMigration
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
        if (Config::exists('portal-engine-logo')) {
            $this->writeMessage('Thumbnail `portal-engine-logo` exists, skipping...');

            return;
        }

        $this->writeMessage('Copy thumbnail `portal-engine_footer-logo` to `portal-engine-logo`.');

        $config = Config::getByName('portal-engine_footer-logo');
        $config->setName('portal-engine-logo');
        $config->save(true);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        if (Config::exists('portal-engine_footer-logo')) {
            $this->writeMessage('Thumbnail `portal-engine_footer-logo` exists, skipping...');

            return;
        }

        $this->writeMessage('Copy thumbnail `portal-engine-logo` to `portal-engine_footer-logo`.');

        $config = Config::getByName('portal-engine-logo');
        $config->setName('portal-engine_footer-logo');
        $config->save(true);
    }
}
