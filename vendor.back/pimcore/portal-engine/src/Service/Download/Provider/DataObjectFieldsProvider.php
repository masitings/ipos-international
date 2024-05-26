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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider;

use Pimcore\Bundle\PortalEngineBundle\Enum\Download\Context;
use Pimcore\Bundle\PortalEngineBundle\Enum\Download\Type;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataObjectConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadType;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\CustomLayoutService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Provider\DataObjectFieldExtractor\DataObjectFieldExtractorInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\LanguagesService;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;

class DataObjectFieldsProvider implements DownloadProviderInterface
{
    protected $customLayoutService;

    /**
     * @var DataObjectFieldExtractorInterface[]
     */
    protected $fieldExtractors = [];

    /**
     * @var LanguagesService
     */
    protected $languagesService;

    public function __construct(CustomLayoutService $customLayoutService, LanguagesService $languagesService)
    {
        $this->customLayoutService = $customLayoutService;
        $this->languagesService = $languagesService;
    }

    /**
     * @param DataObjectFieldExtractorInterface $dataObjectFieldExtractor
     */
    public function addFieldExtractor(DataObjectFieldExtractorInterface $dataObjectFieldExtractor)
    {
        $this->fieldExtractors[] = $dataObjectFieldExtractor;
    }

    /**
     * {@inheritDoc}
     */
    public function canProvide(DataPoolConfigInterface $config): bool
    {
        return $config instanceof DataObjectConfig && $config->isEnabled();
    }

    /**
     * @param DataObjectConfig $config
     * @param bool $checkPermissions
     *
     * @return DownloadType[]
     */
    public function provide(DataPoolConfigInterface $config, bool $checkPermissions = true): array
    {
        if (!$config instanceof DataObjectConfig || !$config->isEnabled()) {
            return [];
        }

        $customLayout = $this->customLayoutService->getCustomLayout($config->getCustomLayoutId());

        if (!$customLayout) {
            return [];
        }

        $downloadTypes = [];

        $this->provideByDefinitions($config, $customLayout->getLayoutDefinitions(), $downloadTypes);

        return $downloadTypes;
    }

    /**
     * @param DataObjectConfig $config
     * @param ClassDefinition\Layout|ClassDefinition\Data $definition
     * @param array $downloadTypes
     * @param array $context
     */
    public function provideByDefinitions(DataObjectConfig $config, $definition, array &$downloadTypes, array $context = [])
    {
        if ($definition instanceof ClassDefinition\Data) {
            foreach ($this->fieldExtractors as $fieldExtractor) {
                if (!$fieldExtractor->supports($definition)) {
                    continue;
                }

                $extracted = $fieldExtractor->extract($config, $definition, $context);

                if (!$extracted) {
                    continue;
                }

                if (!is_array($extracted)) {
                    $extracted = [$extracted];
                }

                foreach ($extracted as $item) {
                    $downloadTypes[] = $item;
                }
            }
        } else {
            foreach ($definition->getChildren() as $child) {
                $this->provideByDefinitions($config, $child, $downloadTypes, $context);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function canExtractSource(DataPoolConfigInterface $dataPoolConfig, DownloadConfig $downloadConfig, $source): bool
    {
        return
            $this->canProvide($dataPoolConfig) &&
            $downloadConfig->getType() !== Type::STRUCTURED_DATA &&
            $downloadConfig->getAttribute() &&
            $source instanceof AbstractObject;
    }

    /**
     * @param string $attribute
     *
     * @return string[]
     */
    protected function getAttributeParts(string $attribute)
    {
        return explode('.', $attribute);
    }

    /**
     * @param DataPoolConfigInterface $dataPoolConfig
     * @param $attribute
     * @param bool $localized
     * @param $container
     *
     * @return array|null
     */
    protected function extractDataFromContainer($attribute, bool $localized, $container)
    {
        $getter = 'get' . ucfirst($attribute);

        if (!method_exists($container, $getter)) {
            return null;
        }

        if ($localized) {
            $data = [];

            foreach ($this->languagesService->getVisibleLanguages() as $language) {
                $value = $this->transformValue($container->$getter($language));

                if (!is_array($value)) {
                    $value = [$value];
                }

                $data = array_merge($data, array_filter($value));
            }

            return $data;
        } else {
            return $this->transformValue($container->$getter());
        }
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function transformValue($value)
    {
        foreach ($this->fieldExtractors as $extractor) {
            if ($extractor->canTransform($value)) {
                return $extractor->transform($value);
            }
        }

        return $value;
    }

    protected function extractDataFromObjectbrick(DownloadConfig $config, array $path, $container)
    {
        list($objectbrickField, $objectbrickType, $objectbrickAttribute) = $path;

        $objectbrickContainerGetter = 'get' . ucfirst($objectbrickField);
        $objectbrickGetter = 'get' . ucfirst($objectbrickType);

        if (!method_exists($container, $objectbrickContainerGetter)) {
            return null;
        }

        $brickContainer = $container->$objectbrickContainerGetter;

        if (!$brickContainer || !method_exists($brickContainer, $objectbrickGetter)) {
            return null;
        }

        $brick = $brickContainer->$objectbrickGetter();

        if (!$brick) {
            return null;
        }

        return $this->extractDataFromContainer($objectbrickAttribute, $config->getLocalized(), $container);
    }

    protected function extractDataFromFieldcollection(DownloadConfig $config, array $path, $container)
    {
        list($fieldcollectionField, $fieldcollectionType, $fieldcollectionAttribute) = $path;

        $fieldcollectionGetter = 'get' . ucfirst($fieldcollectionField);

        if (!method_exists($container, $fieldcollectionGetter)) {
            return null;
        }

        $fieldcollection = $container->$fieldcollectionGetter();

        if (!$fieldcollection instanceof Fieldcollection) {
            return null;
        }

        $data = [];

        foreach ($fieldcollection->getItems() as $item) {
            if ($item->getType() !== $fieldcollectionType) {
                continue;
            }

            $value = $this->extractDataFromContainer($fieldcollectionAttribute, $config->getLocalized(), $item);

            if (!is_array($value)) {
                $value = [$value];
            }

            $data = array_merge($data, array_filter($value));
        }

        return $data;
    }

    /**
     * @param DataObjectConfig $dataPoolConfig
     * @param DownloadConfig $downloadConfig
     * @param AbstractObject $source
     *
     * @return mixed
     */
    public function extractSource(DataPoolConfigInterface $dataPoolConfig, DownloadConfig $downloadConfig, $source)
    {
        $path = $this->getAttributeParts($downloadConfig->getAttribute());

        // no container, only attribute
        if (count($path) === 1) {
            return $this->extractDataFromContainer($downloadConfig->getAttribute(), $downloadConfig->getLocalized(), $source);
        }

        $containerType = array_shift($path);

        switch ($containerType) {
            case Context::CONTAINER_TYPE_OBJECTBRICK:
                return $this->extractDataFromObjectbrick($downloadConfig, $path, $source);

            case Context::CONTAINER_TYPE_FIELDCOLLECTIONS:
                return $this->extractDataFromFieldcollection($downloadConfig, $path, $source);
        }

        return null;
    }
}
