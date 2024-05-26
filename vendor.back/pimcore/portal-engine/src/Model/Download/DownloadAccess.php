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

namespace Pimcore\Bundle\PortalEngineBundle\Model\Download;

use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;

class DownloadAccess
{
    private $dataPoolConfigId;
    private $downloadType;
    private $attribute;
    private $formats;

    public function __construct(int $dataPoolConfigId, string $downloadType, ?string $attribute, ?array $formats = [])
    {
        $this->dataPoolConfigId = $dataPoolConfigId;
        $this->downloadType = $downloadType;
        $this->attribute = $attribute;
        $this->formats = $formats;
    }

    /**
     * @return string
     */
    public function toPermission(): string
    {
        return implode(Permission::PERMISSION_DELIMITER, [
            Permission::DOWNLOAD,
            $this->dataPoolConfigId,
            $this->downloadType,
            $this->attribute
        ]);
    }

    /**
     * @return DownloadFormatAccess[]
     */
    public function getFormatAccess(): array
    {
        $result = [];
        foreach ($this->formats as $format) {
            $result[] = DownloadFormatAccess::create($this->dataPoolConfigId, $this->downloadType, $this->attribute, $format);
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getDataPoolConfigId(): int
    {
        return $this->dataPoolConfigId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toPermission();
    }

    /**
     * @param int $dataPoolConfigId
     * @param string $downloadType
     * @param string|null $attribute
     * @param array $formats
     *
     * @return static
     */
    public static function create(int $dataPoolConfigId, string $downloadType, ?string $attribute, ?array $formats = []): self
    {
        return new static($dataPoolConfigId, $downloadType, $attribute, $formats);
    }

    /**
     * @param int $dataPoolConfigId
     * @param DownloadType $downloadType
     *
     * @return static
     */
    public static function fromDownloadType(int $dataPoolConfigId, DownloadType $downloadType): self
    {
        $formats = [];
        foreach ($downloadType->getFormats() as $format) {
            $formats[] = $format['id'];
        }

        return self::create($dataPoolConfigId, $downloadType->getType(), $downloadType->getAttribute(), $formats);
    }

    /**
     * @param int $dataPoolConfigId
     * @param DownloadConfig $downloadConfig
     *
     * @return static
     */
    public static function fromDownloadConfig(int $dataPoolConfigId, DownloadConfig $downloadConfig): self
    {
        return self::create($dataPoolConfigId, $downloadConfig->getType(), $downloadConfig->getAttribute(), [$downloadConfig->getFormat()]);
    }
}
