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

namespace Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration;

use Pimcore\AssetMetadataClassDefinitionsBundle\Event\AssetMetadataConfigurationEvents;
use Pimcore\AssetMetadataClassDefinitionsBundle\Event\Model\Asset\ConfigurationEvent;
use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration;
use Pimcore\Config;
use Pimcore\File;
use Pimcore\Model\Dao\AbstractDao;

/**
 * @property Configuration $model
 */
class Dao extends AbstractDao
{
    public const ROOT_PATH = '/';

    /**
     * path to the configuration file
     */
    public const CONFIG_FILE = 'assetmetadata-classdefinitions.php';

    /**
     * @var null|array
     */
    private static $_config = null;

    /**
     * get a configuration by name.
     *
     * @param string $name
     *
     * @return Configuration|null
     */
    public static function getByName($name): ?Configuration
    {
        $list = self::getList();

        /** @var Configuration $item */
        foreach ($list as $item) {
            if ($item->getName() === $name) {
                $filename = self::getConfigurationDirectory() . '/definition_' . $name . '.php';
                if (file_exists($filename)) {
                    $definition = include($filename);
                    $item->setTitle($definition->title);
                    $item->setPrefix($definition->prefix);
                    $item->setLayoutDefinitions($definition->layoutDefinitions);
                }

                return $item;
            }
        }

        return null;
    }

    /**
     * get a configuration by prefix.
     *
     * @param string $prefix
     *
     * @return Configuration|null
     */
    public static function getByPrefix($prefix): ?Configuration
    {
        $list = self::getList(true);

        /** @var Configuration $item */
        foreach ($list as $item) {
            if ($item->getPrefix() === $prefix) {
                $filename = self::getConfigurationDirectory() . '/definition_' . $item->getName() . '.php';
                if (file_exists($filename)) {
                    $definition = include($filename);
                    $item->setLayoutDefinitions($definition->layoutDefinitions);
                }

                return $item;
            }
        }

        return null;
    }

    /**
     * get the list of configurations.
     *
     * @param bool $includeDefinition
     *
     * @return Configuration[]
     */
    public static function getList($includeDefinition = false): array
    {
        $config = &self::getConfig();
        $configurations = [];

        foreach ($config['list'] ?? [] as $item) {
            $c = new Configuration($item['name'], $item['title']);
            $c->setIcon($item['icon'] ?? null);
            if ($includeDefinition) {
                $filename = self::getConfigurationDirectory() . '/definition_' . $c->getName() . '.php';
                if (file_exists($filename)) {
                    $definition = include($filename);
                    $c->setTitle($definition->title);
                    $c->setPrefix($definition->prefix);
                    $c->setLayoutDefinitions($definition->layoutDefinitions);
                }
            }

            $configurations[$c->getName()] = $c;
        }

        return $configurations;
    }

    /**
     * get the whole configuration file content.
     *
     * @return array|mixed|null
     */
    private static function &getConfig()
    {
        if (self::$_config) {
            return self::$_config;
        }

        $file = Config::locateConfigFile(self::CONFIG_FILE);
        $config = null;

        if (!file_exists($file)) {
            $config = self::defaultConfig();

            self::writeConfig($config);
        } else {
            $config = include($file);
        }

        self::$_config = $config;

        return self::$_config;
    }

    /**
     * get a default configuration.
     *
     * @return array
     */
    private static function defaultConfig(): array
    {
        return [
            'list' => []
        ];
    }

    /**
     * write the configuration file.
     *
     * @param $config
     */
    private static function writeConfig($config): void
    {
        File::putPhpFile(Config::locateConfigFile(self::CONFIG_FILE), to_php_data_file_format($config));

        // reset singleton var to force get updated config in current request when calling getConfig()
        self::$_config = null;
    }

    /**
     * @param bool $create
     *
     * @return string
     */
    public static function getConfigurationDirectory($create = false)
    {
        $dir = PIMCORE_PRIVATE_VAR . '/config/assetmetadata-definitions';

        if ($create) {
            if (!file_exists($dir)) {
                File::mkdir($dir);
            }
        }

        return $dir;
    }

    /**
     * save a configuration.
     */
    public function save(): void
    {
        // create dir on the fl
        $this->getConfigurationDirectory(true);

        /** @var string $definitionFile */
        $definitionFile = $this->getDefinitionFile();
        /** @var bool $isUpdate */
        $isUpdate = file_exists($definitionFile);

        if (!$isUpdate) {
            \Pimcore::getEventDispatcher()->dispatch(
                new ConfigurationEvent($this->model),
                AssetMetadataConfigurationEvents::PRE_ADD
            );
        } else {
            \Pimcore::getEventDispatcher()->dispatch(
                new ConfigurationEvent($this->model),
                AssetMetadataConfigurationEvents::PRE_UPDATE
            );
        }

        $name = $this->model->getName();
        $config = &self::getConfig();

        $clone = clone $this->model;

        $config['list'][$name] = [
            'name' => $this->model->name,
            'title' => $this->model->title,
            'icon' => $this->model->icon
        ];

        $export = var_export($clone, true);

        $data = '<?php ';
        $data .= "\n\n";

        $data .= "\nreturn " . $export . ";\n";
        file_put_contents($definitionFile, $data);

        // necessary for include in getList function (when calling getList after save in current request), else include will serve the cached data without/old layoutDefinitions
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($definitionFile, true);
        }

        self::writeConfig($config);

        if ($isUpdate) {
            \Pimcore::getEventDispatcher()->dispatch(
                new ConfigurationEvent($this->model),
                AssetMetadataConfigurationEvents::POST_UPDATE
            );
        } else {
            \Pimcore::getEventDispatcher()->dispatch(
                new ConfigurationEvent($this->model),
                AssetMetadataConfigurationEvents::POST_ADD
            );
        }
    }

    /**
     * @return string
     */
    public function getDefinitionFile()
    {
        $filename = self::getConfigurationDirectory() . '/definition_' . $this->model->getName() . '.php';

        return $filename;
    }

    /**
     * delete a configuration.
     */
    public function delete(): void
    {
        \Pimcore::getEventDispatcher()->dispatch(
            new ConfigurationEvent($this->model),
            AssetMetadataConfigurationEvents::PRE_DELETE
        );

        $name = $this->model->getName();
        $config = &self::getConfig();

        unset($config['list'][$name]);

        self::writeConfig($config);

        $definitionFile = $this->getDefinitionFile();
        if (file_exists($definitionFile)) {
            unlink($definitionFile);
        }

        \Pimcore::getEventDispatcher()->dispatch(
            new ConfigurationEvent($this->model),
            AssetMetadataConfigurationEvents::POST_DELETE
        );
    }
}
