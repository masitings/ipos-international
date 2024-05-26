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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool;

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\Asset\WorkspaceConfig as AssetWorkspaceConfig;
use Pimcore\Model\Asset;
use Pimcore\Model\Document\Editable\Block;

class AssetConfig extends AbstractDataPoolConfig
{
    /**
     * @var array|null
     */
    protected $workspaces;

    public function isEnabled(): bool
    {
        return true;
    }

    public function getElementType(): string
    {
        return 'asset';
    }

    public function getWorkspaces(): array
    {
        if (is_null($this->workspaces)) {
            $workspaces = [];

            /**
             * @var Block $block
             */
            if ($block = $this->document->getEditable(Editables\DataPool\DataPoolConfig::WORKSPACE_DEFINITION)) {
                foreach ($block->getElements() as $blockItem) {
                    $workspacePathObject = $this->getBlockItemElementData($blockItem, Editables\DataPool\WorkspaceConfig::WORKSPACE_PATH);

                    if (!$workspacePathObject instanceof Asset) {
                        continue;
                    }

                    $workspaces[] = new AssetWorkspaceConfig(
                        $workspacePathObject->getRealFullPath(),
                        (bool)$this->getBlockItemElementData($blockItem, Editables\DataPool\WorkspaceConfig::PERMISSION_VIEW),
                        (bool)$this->getBlockItemElementData($blockItem, Editables\DataPool\WorkspaceConfig::PERMISSION_DOWNLOAD),
                        (bool)$this->getBlockItemElementData($blockItem, Editables\DataPool\WorkspaceConfig::PERMISSION_EDIT),
                        (bool)$this->getBlockItemElementData($blockItem, Editables\DataPool\WorkspaceConfig::PERMISSION_UPDATE),
                        (bool)$this->getBlockItemElementData($blockItem, Editables\DataPool\WorkspaceConfig::PERMISSION_CREATE),
                        (bool)$this->getBlockItemElementData($blockItem, Editables\DataPool\WorkspaceConfig::PERMISSION_DELETE),
                        (bool)$this->getBlockItemElementData($blockItem, Editables\DataPool\WorkspaceConfig::PERMISSION_SUBFOLDER),
                        (bool)$this->getBlockItemElementData($blockItem, Editables\DataPool\WorkspaceConfig::PERMISSION_VIEW_OWNED_ASSETS_ONLY)
                    );
                }
            }

            $this->workspaces = $workspaces;
        }

        return $this->workspaces;
    }

    /**
     * @return array
     */
    public function getGeneralAttributes()
    {
        return (array)$this->getElementData(Editables\DataPool\AssetConfig::GENERAL_ATTRIBUTES);
    }

    /**
     * @return array
     */
    public function getDirectDownloadShortcuts()
    {
        return (array)$this->getElementData(Editables\DataPool\AssetConfig::DIRECT_DOWNLOAD_SHORTCUTS);
    }

    /**
     * @return Asset\Folder|null
     */
    public function getUploadFolder(): ?Asset\Folder
    {
        $folder = $this->getElementData(Editables\DataPool\AssetConfig::UPLOAD_FOLDER);

        return $folder instanceof Asset\Folder ? $folder : null;
    }
}
