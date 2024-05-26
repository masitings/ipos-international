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

namespace Pimcore\Bundle\StatisticsExplorerBundle;

use Pimcore\Bundle\StatisticsExplorerBundle\Entity\Configuration;
use Pimcore\Bundle\StatisticsExplorerBundle\Entity\ConfigurationShare;
use Pimcore\Bundle\StatisticsExplorerBundle\Migrations\PimcoreX\Version20210305134111;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Pimcore\Model\Translation;
use Pimcore\Tool;

class Installer extends SettingsStoreAwareInstaller
{
    public function install()
    {
        $this->installTables();
        $this->installSharedTranslations();

        parent::install();
    }

    protected function installTables()
    {
        $db = \Pimcore\Db::get();
        $currentSchema = $db->getSchemaManager()->createSchema();
        $schema = $db->getSchemaManager()->createSchema();

        if (!$schema->hasTable(Configuration::TABLE)) {
            $table = $schema->createTable(Configuration::TABLE);
            $table->addColumn('id', 'string', [
                'length' => 36,
                'notnull' => true
            ]);
            $table->addColumn('context', 'string', [
                'length' => 100,
                'notnull' => true
            ]);
            $table->addColumn('ownerId', 'integer', [
                'notnull' => false
            ]);
            $table->addColumn('name', 'string', [
                'length' => 100,
                'notnull' => false
            ]);
            $table->addColumn('modificationDate', 'integer', [
                'notnull' => true
            ]);
            $table->addColumn('configuration', 'text', [
                'notnull' => false
            ]);
            $table->setPrimaryKey(['id']);
        }

        if (!$schema->hasTable(ConfigurationShare::TABLE)) {
            $table = $schema->createTable(ConfigurationShare::TABLE);
            $table->addColumn('sharedWithType', 'string', [
                'length' => 20,
                'notnull' => true
            ]);
            $table->addColumn('sharedWithId', 'integer', [
                'notnull' => true
            ]);
            $table->addColumn('configurationId', 'string', [
                'length' => 36,
                'notnull' => true
            ]);
            $table->setPrimaryKey(['sharedWithType', 'configurationId', 'sharedWithId']);
        }

        $sqlStatements = $currentSchema->getMigrateToSql($schema, $db->getDatabasePlatform());
        if (!empty($sqlStatements)) {
            $db->exec(implode(';', $sqlStatements));
        }
    }

    protected function installSharedTranslations()
    {
        /** @var string[] $sharedTranslationFilePaths */
        $sharedTranslationFilePaths = [
            __DIR__ . '/../install/shared_translation_source/de.csv' => 'de',
            __DIR__ . '/../install/shared_translation_source/en.csv' => 'en'
        ];

        foreach ($sharedTranslationFilePaths as $sharedTranslationFilePath => $language) {
            try {
                if (!in_array($language, Tool::getValidLanguages())) {
                    throw new \Exception(sprintf('language "%s" not enabled in system settings', $language));
                }

                /** @var array|false $sharedTranslations */
                $sharedTranslations = file($sharedTranslationFilePath);

                if (!is_array($sharedTranslations)) {
                    throw new \Exception('file has no valid array content');
                }

                // remove first array entry, its just the column title, e.g. "key;en"
                array_shift($sharedTranslations);

                /** @var string $sharedTranslation */
                foreach ($sharedTranslations as $sharedTranslation) {
                    /** @var array $rowContent */
                    $rowContent = explode(';', $sharedTranslation, 2);

                    /** @var string $sharedTranslationKey */
                    $sharedTranslationKey = PimcoreStatisticsExplorerBundle::TRANSLATION_PREFIX . $rowContent[0];
                    /** @var string $sharedTranslationValue */
                    $sharedTranslationValue = htmlspecialchars_decode(str_replace("\r\n", '', ($rowContent[1] ?? '')));

                    $websiteTranslation = Translation::getByKey($sharedTranslationKey, Translation::DOMAIN_DEFAULT, true);
                    if (!$websiteTranslation->getTranslation($language)) {
                        $websiteTranslation->addTranslation($language, $sharedTranslationValue);
                        $websiteTranslation->setModificationDate(time());
                        $websiteTranslation->save();
                    }
                }

                $this->getOutput()->write(sprintf('SharedTranslation csv file "%s" successfully installed', $sharedTranslationFilePath));
            } catch (\Exception $e) {
                $this->getOutput()->write(sprintf('Install for sharedTranslation csv file %s skipped. Error: %s', $sharedTranslationFilePath, $e->getMessage()));
            }
        }

        return $this;
    }

    public function needsReloadAfterInstall()
    {
        return true;
    }

    public function getLastMigrationVersionClassName(): ?string
    {
        return Version20210305134111::class;
    }
}
