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

namespace Pimcore\Bundle\PortalEngineBundle\Migrations\PimcoreX;

use Doctrine\DBAL\Schema\Schema;
use Elasticsearch\Client;
use Pimcore\Bundle\PortalEngineBundle\Enum\Index\Statistics\ElasticSearchAlias;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\ElasticSearchConfigService;
use Pimcore\Bundle\PortalEngineBundle\Tools\EsClientFactory;
use Pimcore\Migrations\BundleAwareMigration;
use Pimcore\Model\DataObject\ClassDefinition;

class Version20210526151000 extends BundleAwareMigration
{
    protected function getBundleName(): string
    {
        return 'PimcorePortalEngineBundle';
    }

    protected function createIndexBasedOnTemplate(Client $esClient, $templateIndexName, $newIndexName)
    {
        $exists = $esClient->indices()->exists(['index' => $templateIndexName]);
        if (!$exists) {
            throw new \Exception('Templateindex ' . $templateIndexName . ' does not exist.');
        }

        $templateSettings = $esClient->indices()->getSettings(['index' => $templateIndexName]);
        $templateSettings = $templateSettings[$templateIndexName]['settings']['index'];
        $blacklistKeys = ['mapping', 'provided_name', 'creation_date', 'uuid', 'version'];
        $settings = array_diff_key($templateSettings, array_flip($blacklistKeys));

        $templateMapping = $esClient->indices()->getMapping(['index' => $templateIndexName]);

        $esClient->indices()->create([
            'index' => $newIndexName,
            'include_type_name' => false,
            'body' => [
                'mappings' => $templateMapping[$templateIndexName]['mappings'],
                'settings' => $settings
            ]
        ]);
    }

    protected function migrateIndex(string $indexName, Client $esClient, bool $updateClassDefinitionAlias = false)
    {
        $configService = \Pimcore::getContainer()->get(ElasticSearchConfigService::class);

        $exists = $esClient->indices()->exists([
            'index' => $indexName,
            'client' => [
                'ignore' => 404
            ]
        ]);

        $existsAlias = $esClient->indices()->existsAlias([
            'name' => $indexName,
            'client' => [
                'ignore' => 404
            ]
        ]);

        if ($existsAlias) {
            $this->write($indexName . ' already a alias, do nothing.');

            return;
        }

        if ($exists) {
            $this->write('Migrate ' . $indexName);

            $newIndexName = $indexName . '-odd';

            $this->createIndexBasedOnTemplate($esClient, $indexName, $newIndexName);

            //move index to new naming schema
            $esClient->reindex([
                'body' => [
                    'source' => [
                        'index' => $indexName,

                    ],
                    'dest' => [
                        'index' => $newIndexName,
                    ],
                ],
            ]);

            //delete old index
            $esClient->indices()->delete(['index' => $indexName]);

            //create alias for new index
            $esClient->indices()->putAlias([
                'index' => $newIndexName,
                'name' => $indexName,
            ]);

            if ($updateClassDefinitionAlias) {
                $esClient->indices()->putAlias([
                    'index' => $newIndexName,
                    'name' => $configService->getIndexName(ElasticSearchAlias::CLASS_DEFINITIONS),
                ]);
            }
        } else {
            $this->write($indexName . ' does not exist, do nothing.');
        }
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->write('Migrating ES indexes to new naming schema...');

        $configService = \Pimcore::getContainer()->get(ElasticSearchConfigService::class);
        $esClient = EsClientFactory::getESClient($configService);

        $classDefinitions = new ClassDefinition\Listing();

        $this->migrateIndex($configService->getIndexName('asset'), $esClient);
        foreach ($classDefinitions->load() as $classDefinition) {
            $indexName = $configService->getIndexName($classDefinition->getName());
            $this->migrateIndex($indexName, $esClient, true);
        }
    }

    protected function unMigrateIndex(string $indexName, Client $esClient)
    {
        $this->write('Migrate ' . $indexName);

        $existsOdd = $esClient->indices()->exists([
            'index' => $indexName . '-odd',
            'client' => [
                'ignore' => 404
            ]
        ]);

        $existsEven = $esClient->indices()->exists([
            'index' => $indexName . '-even',
            'client' => [
                'ignore' => 404
            ]
        ]);

        if ($existsOdd) {
            $fullIndexName = $indexName . '-odd';
        } elseif ($existsEven) {
            $fullIndexName = $indexName . '-even';
        } else {
            $this->write('Index ' . $indexName . ' has no fullindex. Do nothing.');

            return;
        }

        $esClient->indices()->deleteAlias(['index' => $fullIndexName, 'name' => $indexName]);

        $this->createIndexBasedOnTemplate($esClient, $fullIndexName, $indexName);

        //move index to old naming schema
        $esClient->reindex([
            'body' => [
                'source' => [
                    'index' => $fullIndexName,

                ],
                'dest' => [
                    'index' => $indexName,
                ],
            ],
        ]);

        //delete old index
        $esClient->indices()->delete(['index' => $fullIndexName]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->write('Migrating ES indexes to old naming schema...');

        $configService = \Pimcore::getContainer()->get(ElasticSearchConfigService::class);
        $esClient = EsClientFactory::getESClient($configService);

        $classDefinitions = new ClassDefinition\Listing();

        $this->unMigrateIndex($configService->getIndexName('asset'), $esClient);
        foreach ($classDefinitions->load() as $classDefinition) {
            $indexName = $configService->getIndexName($classDefinition->getName());
            $this->unMigrateIndex($indexName, $esClient);

            $esClient->indices()->putAlias([
                'index' => $indexName,
                'name' => $configService->getIndexName(ElasticSearchAlias::CLASS_DEFINITIONS),
            ]);
        }
    }
}
