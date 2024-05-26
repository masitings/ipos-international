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

use Pimcore\Bundle\PortalEngineBundle\Service\Wizard\WizardService;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WizardCommand extends AbstractCommand
{
    const ARGUMENT_TMP_STORE_KEY = 'tmp-store-key';

    /**
     * @var WizardService
     */
    protected $wizardService;

    protected function configure()
    {
        $this->setName('portal-engine:wizard')
            ->setDescription('Used within the Pimcore backend to create portals via wizard.')
            ->addArgument(self::ARGUMENT_TMP_STORE_KEY, InputArgument::OPTIONAL, 'TmpStore key prepared by WizardService', null);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tmpStoreKey = $input->getArgument(self::ARGUMENT_TMP_STORE_KEY);
        $this->wizardService->createPortal($tmpStoreKey);

        return 0;
    }

    /**
     * @param WizardService $wizardService
     * @required
     */
    public function setWizardService(WizardService $wizardService): void
    {
        $this->wizardService = $wizardService;
    }
}
