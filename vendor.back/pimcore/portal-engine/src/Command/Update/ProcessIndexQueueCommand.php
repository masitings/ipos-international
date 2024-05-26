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
use Pimcore\Console\AbstractCommand;
use Pimcore\Console\Traits\Parallelization;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Usage: ./bin/console portal-engine:update:process-index-queue --processes 2
 *
 * Class ProcessIndexQueueCommand
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Command\Update
 */
class ProcessIndexQueueCommand extends AbstractCommand
{
    use Parallelization;

    /** @var string */
    protected static $defaultName = 'portal-engine:update:process-index-queue';

    /** @var IndexQueueService */
    protected $indexQueueService;

    /**
     * ProcessIndexQueueCommand constructor.
     *
     * @param IndexQueueService $indexQueueService
     */
    public function __construct(IndexQueueService $indexQueueService)
    {
        $this->indexQueueService = $indexQueueService;

        parent::__construct();
    }

    protected function configure()
    {
        self::configureParallelization($this);

        $this->setDescription('Process all elements in index queue');
    }

    /**
     * Fetches the items that should be processed.
     *
     * Typically, you will fetch all the items of the database objects that
     * you want to process here. These will be passed to runSingleCommand().
     *
     * This method is called exactly once in the master process.
     *
     * @param InputInterface $input The console input
     *
     * @return string[] The items to process
     */
    protected function fetchItems(InputInterface $input): array
    {
        /** @var array $unhandledIndexQueueEntries */
        $unhandledIndexQueueEntries = $this->indexQueueService->getUnhandledIndexQueueEntries();
        /** @var array $serializedUnhandledIndexQueueEntries */
        $serializedUnhandledIndexQueueEntries = [];

        foreach ($unhandledIndexQueueEntries as $unhandledIndexQueueEntry) {
            $serializedUnhandledIndexQueueEntries[] = serialize($unhandledIndexQueueEntry);
        }

        return $serializedUnhandledIndexQueueEntries;
    }

    /**
     * Processes an item in the child process.
     *
     * @param string $item
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function runSingleCommand(string $item, InputInterface $input, OutputInterface $output): void
    {
        /** @var array $unhandledIndexQueueEntry */
        $unhandledIndexQueueEntry = unserialize($item);

        $this->indexQueueService->handleIndexQueueEntry($unhandledIndexQueueEntry);
    }

    /**
     * Returns the name of each item in lowercase letters.
     *
     * For example, this method could return "contact" if the count is one and
     * "contacts" otherwise.
     *
     * @param int $count The number of items
     *
     * @return string The name of the item in the correct plurality
     */
    protected function getItemName(int $count): string
    {
        return 1 === $count ? 'element' : 'elements';
    }
}
