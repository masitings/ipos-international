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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\DataObjectFieldExtractor;

use Pimcore\Bundle\PortalEngineBundle\Enum\Download\Context;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadType;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadService;
use Pimcore\Model\DataObject\ClassDefinition\Data;

abstract class AbstractDataObjectFieldExtractor implements DataObjectFieldExtractorInterface
{
    protected $downloadService;

    public function __construct(DownloadService $downloadService)
    {
        $this->downloadService = $downloadService;
    }

    protected function getLabel(DataPoolConfigInterface $dataPoolConfig, string $type, Data $fieldDefinition, array $context = [])
    {
        $attribute = $this->getAttribute($fieldDefinition);

        return $this->downloadService->getLabelForDownloadable($dataPoolConfig, $type, $attribute);
    }

    protected function getAttribute(Data $fieldDefinition, array $context = [])
    {
        $attribute = $fieldDefinition->getName();

        if (!empty($context[Context::CONTAINER_TYPE])) {
            $attribute = implode('.', array_filter([$context[Context::CONTAINER_TYPE], $context[Context::CONTAINER], $context[Context::ATTRIBUTE], $attribute]));
        }

        return $attribute;
    }

    protected function createBasicDownloadType(DataPoolConfigInterface $dataPoolConfig, string $type, Data $fieldDefinition, array $context = [])
    {
        return (new DownloadType())
            ->setLocalized(($context[Context::LOCALIZED] ?? null) ? true : false)
            ->setAttribute($this->getAttribute($fieldDefinition, $context))
            ->setLabel($this->getLabel($dataPoolConfig, $type, $fieldDefinition, $context));
    }

    public function canTransform($data): bool
    {
        return false;
    }

    public function transform($data)
    {
        return null;
    }
}
