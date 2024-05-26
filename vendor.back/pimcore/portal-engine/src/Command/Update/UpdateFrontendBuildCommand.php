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

use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\FrontendBuildService;
use Pimcore\Console\AbstractCommand;
use Pimcore\Tool\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class UpdateFrontendBuildCommand extends AbstractCommand
{
    const OPTION_CORE_BUNDLE_DEVELOPMENT = 'core-bundle-development';
    const OPTION_SKIP_PACKAGE_JSON = 'skip-package-json';

    /**
     * @var FrontendBuildService
     */
    protected $frontendBuildService;

    protected function configure()
    {
        $this->setName('portal-engine:update:frontend-build')
            ->setDescription('Setup the webpack build process.')
            ->addOption(
                self::OPTION_CORE_BUNDLE_DEVELOPMENT,
                null,
                InputOption::VALUE_NONE,
                'Use this option if you would like to develop for the core of the portal engine bundle'
            )
            ->addOption(
                self::OPTION_SKIP_PACKAGE_JSON,
                null,
                InputOption::VALUE_NONE,
                'Skip generating/updating package.json'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->frontendBuildService->updatePortalsJson();

        $output->writeln('updated customized frontend builds json');
        $this->frontendBuildService->updateCustomizedFrontendBuildsJson();

        $skipPackageJson = $input->getOption(self::OPTION_SKIP_PACKAGE_JSON);

        if ($skipPackageJson) {
            $output->writeln('<info>Finished, package.json was not updated.</info>');

            return 0;
        }

        $npxExecutable = Console::getExecutable('npx');
        if (!$npxExecutable) {
            $output->writeln('<error>npx executable not found - please install it</error>');

            return 1;
        }

        $npmExecutable = Console::getExecutable('npm');
        if (!$npmExecutable) {
            $output->writeln('<error>npm executable not found - please install it or make it available </error>');

            return 1;
        }

        $process = new Process([$npmExecutable, '-version']);
        $npmVersion = $process->mustRun()->getOutput();

        if (empty($npmVersion) || intval(explode('.', $npmVersion)[0]) < 6) {
            $output->writeln('<error>please upgrade to at least npm version 6</error>');

            return 1;
        }

        $coreBundleDevelopment = $input->getOption(self::OPTION_CORE_BUNDLE_DEVELOPMENT);
        $this->frontendBuildService->writePackageJson($output, $npmExecutable, $npxExecutable, $coreBundleDevelopment);

        $output->writeln(sprintf('<info>Finished. Please run "npm install" in "%s" to install all dependencies.</info>', PIMCORE_PROJECT_ROOT));

        return 0;
    }

    /**
     * @param FrontendBuildService $frontendBuildService
     * @required
     */
    public function setFrontendBuildService(FrontendBuildService $frontendBuildService): void
    {
        $this->frontendBuildService = $frontendBuildService;
    }
}
