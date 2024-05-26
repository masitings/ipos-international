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

namespace Pimcore\Bundle\PortalEngineBundle;

use FrontendPermissionToolkitBundle\FrontendPermissionToolkitBundle;
use League\FlysystemBundle\FlysystemBundle;
use Pimcore\Bundle\EnterpriseSubscriptionToolsBundle\Bundle\EnterpriseBundleInterface;
use Pimcore\Bundle\EnterpriseSubscriptionToolsBundle\PimcoreEnterpriseSubscriptionToolsBundle;
use Pimcore\Bundle\PortalEngineBundle\DependencyInjection\Compiler\BatchTaskProcessNotificationActionPass;
use Pimcore\Bundle\PortalEngineBundle\DependencyInjection\Compiler\DataPoolPass;
use Pimcore\Bundle\PortalEngineBundle\DependencyInjection\Compiler\DirectEditCompilerPass;
use Pimcore\Bundle\PortalEngineBundle\DependencyInjection\Compiler\DownloadFormatPass;
use Pimcore\Bundle\PortalEngineBundle\DependencyInjection\Compiler\DownloadGeneratorPass;
use Pimcore\Bundle\PortalEngineBundle\DependencyInjection\Compiler\DownloadProviderPass;
use Pimcore\Bundle\PortalEngineBundle\DependencyInjection\Compiler\PreConditionPass;
use Pimcore\Bundle\PortalEngineBundle\DependencyInjection\Compiler\RestApiFieldDefinitionPass;
use Pimcore\Bundle\PortalEngineBundle\DependencyInjection\Compiler\SearchIndexFieldDefinitionPass;
use Pimcore\Bundle\StatisticsExplorerBundle\PimcoreStatisticsExplorerBundle;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

/**
 * Class PimcorePortalEngineBundle
 *
 * @package Pimcore\Bundle\PortalEngineBundle
 */
class PimcorePortalEngineBundle extends AbstractPimcoreBundle implements DependentBundleInterface, EnterpriseBundleInterface
{
    use PackageVersionTrait;

    /**
     * @var array
     */
    protected static $config;

    /**
     * @return array|\Pimcore\Routing\RouteReferenceInterface[]|string[]
     */
    public function getJsPaths()
    {
        return [
            '/bundles/pimcoreportalengine/js/pimcore/startup.js',
            '/bundles/pimcoreportalengine/js/pimcore/collections/tree.js',
            '/bundles/pimcoreportalengine/js/pimcore/collections/list.js',
            '/bundles/pimcoreportalengine/js/pimcore/collections/openTreeStorage.js',
            '/bundles/pimcoreportalengine/js/pimcore/wizard/helpers.js',
            '/bundles/pimcoreportalengine/js/pimcore/wizard/wizard.js',
            '/bundles/pimcoreportalengine/js/pimcore/wizard/data-pool/asset.js',
            '/bundles/pimcoreportalengine/js/pimcore/wizard/data-pool/data-object.js'
        ];
    }

    public function getCssPaths()
    {
        return [
            '/bundles/pimcoreportalengine/css/admin.css'
        ];
    }

    public function getEditmodeCssPaths()
    {
        return [
            '/bundles/pimcoreportalengine/css/editmode.css'
        ];
    }

    public function getEditmodeJsPaths()
    {
        return [
            '/bundles/pimcoreportalengine/js/editmode.js'
        ];
    }

    /**
     * @param BundleCollection $collection
     */
    public static function registerDependentBundles(BundleCollection $collection)
    {
        $collection->addBundle(new WebpackEncoreBundle());
        $collection->addBundle(new FrontendPermissionToolkitBundle());
        $collection->addBundle(new PimcoreStatisticsExplorerBundle());
        $collection->addBundle(new PimcoreEnterpriseSubscriptionToolsBundle());
        $collection->addBundle(new FlysystemBundle());
    }

    /**
     * @return Installer
     */
    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new SearchIndexFieldDefinitionPass())
            ->addCompilerPass(new RestApiFieldDefinitionPass())
            ->addCompilerPass(new PreConditionPass())
            ->addCompilerPass(new DownloadFormatPass())
            ->addCompilerPass(new DataPoolPass())
            ->addCompilerPass(new DownloadProviderPass())
            ->addCompilerPass(new DownloadGeneratorPass())
            ->addCompilerPass(new BatchTaskProcessNotificationActionPass())
            ->addCompilerPass(new DirectEditCompilerPass());
    }

    public function getBundleLicenseId(): string
    {
        return 'PPE';
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName(): string
    {
        return 'pimcore/portal-engine';
    }
}
