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

namespace Pimcore\AssetMetadataClassDefinitionsBundle;

use Pimcore\Bundle\EnterpriseSubscriptionToolsBundle\Bundle\EnterpriseBundleInterface;
use Pimcore\Bundle\EnterpriseSubscriptionToolsBundle\PimcoreEnterpriseSubscriptionToolsBundle;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;

class PimcoreAssetMetadataClassDefinitionsBundle extends AbstractPimcoreBundle implements EnterpriseBundleInterface, DependentBundleInterface
{
    use PackageVersionTrait;

    /**
     * @inheritDoc
     */
    public function getCssPaths()
    {
        return [
            '/bundles/pimcoreassetmetadataclassdefinitions/css/icons.css'
        ];
    }

    /** @inheritDoc */
    public function getJsPaths()
    {
        return [
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/metadataStartup.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/configurationItem.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/configurationTree.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/edit.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/layoutHelper.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/editorPanel.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/asset/metadata/layout/iframe.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/data.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/asset.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/calculatedValue.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/checkbox.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/country.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/user.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/language.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/object.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/date.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/datetime.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/document.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/input.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/manyToManyRelation.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/numeric.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/localizedfields.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/select.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/multiselect.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/textarea.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/data/wysiwyg.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/abstractManyToOneRelation.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/asset.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/calculatedValue.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/checkbox.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/country.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/user.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/manyToManyRelation.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/language.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/object.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/date.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/datetime.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/document.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/input.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/numeric.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/localizedfields.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/select.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/multiselect.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/textarea.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/tags/wysiwyg.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/asset/metadata/tags/calculatedValue.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/asset/metadata/tags/datetime.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/asset/metadata/tags/manyToManyRelation.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/asset/metadata/tags/country.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/asset/metadata/tags/user.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/asset/metadata/tags/numeric.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/asset/metadata/tags/language.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/asset/metadata/tags/multiselect.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/asset/metadata/tags/wysiwyg.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/layout/panel.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/layout/accordion.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/layout/button.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/layout/iframe.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/layout/fieldcontainer.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/layout/fieldset.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/layout/text.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/layout/region.js',
            '/bundles/pimcoreassetmetadataclassdefinitions/js/pimcore/classes/layout/tabpanel.js',
        ];
    }

    /**
     * If the bundle has an installation routine, an installer is responsible of handling installation related tasks
     *
     * @return InstallerInterface|null
     */
    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName(): string
    {
        return 'pimcore/asset-metadata-class-definitions';
    }

    public static function registerDependentBundles(BundleCollection $collection)
    {
        $collection->addBundle(new PimcoreEnterpriseSubscriptionToolsBundle());
    }

    public function getBundleLicenseId(): string
    {
        return 'EMD';
    }
}
