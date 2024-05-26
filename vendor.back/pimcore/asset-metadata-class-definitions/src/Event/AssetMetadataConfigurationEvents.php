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

namespace Pimcore\AssetMetadataClassDefinitionsBundle\Event;

/**
 * Class AssetMetadataConfigurationEvents
 *
 * @package Pimcore\AssetMetadataClassDefinitionsBundle\Event
 */
final class AssetMetadataConfigurationEvents
{
    /**
     * @Event("Pimcore\AssetMetadataClassDefinitionsBundle\Event\Model\Asset\ConfigurationEvent")
     *
     * @var string
     */
    const PRE_ADD = 'pimcore.asset.metadata.configuration.preAdd';

    /**
     * @Event("Pimcore\AssetMetadataClassDefinitionsBundle\Event\Model\Asset\ConfigurationEvent")
     *
     * @var string
     */
    const POST_ADD = 'pimcore.asset.metadata.configuration.postAdd';

    /**
     * @Event("Pimcore\AssetMetadataClassDefinitionsBundle\Event\Model\Asset\ConfigurationEvent")
     *
     * @var string
     */
    const PRE_UPDATE = 'pimcore.asset.metadata.configuration.preUpdate';

    /**
     * @Event("Pimcore\AssetMetadataClassDefinitionsBundle\Event\Model\Asset\ConfigurationEvent")
     *
     * @var string
     */
    const POST_UPDATE = 'pimcore.asset.metadata.configuration.postUpdate';

    /**
     * @Event("Pimcore\AssetMetadataClassDefinitionsBundle\Event\Model\Asset\ConfigurationEvent")
     *
     * @var string
     */
    const PRE_DELETE = 'pimcore.asset.metadata.configuration.preDelete';

    /**
     * @Event("Pimcore\AssetMetadataClassDefinitionsBundle\Event\Model\Asset\ConfigurationEvent")
     *
     * @var string
     */
    const POST_DELETE = 'pimcore.asset.metadata.configuration.postDelete';
}
