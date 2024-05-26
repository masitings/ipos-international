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

use Pimcore\Model\Document\Page;

interface DataPoolConfigInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    public function getDataPoolName(): string;

    /**
     * @return Page
     */
    public function getDocument(): Page;

    /**
     * @return string
     */
    public function getLanguage(): string;

    /**
     * @return string[]
     */
    public function getVisibleLanguages(): array;

    /**
     * @return string[]
     */
    public function getEditableLanguages(): array;

    /**
     * @return FilterDefinitionConfig[]
     */
    public function getFilterDefinitions(): array;

    /**
     * @return SortOptionConfig[]
     */
    public function getSortOptions(): array;

    /**
     * @return string[]
     */
    public function getGridConfigurationAttributes(): array;

    /**
     * @return string
     */
    public function getGridConfigurationNameAttribute(): string;

    /**
     * @return string
     */
    public function getElementType(): string;

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @return string[]
     */
    public function getAvailableDownloadThumbnails(): array;

    /**
     * @return string[]
     */
    public function getAvailableDownloadFormats(): array;

    /**
     * @return WorkspaceConfig[]
     */
    public function getWorkspaces(): array;

    /**
     * @return ?int
     */
    public function getRootTag(): ?int;

    /**
     * @return string
     */
    public function getCssClass(): string;

    /**
     * @return bool
     */
    public function getEnableVersionHistory(): bool;

    /**
     * @return \Pimcore\Model\Asset\Image|string|null
     */
    public function getIcon();

    public function getLanguageVariantOrDocument(): Page;

    public function getLanguageVariantDataPoolId(): int;
}
