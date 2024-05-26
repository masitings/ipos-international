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

use Pimcore\AssetMetadataClassDefinitionsBundle\Model\Configuration\Dao;
use Pimcore\Model\AbstractModel;
use Pimcore\Model\DataObject\ClassDefinition\Helper\VarExport;

/**
 * Class Configuration
 *
 * @method string getDefinitionFile()
 * @method Dao getDao()
 * @method void save()
 */
class Configuration extends AbstractModel
{
    use VarExport;

    /**
     * @var string
     */
    public $name;

    /** @var string|null */
    public $title;

    /** @var string */
    public $prefix;

    /** string|null */
    public $icon;

    public $layoutDefinitions;

    public function __construct($name = null, $title = null, $layoutDefinitions = [])
    {
        $this->name = $name;
        $this->title = $title;
        $this->layoutDefinitions = $layoutDefinitions;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLayoutDefinitions()
    {
        return $this->layoutDefinitions;
    }

    /**
     * @param mixed $layoutDefinitions
     */
    public function setLayoutDefinitions($layoutDefinitions): void
    {
        $this->layoutDefinitions = $layoutDefinitions;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function delete()
    {
        $this->getDao()->delete();
    }

    /**
     * @return string|null
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     */
    public function setIcon($icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return (string) $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix): void
    {
        $this->prefix = (string) $prefix;
    }
}
