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

namespace Pimcore\AssetMetadataClassDefinitionsBundle\Model\ClassDefinition\Data;

class AbstractRelation extends Data
{
    /**
     *
     * @var bool
     */
    public $documentsAllowed = false;

    /**
     *
     * @var bool
     */
    public $objectsAllowed = false;

    /**
     *
     * @var bool
     */
    public $assetsAllowed = false;

    public $classes = [];

    public $assetTypes = [];

    public $documentTypes = [];
}
