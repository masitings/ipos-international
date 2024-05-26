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

class DownloadFormatAccess
{
    private $dataPoolConfigId;
    private $downloadType;
    private $attribute;
    private $format;

    public function __construct(int $dataPoolConfigId, string $downloadType, ?string $attribute, ?string $format)
    {
        $this->dataPoolConfigId = $dataPoolConfigId;
        $this->downloadType = $downloadType;
        $this->attribute = $attribute;
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function toPermission(): string
    {
        return implode(Permission::PERMISSION_DELIMITER, [
            Permission::DOWNLOAD_FORMAT_ACCESS,
            $this->dataPoolConfigId,
            $this->downloadType,
            $this->attribute,
            $this->format
        ]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toPermission();
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param int $dataPoolConfigId
     * @param string $downloadType
     * @param string $attribute
     * @param string $format
     *
     * @return static
     */
    public static function create(int $dataPoolConfigId, string $downloadType, ?string $attribute, ?string $format): self
    {
        return new static($dataPoolConfigId, $downloadType, $attribute, $format);
    }

    /**
     * @param int $dataPoolConfigId
     * @param DownloadType $downloadType
     *
     * @return static
     */
    public static function fromDownloadTypeAndFormat(int $dataPoolConfigId, DownloadType $downloadType, ?string $format): self
    {
        return self::create($dataPoolConfigId, $downloadType->getType(), $downloadType->getAttribute(), $format);
    }
}
