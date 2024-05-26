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

namespace Pimcore\Bundle\PortalEngineBundle\Command\Update;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class IndexRecreateCommand
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Command\Update
 */
class IndexRecreateCommand extends IndexUpdateCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('portal-engine:update:index-recreate')
            ->setDescription('Creates index/mapping for all classDefinitions/asset after deleting them. Adds there elements to index queue');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this
            ->indexUpdateService
            ->setReCreateIndex(true);

        return parent::execute($input, $output);
    }
}
