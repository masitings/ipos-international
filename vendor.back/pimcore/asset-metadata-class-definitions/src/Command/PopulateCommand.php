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

namespace Pimcore\AssetMetadataClassDefinitionsBundle\Command;

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration\Dao;
use Pimcore\AssetMetadataClassDefinitionsBundle\Service;
use Pimcore\Cache;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateCommand extends AbstractCommand
{
    const NAME = 'asset-metadata-class-definitions:populate';

    public function __construct()
    {
        parent::__construct(static::NAME);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        parent::configure();

        $this->setName(static::NAME);
        $this->setDescription('Use this command to populate the asset `custom_settings` table based on your existing data.
               This table contains a list of active class definitions for a specific asset.
               Approach: E.g. there is an existing metadata key `license.something` it will mark the definition with prefix `license` as active.
        ');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $map = [];
        $localizedMap = [];

        $list = Dao::getList(true);
        foreach ($list as $configuration) {
            $definitions = [];
            $localizedDefinitions = [];
            $layoutDefinitions = $configuration->getLayoutDefinitions();
            $name = $configuration->getName();
            Service::extractDataDefinitions($layoutDefinitions, false, $definitions, $localizedDefinitions);

            foreach ($definitions as $key => $def) {
                if (!array_key_exists($key, $map)) {
                    $map[$key] = [];
                }
                $map[$key][$name] = $name;
            }

            foreach ($localizedDefinitions as $key => $def) {
                if (!array_key_exists($key, $localizedMap)) {
                    $localizedMap[$key] = [];
                }
                $localizedMap[$key][$name] = $name;
            }
        }

        $collections = [];
        $db = Db::get();
        $metadata = $db->fetchAll('select * from assets_metadata');
        foreach ($metadata as $metadataItem) {
            $cid = $metadataItem['cid'];
            $name = $metadataItem['name'];
            $language = $metadataItem['language'];
            if (!array_key_exists($cid, $collections)) {
                $collections[$cid] = [];
            }

            if ($language) {
                if (isset($localizedMap[$name])) {
                    $collections[$cid] = array_merge($collections[$cid], $localizedMap[$name]);
                }
            } else {
                if (isset($map[$name])) {
                    $collections[$cid] = array_merge($collections[$cid], $map[$name]);
                }
            }
        }

        foreach ($collections as $cid => $collection) {
            $asset = Asset::getById($cid);
            $asset->setCustomSetting('plugin_assetmetdata_collections', $collection);
            $asset->save();
        }

        Cache::clearAll();
        $output->writeln('done');

        return 0;
    }
}
