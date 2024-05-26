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

namespace Pimcore\Bundle\PortalEngineBundle\Model\DataObject;

interface PortalUserGroupInterface
{
    /**
     * @return \Pimcore\Model\DataObject\Data\ElementMetadata[]
     */
    public function getAssetWorkspaceDefinition();

    /**
     * @return \Pimcore\Model\DataObject\Data\ElementMetadata[]
     */
    public function getDataObjectWorkspaceDefinition();

    /**
     * @return array|null
     */
    public function getVisibleLanguages();

    /**
     * @return array|null
     */
    public function getEditableLanguages();
}
