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
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Document\Editable\Block;

class DataObjectConfig extends AbstractDataPoolConfig
{
    /**
     * @var array|null
     */
    protected $workspaces;

    public function isEnabled(): bool
    {
        return !empty($this->getDataObjectClass()) && !empty($this->getCustomLayoutId());
    }

    public function getElementType(): string
    {
        return 'object';
    }

    public function getDataObjectClass(): ?string
    {
        return $this->getElementData(Editables\DataPool\DataObjectConfig::DATA_OBJECT_CLASS);
    }

    public function getCustomLayoutId(): ?string
    {
        return $this->getElementData(Editables\DataPool\DataObjectConfig::DETAIL_PAGE_LAYOUT);
    }

    /**
     * @return WorkspaceConfig[]
     */
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

                    if (!$workspacePathObject instanceof AbstractObject) {
                        continue;
                    }

                    $workspaces[] = new WorkspaceConfig(
                        $workspacePathObject->getRealFullPath(),
                        (bool)$this->getBlockItemElementData($blockItem, Editables\DataPool\WorkspaceConfig::PERMISSION_VIEW),
                        (bool)$this->getBlockItemElementData($blockItem, Editables\DataPool\WorkspaceConfig::PERMISSION_DOWNLOAD)
                    );
                }
            }
            $this->workspaces = $workspaces;
        }

        return $this->workspaces;
    }
}
