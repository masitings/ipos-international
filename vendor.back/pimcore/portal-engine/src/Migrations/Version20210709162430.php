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
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\PortalConfig;
use Pimcore\Bundle\PortalEngineBundle\Tools\MigrationServiceLocator;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\Document;

class Version20210709162430 extends AbstractPimcoreMigration
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
        $migrationServiceLocator = \Pimcore::getContainer()->get(MigrationServiceLocator::class);
        $frontendBuildService = $migrationServiceLocator->getFrontendBuildService();

        $oldPath = PIMCORE_WEB_ROOT . '/portal-engine';

        $directories = glob($oldPath . '/build/portal_*', GLOB_ONLYDIR);

        $ids = [];
        foreach ($directories as $directory) {
            $matches = [];

            if (preg_match('/_([0-9]+)$/i', $directory, $matches)) {
                $ids[] = $matches[1];
            }
        }

        foreach ($ids as $id) {
            $this->writeMessage('Update Frontend Build for Portal ' . $id);
            $document = Document\Page::getById($id);
            $portalConfig = new PortalConfig($document);
            $frontendBuildService->publishCustomizedBuild($portalConfig);
        }

        $this->writeMessage('Update Customized Frontend Builds');
        $frontendBuildService->updateCustomizedFrontendBuildsJson();

        $this->writeMessage('Update portals.json and clear cache during terminate');
        $migrationServiceLocator->getDocumentConfigSubscriber()->setUpdatePortalsJson(true, true);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $migrationServiceLocator = \Pimcore::getContainer()->get(MigrationServiceLocator::class);
        $frontendBuildService = $migrationServiceLocator->getFrontendBuildService();

        $this->writeMessage('Update Customized Frontend Builds');
        $frontendBuildService->updateCustomizedFrontendBuildsJson();

        $this->writeMessage('Update portals.json and clear cache during terminate');
        $migrationServiceLocator->getDocumentConfigSubscriber()->setUpdatePortalsJson(true, true);
    }
}
