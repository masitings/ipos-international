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

namespace Pimcore\Bundle\PortalEngineBundle\Command;

use Pimcore\Bundle\PortalEngineBundle\Service\Download\SizeEstimation\AsyncSizeEstimationService;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SizeEstimationCommand extends AbstractCommand
{
    const ARGUMENT_TMP_STORE_KEY = 'tmp-store-key';

    /**
     * @var AsyncSizeEstimationService
     */
    protected $asyncSizeEstimationService;

    protected function configure()
    {
        $this->setName('portal-engine:size-estimation')
            ->setDescription('Used to estimate the size of a download.')
            ->addArgument(self::ARGUMENT_TMP_STORE_KEY, InputArgument::OPTIONAL, 'TmpStore key prepared by AsyncSizeEstimationService', null);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tmpStoreKey = $input->getArgument(self::ARGUMENT_TMP_STORE_KEY);

        $this->asyncSizeEstimationService->executeEstimate($tmpStoreKey);

        return 0;
    }

    /**
     * @param AsyncSizeEstimationService $asyncSizeEstimationService
     *
     * @return SizeEstimationCommand
     * @required
     */
    public function setAsyncSizeEstimationService(AsyncSizeEstimationService $asyncSizeEstimationService)
    {
        $this->asyncSizeEstimationService = $asyncSizeEstimationService;
    }
}
