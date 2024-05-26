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
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\ControllerReference;
use Pimcore\Db;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Pimcore\Model\Document;
use Pimcore\Model\Document\DocType;
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
            ['PimcorePortalEngineBundle', '00000001']
        );

        if ($entry && !empty($entry['migrated_at'])) {
            SettingsStore::set('BUNDLE_INSTALLED__Pimcore\\Bundle\\PortalEngineBundle\\PimcorePortalEngineBundle', true, 'bool', 'pimcore');
        }

        // document types
        $docTypes = new DocType\Listing();
        $docTypes->load();
        foreach ($docTypes->getDocTypes() as $docType) {
            $this->migrate($docType);
        }

        // documents

        foreach (['documents_page', 'documents_snippet'] as $tablename) {
            $ids = $db->fetchCol(sprintf('SELECT documents.id FROM documents INNER JOIN %s sub ON documents.id = sub.id WHERE sub.controller IN (%s)',
                $tablename,
                "'" . implode("','", [
                    '@Pimcore\\\\Bundle\\\\PortalEngineBundle\\\\Controller\\\\PortalController',
                    '@Pimcore\\\\Bundle\\\\PortalEngineBundle\\\\Controller\\\\DataPool\\\\DataObjectController',
                    '@Pimcore\\\\Bundle\\\\PortalEngineBundle\\\\Controller\\\\DataPool\\\\AssetController',
                    '@Pimcore\\\\Bundle\\\\PortalEngineBundle\\\\Controller\\\\SnippetController',
                    '@Pimcore\\\\Bundle\\\\PortalEngineBundle\\\\Controller\\\\LanguageVariantController',
                ]) . "'")
            );

            $documents = new Document\Listing();
            $documents->setCondition('id IN (:ids)', ['ids' => $ids]);

            foreach ($documents as $document) {
                if ($document instanceof Document\PageSnippet) {
                    $this->migrate($document);
                }
            }
        }
    }

    private function migrate($entity)
    {
        if (
            $entity->getController() === '@Pimcore\Bundle\PortalEngineBundle\Controller\PortalController' &&
            $entity->getAction() === 'page'
        ) {
            $entity->setController(ControllerReference::PORTAL_PAGE);
            $entity->setAction(null);
            $entity->save();
        }

        if (
            $entity->getController() === '@Pimcore\Bundle\PortalEngineBundle\Controller\PortalController' &&
            $entity->getAction() === 'content'
        ) {
            $entity->setController(ControllerReference::PORTAL_CONTENT_PAGE);
            $entity->setAction(null);
            $entity->save();
        }

        if (
            $entity->getController() === '@Pimcore\Bundle\PortalEngineBundle\Controller\DataPool\DataObjectController' &&
            $entity->getAction() === 'list'
        ) {
            $entity->setController(ControllerReference::DATA_POOL_DATA_OBJECTS_LIST);
            $entity->setAction(null);
            $entity->save();
        }

        if (
            $entity->getController() === '@Pimcore\Bundle\PortalEngineBundle\Controller\DataPool\AssetController' &&
            $entity->getAction() === 'list'
        ) {
            $entity->setController(ControllerReference::DATA_POOL_ASSETS_LIST);
            $entity->setAction(null);
            $entity->save();
        }

        if (
            $entity->getController() === '@Pimcore\Bundle\PortalEngineBundle\Controller\SnippetController' &&
            $entity->getAction() === 'footer'
        ) {
            $entity->setController(ControllerReference::SNIPPET_FOOTER);
            $entity->setAction(null);
            $entity->save();
        }

        if (
            $entity->getController() === '@Pimcore\Bundle\PortalEngineBundle\Controller\LanguageVariantController' &&
            $entity->getAction() === 'data-pool-language-variant'
        ) {
            $entity->setController(ControllerReference::LANGUAGE_VARIANT_DATA_POOL);
            $entity->setAction(null);
            $entity->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
