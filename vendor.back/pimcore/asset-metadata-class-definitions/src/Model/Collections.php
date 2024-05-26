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

namespace Pimcore\AssetMetadataClassDefinitionsBundle\Model;

use Pimcore\Model;

class Collections extends Model\AbstractModel
{
    /** Asset id.
     * @var int
     */
    public $assetId;

    public $collections = [];

    /**
     * @return int
     */
    public function getAssetId()
    {
        return $this->assetId;
    }

    /**
     * @param int $assetId
     */
    public function setAssetId(int $assetId): void
    {
        $this->assetId = $assetId;
    }

    /**
     * @return mixed
     */
    public function getCollections()
    {
        return $this->collections ?? [];
    }

    /**
     * @param array $collections List of collection names
     */
    public function setCollections($collections): void
    {
        $this->collections = $collections ?? [];
    }

    public function applyToAsset()
    {
        $asset = Model\Asset::getById($this->assetId);
        $asset->setCustomSetting('plugin_assetmetdata_collections', array_values(array_unique($this->collections)));
    }

    /**
     * @throws \Exception
     */
    public function save()
    {
        $this->applyToAsset();

        $asset = Model\Asset::getById($this->assetId);
        $asset->save();
    }

    /**
     * @param int $assetId
     *
     * @return Collections
     */
    public static function getByAssetId(int $assetId)
    {
        $asset = Model\Asset::getById($assetId);

        $self = new self();
        $self->setAssetId($assetId);
        $self->setCollections(array_values($asset->getCustomSetting('plugin_assetmetdata_collections') ?? []));

        return $self;
    }
}
