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

namespace Pimcore\Bundle\StatisticsExplorerBundle;

use Pimcore\Bundle\EnterpriseSubscriptionToolsBundle\Bundle\EnterpriseBundleInterface;
use Pimcore\Bundle\EnterpriseSubscriptionToolsBundle\PimcoreEnterpriseSubscriptionToolsBundle;
use Pimcore\Bundle\StatisticsExplorerBundle\DependencyInjection\Compiler\DataSourcePass;
use Pimcore\Bundle\StatisticsExplorerBundle\DependencyInjection\Compiler\UserProviderContextPass;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

class PimcoreStatisticsExplorerBundle extends AbstractPimcoreBundle implements DependentBundleInterface, EnterpriseBundleInterface
{
    use PackageVersionTrait;

    const TRANSLATION_PREFIX = 'statistics_container.';

    public function getJsPaths()
    {
        return [];
    }

    /**
     * @param BundleCollection $collection
     */
    public static function registerDependentBundles(BundleCollection $collection)
    {
        $collection->addBundle(new WebpackEncoreBundle());
        $collection->addBundle(new PimcoreEnterpriseSubscriptionToolsBundle());
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DataSourcePass());
        $container->addCompilerPass(new UserProviderContextPass());
    }

    /**
     * @return Installer
     */
    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }

    public function getBundleLicenseId(): string
    {
        return 'PSE';
    }

    protected function getComposerPackageName(): string
    {
        return 'pimcore/statistics-explorer';
    }
}
