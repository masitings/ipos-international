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

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Collections;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 *
 * @deprecated will be removed in v0.2. Use it to transfer existing data from the plugin_assetmetdata_collections table
 *                      to custom settings of assets.
 */
class MigrateToCustomSettingsCommand extends AbstractCommand
{
    const NAME = 'asset-metadata-class-definitions:migrate-to-custom-settings';

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
        $this->setDescription('Use this command to migrate the `plugin_assetmetdata_collections` table to the internal asset customsetting property.');
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
        $assets = [];
        $db = Db::get();
        $result = $db->fetchAll('select * from plugin_assetmetdata_collections');
        foreach ($result as $item) {
            $cid = $item['cid'];
            if (!isset($assets[$cid])) {
                $assets[$cid] = [];
            }
            $assets[$cid][] = $item['name'];
        }

        foreach ($assets as $cid => $groups) {
            $asset = Asset::getById($cid);
            if ($asset) {
                $output->writeln('updating asset ' . $cid);
                $asset->setCustomSetting('plugin_assetmetdata_collections', $groups);
                $asset->save();
            } else {
                $output->writeln('asset ' . $cid . ' not found');
            }
        }
    }
}
