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

use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\IndexQueueService;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\IndexUpdateService;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class IndexUpdateCommand
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Command\Update
 */
class IndexUpdateCommand extends AbstractCommand
{
    use LockableTrait;

    /** @var IndexUpdateService */
    protected $indexUpdateService;
    /** @var IndexQueueService */
    protected $indexQueueService;

    /**
     * IndexUpdateCommand constructor.
     *
     * @param IndexUpdateService $indexUpdateService
     * @param IndexQueueService $indexQueueService
     */
    public function __construct(IndexUpdateService $indexUpdateService, IndexQueueService $indexQueueService)
    {
        $this->indexUpdateService = $indexUpdateService;
        $this->indexQueueService = $indexQueueService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('portal-engine:update:index')
            ->addOption('class-definition-id', 'cid', InputOption::VALUE_OPTIONAL, 'Update mapping and data for specific data object classDefinition', null)
            ->addArgument('update-asset-index', InputArgument::OPTIONAL, 'Update mapping and data for asset index', null)
            ->setDescription('Updates index/mapping for all classDefinitions/asset without deleting them. Adds there elements to index queue.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            throw new \Exception('The command is already running in another process.');
        }

        /** @var bool $updateAll */
        $updateAll = true;

        $output->writeln('start');

        /** @var string|null $classDefinitionId */
        $classDefinitionId = $input->getOption('class-definition-id');

        if ($classDefinitionId) {
            $updateAll = false;

            try {
                /** @var ClassDefinition|null $classDefinition */
                $classDefinition = ClassDefinition::getById($classDefinitionId);
                if (!$classDefinition) {
                    throw new \Exception(sprintf('ClassDefinition with id %s not found', $classDefinitionId));
                }

                $this->output->writeln(sprintf('Update index and indices for ClassDefinition with id %s', $classDefinitionId));

                $this
                    ->indexUpdateService
                    ->updateClassDefinition($classDefinition);
            } catch (\Exception $e) {
                $this->output->writeln($e->getMessage());
            }
        }

        /** @var string|null $updateAsset */
        $updateAsset = $input->getArgument('update-asset-index');

        if ($updateAsset) {
            $updateAll = false;

            try {
                $this->output->writeln('Update indices for asset');

                $this
                    ->indexUpdateService
                    ->updateAssets();
            } catch (\Exception $e) {
                $this->output->writeln($e->getMessage());
            }
        }

        if ($updateAll) {
            $this->output->writeln('Update all mappings and indices for objects/assets');

            $this
                ->indexUpdateService
                ->updateAll();
        }

        $this->release();

        $output->writeln('finished');

        return 0;
    }
}
