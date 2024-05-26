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

namespace Pimcore\Bundle\PortalEngineBundle;

use Doctrine\DBAL\Schema\Schema;
use Pimcore;
use Pimcore\Bundle\PortalEngineBundle\Enum\BackendPermission;
use Pimcore\Bundle\PortalEngineBundle\Enum\Document\ControllerReference;
use Pimcore\Bundle\PortalEngineBundle\Enum\Routing;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\IndexUpdateService;
use Pimcore\Logger;
use Pimcore\Model\Asset\Image\Thumbnail\Config;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Service;
use Pimcore\Model\User\Permission\Definition;

/**
 * Class Installer
 *
 * @package Installer
 */
class Installer extends Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller
{
    /**
     * @var Pimcore\Bundle\PortalEngineBundle\Installer\TableInstaller
     */
    private $tableInstaller;

    /**
     * @var IndexUpdateService
     */
    private $indexUpdatService;

    public function install()
    {
        $this->beforeInstall();

        $this
            ->installClasses()
            ->installTables()
            ->installThumbnails()
            ->installDocTypes()
            ->installPredefinedProperties()
            ->installSharedTranslations()
            ->installAdminTranslations()
            ->installBackendPermissions()
        ;

        $this->afterInstall();

        parent::install();
    }

    /**
     * @throws Pimcore\Model\Element\ValidationException
     */
    protected function beforeInstall()
    {
        /** @var string[] $requiredParameters */
        $requiredParameters = ['pimcore_portal_engine.elasticsearch.host', 'pimcore_portal_engine.elasticsearch.index_prefix'];

        foreach ($requiredParameters as $requiredParameter) {
            if ('placeholder_value' === \Pimcore::getKernel()->getContainer()->getParameter($requiredParameter)) {
                throw new Pimcore\Model\Element\ValidationException(sprintf('Required parameter "%s" is not set. Overwrite this parameter in your yml config. Read docs for more information', $requiredParameter));
            }
        }

        /** @var string[] $existingClassDefinitions */
        $existingClassDefinitions = ['portaluser' => 'PortalUser', 'portalusergroup' => 'PortalUserGroup'];

        foreach ($existingClassDefinitions as $existingClassDefinitionId => $existingClassDefinitionName) {
            $classDefinition = ClassDefinition::getByName($existingClassDefinitionName);
            //check if there is a existing ClassDefinition with the same reserved name and a different id (in bundle uninstall classDefinitions are not deleted)
            if ($classDefinition && $classDefinition->getId() !== $existingClassDefinitionId) {
                throw new Pimcore\Model\Element\ValidationException(sprintf('ClassDefinition "%s" already exists. "PortalUser" and "PortalUserGroup" are reserved PortalEngine classDefinition names. Read docs for more information', $classDefinition));
            }
        }
    }

    protected function afterInstall()
    {
        // Clear cache before starting IndexUpdateService
        Pimcore\Cache::clearAll();

        //ES index setup
        Pimcore::getKernel()
            ->getContainer()
            ->get(IndexUpdateService::class)
            ->setReCreateIndex(true)
            ->updateAll();
    }

    /**
     * @return $this
     */
    protected function installClasses()
    {
        $sourcePath = __DIR__.'/../install/class_source';

        //install PortalUserGroup first, because PortalUser has a relation to this entity
        $this->installClass('PortalUserGroup', $sourcePath.'/class_PortalUserGroup_export.json');
        $this->installClass('PortalUser', $sourcePath.'/class_PortalUser_export.json');

        return $this;
    }

    /**
     * @param Schema $schema
     *
     * @return $this
     */
    protected function installTables()
    {
        $db = Pimcore\Db::get();
        $currentSchema = $db->getSchemaManager()->createSchema();
        $schema = $db->getSchemaManager()->createSchema();

        $this->tableInstaller->installTables($schema);

        $changes = $currentSchema->getMigrateToSql($schema, $db->getDatabasePlatform());
        if (!empty($changes)) {
            $db->exec(implode(';', $changes));
        }

        return $this;
    }

    /**
     * @param string $classname
     * @param string $filepath
     *
     * @return $this
     */
    protected function installClass($classname, $filepath)
    {
        $class = ClassDefinition::getByName($classname);
        if (!$class) {
            $class = new ClassDefinition();
            $class->setName($classname);
            $class->setGroup('PortalEngine');

            $json = file_get_contents($filepath);

            $success = Service::importClassDefinitionFromJson($class, $json);
            if (!$success) {
                Logger::err("Could not import $classname Class.");
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function installThumbnails()
    {
        $thumbnailConfigElementTeaser = new \Pimcore\Model\Asset\Image\Thumbnail\Config();
        $thumbnailConfigElementTeaser->setName(Pimcore\Bundle\PortalEngineBundle\Enum\ImageThumbnails::ELEMENT_TEASER);
        $thumbnailConfigElementTeaser->setGroup('Portal Engine');
        $thumbnailConfigElementTeaser->addItem('contain', [
            'width' => 294,
            'height' => 165,
            'forceResize' => false
        ]);
        $thumbnailConfigElementTeaser->save();

        $thumbnailConfigElementDetail = new \Pimcore\Model\Asset\Image\Thumbnail\Config();
        $thumbnailConfigElementDetail->setName(Pimcore\Bundle\PortalEngineBundle\Enum\ImageThumbnails::ELEMENT_DETAIL);
        $thumbnailConfigElementDetail->setGroup('Portal Engine');
        $thumbnailConfigElementDetail->addItem('scaleByWidth', [
            'width' => 1024
        ]);
        $thumbnailConfigElementDetail->addItem('scaleByHeight', [
            'height' => 768
        ]);
        $thumbnailConfigElementDetail->save();

        $thumbnailConfigDetailPage = new \Pimcore\Model\Asset\Image\Thumbnail\Config();
        $thumbnailConfigDetailPage->setName(Pimcore\Bundle\PortalEngineBundle\Enum\ImageThumbnails::DETAIL_PAGE);
        $thumbnailConfigDetailPage->setGroup('Portal Engine');
        $thumbnailConfigDetailPage->addItem('contain', [
            'width' => 600,
            'height' => 400,
            'forceResize' => false
        ]);
        $thumbnailConfigDetailPage->save();

        $thumbnailConfigFooterLogo = new \Pimcore\Model\Asset\Image\Thumbnail\Config();
        $thumbnailConfigFooterLogo->setName(Pimcore\Bundle\PortalEngineBundle\Enum\ImageThumbnails::LOGO);
        $thumbnailConfigFooterLogo->setGroup('Portal Engine');
        $thumbnailConfigFooterLogo->addItem('scaleByWidth', [
            'width' => 200,
            'forceResize' => false
        ]);
        $thumbnailConfigFooterLogo->save();

        $coverTransformations = [
            'portal-engine_teaser-1by1' => [
                'width' => 223,
                'height' => 236
            ],
            'portal-engine_teaser-2by1' => [
                'width' => 471,
                'height' => 235
            ],
            'portal-engine_teaser-cta' => [
                'width' => 730,
                'height' => 408
            ],
        ];

        foreach ($coverTransformations as $name => $options) {
            if (Config::getByName($name)) {
                continue;
            }

            $options['forceResize'] = $options['forceResize'] ?? true;
            $options['positioning'] = $options['positioning'] ?? 'center';
            $thumbnailConfig = new Config();
            $thumbnailConfig->setName($name);
            $thumbnailConfig->setGroup('Portal Engine');
            $thumbnailConfig->addItem('cover', $options);
            $thumbnailConfig->save();
        }

        $name = 'portal-engine_email-logo';

        if (!Config::getByName($name)) {
            $options['forceResize'] = true;
            $thumbnailConfig = new Config();
            $thumbnailConfig->setName($name);
            $thumbnailConfig->setFormat('png');
            $thumbnailConfig->setGroup('Portal Engine');
            $thumbnailConfig->addItem('scaleByHeight', [
                'height' => 42
            ]);
            $thumbnailConfig->setRasterizeSVG(true);
            $thumbnailConfig->save();
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function installDocTypes()
    {
        $docTypeConfigs = [
            [
                'name' => 'Portal Engine - Portal Page',
                'controller' => ControllerReference::PORTAL_PAGE,
                'type' => 'page',
            ],
            [
                'name' => 'Portal Engine - Data Object Data Pool',
                'controller' => ControllerReference::DATA_POOL_DATA_OBJECTS_LIST,
                'type' => 'page',
            ],
            [
                'name' => 'Portal Engine - Asset Data Pool',
                'controller' => ControllerReference::DATA_POOL_ASSETS_LIST,
                'type' => 'page',
            ],
            [
                'name' => 'Portal Engine - Content Page',
                'controller' => ControllerReference::PORTAL_CONTENT_PAGE,
                'type' => 'page',
            ],
            [
                'name' => 'Portal Engine - Footer Snippet',
                'controller' => ControllerReference::SNIPPET_FOOTER,
                'type' => 'snippet',
            ],
            [
                'name' => 'Portal Engine - Data Pool Language Variant',
                'controller' => ControllerReference::LANGUAGE_VARIANT_DATA_POOL,
                'type' => 'page',
            ],
        ];

        foreach ($docTypeConfigs as $docTypeConfig) {
            $exists = false;
            $docTypes = new Pimcore\Model\Document\DocType\Listing;
            /**
             * @var Pimcore\Model\Document\DocType $docType
             */
            foreach ($docTypes->load() as $docType) {
                if ($docType->getController() === $docTypeConfig['controller']) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                (new Pimcore\Model\Document\DocType())
                    ->setName($docTypeConfig['name'])
                    ->setGroup('Portal Engine')
                    ->setController($docTypeConfig['controller'])
                    ->setType($docTypeConfig['type'])
                    ->save();
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function installPredefinedProperties()
    {
        $predefinedPropertyConfigs = [
            [
                'name' => 'Portal Engine - Navigation Root',
                'key' => Routing::NAVIGATION_ROOT_PROPERTY,
                'type' => 'document',
                'contentType' => 'document',
            ],
        ];

        foreach ($predefinedPropertyConfigs as $predefinedPropertyConfig) {
            $exists = false;
            $predefinedProperties = new Pimcore\Model\Property\Predefined\Listing;

            /**
             * @var Pimcore\Model\Property\Predefined $predefinedProperty
             */
            foreach ($predefinedProperties->load() as $predefinedProperty) {
                if ($predefinedProperty->getKey() === $predefinedPropertyConfig['key']
                    && $predefinedProperty->getType() === $predefinedPropertyConfig['type']
                    && $predefinedProperty->getCtype() === $predefinedPropertyConfig['contentType']
                ) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                (new Pimcore\Model\Property\Predefined())
                    ->setName($predefinedPropertyConfig['name'])
                    ->setKey($predefinedPropertyConfig['key'])
                    ->setType($predefinedPropertyConfig['type'])
                    ->setCtype($predefinedPropertyConfig['contentType'])
                    ->setInheritable(true)
                    ->save();
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function installSharedTranslations()
    {
        /** @var string[] $sharedTranslationFilePaths */
        $sharedTranslationFilePaths = [
            __DIR__ . '/../install/shared_translation_source/de.csv' => 'de',
            __DIR__ . '/../install/shared_translation_source/en.csv' => 'en'
        ];

        foreach ($sharedTranslationFilePaths as $sharedTranslationFilePath => $language) {
            try {
                if (!in_array($language, Pimcore\Tool::getValidLanguages())) {
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
                    $sharedTranslationKey = $rowContent[0];
                    /** @var string $sharedTranslationValue */
                    $sharedTranslationValue = htmlspecialchars_decode(str_replace("\r\n", '', ($rowContent[1] ?? '')));

                    $websiteTranslation = Pimcore\Model\Translation::getByKey($sharedTranslationKey, Pimcore\Model\Translation::DOMAIN_DEFAULT, true);
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

    /**
     * @return $this
     */
    protected function installAdminTranslations()
    {
        /** @var string[] $adminTranslationFilePaths */
        $adminTranslationFilePaths = [
            __DIR__.'/../install/admin_translation_source/de.csv' => 'de',
            __DIR__.'/../install/admin_translation_source/en.csv' => 'en',
        ];

        foreach ($adminTranslationFilePaths as $adminTranslationFilePath => $language) {
            try {
                /** @var array|false $translations */
                $translations = file($adminTranslationFilePath);

                if (!is_array($translations)) {
                    throw new \Exception('file has no valid array content');
                }

                // remove first array entry, its just the column title, e.g. "key;en"
                array_shift($translations);

                /** @var string $translation */
                foreach ($translations as $translation) {
                    /** @var array $rowContent */
                    $rowContent = explode(';', $translation, 2);

                    /** @var string $translationKey */
                    $translationKey = $rowContent[0];
                    /** @var string $sharedTranslationValue */
                    $sharedTranslationValue = htmlspecialchars_decode(str_replace("\r\n", '', ($rowContent[1] ?? '')));

                    $adminTranslation = Pimcore\Model\Translation::getByKey($translationKey, 'admin', true);
                    if (!$adminTranslation->getTranslation($language)) {
                        $adminTranslation->addTranslation($language, $sharedTranslationValue);
                        $adminTranslation->setModificationDate(time());
                        $adminTranslation->save();
                    }
                }

                $this->getOutput()->write(sprintf('AdminTranslation csv file "%s" successfully installed', $adminTranslationFilePath));
            } catch (\Exception $e) {
                $this->getOutput()->write(sprintf('Install for adminTranslations csv file %s skipped. Error: %s', $adminTranslationFilePath, $e->getMessage()));
            }
        }

        return $this;
    }

    protected function installBackendPermissions()
    {
        foreach ([BackendPermission::COLLECTION_ACCESS, BackendPermission::WIZARD] as $permission) {
            $definition = Definition::getByKey($permission);
            if (empty($definition)) {
                $definition = new Definition([
                    'key' => $permission,
                    'category' => BackendPermission::PERMISSION_CATEGORY
                ]);
                $definition->save();
            }
        }
    }

    public function uninstall()
    {
        // uninstall would result in data loss and is not deemed necessary at the moment
        parent::uninstall();
    }

    /**
     * @return bool
     */
    public function needsReloadAfterInstall()
    {
        return true;
    }

    /**
     * @param Installer\TableInstaller $tableInstaller
     * @required
     */
    public function setTableInstaller(Installer\TableInstaller $tableInstaller)
    {
        $this->tableInstaller = $tableInstaller;
    }

    /**
     * @required
     *
     * @param IndexUpdateService $indexUpdateService
     */
    public function setIndexUpdateService(IndexUpdateService $indexUpdateService)
    {
        $this->indexUpdatService = $indexUpdateService;
    }
}
