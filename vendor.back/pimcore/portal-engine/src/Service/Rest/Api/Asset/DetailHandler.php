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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\Asset;

use Carbon\Carbon;
use Pimcore\Bundle\PortalEngineBundle\Enum\Download\Type;
use Pimcore\Bundle\PortalEngineBundle\Enum\ImageThumbnails;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AssetConfig;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\DataPoolConfigInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadAccess;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\DataPool\ListDataEntry;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\FileNameParserService;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\MetadataService;
use Pimcore\Bundle\PortalEngineBundle\Service\Asset\ThumbnailService;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\Generator\OriginalAssetGenerator;
use Pimcore\Bundle\PortalEngineBundle\Service\Element\UrlExtractorService;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataPool\AbstractDetailHandler;
use Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\VersionHandlerTrait;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\Search\TagService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Bundle\PortalEngineBundle\Service\Workflow\WorkflowService;
use Pimcore\Extension\Bundle\PimcoreBundleManager;
use Pimcore\Localization\IntlFormatter;
use Pimcore\Model\Asset;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Tag;
use Pimcore\Model\Version;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DetailHandler extends AbstractDetailHandler
{
    use VersionHandlerTrait;

    protected $thumbnailService;
    protected $dataPoolConfigService;
    protected $formatter;
    protected $metadataService;
    protected $fileNameParserService;
    protected $permissionService;
    protected $securityService;
    protected $urlExtractorService;
    protected $tagService;
    protected $workflowService;
    protected $eventDispatcher;
    protected $pimcoreBundleManager;
    protected $authorizationChecker;

    public function __construct(
        ThumbnailService $thumbnailService,
        DataPoolConfigService $dataPoolConfigService,
        IntlFormatter $formatter,
        MetadataService $metadataService,
        FileNameParserService $fileNameParserService,
        PermissionService $permissionService,
        SecurityService $securityService,
        UrlExtractorService $urlExtractorService,
        TagService $tagService,
        WorkflowService $workflowService,
        EventDispatcherInterface $eventDispatcher,
        PimcoreBundleManager $pimcoreBundleManager,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->thumbnailService = $thumbnailService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->formatter = $formatter;
        $this->metadataService = $metadataService;
        $this->fileNameParserService = $fileNameParserService;
        $this->permissionService = $permissionService;
        $this->securityService = $securityService;
        $this->urlExtractorService = $urlExtractorService;
        $this->tagService = $tagService;
        $this->workflowService = $workflowService;
        $this->eventDispatcher = $eventDispatcher;
        $this->pimcoreBundleManager = $pimcoreBundleManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param Asset $asset
     *
     * @return string
     */
    public function getFileExtension(Asset $asset)
    {
        $info = pathinfo($asset->getFilename());

        return $info['extension'];
    }

    /**
     * @param Asset $asset
     *
     * @return array
     */
    public function getData(Asset $asset): array
    {
        return [
            'id' => $asset->getId(),
            'breadcrumbs' => $this->urlExtractorService->extractBreadcrumbs($asset),
            'embeddedMetadata' => method_exists($asset, 'getEmbeddedMetaData') ? $asset->getEmbeddedMetaData(true) : null,
            'filename' => $asset->getFilename(),
            'fullPath' => $asset->getFullPath(),
            'path' => $asset->getPath(),
            'thumbnail' => $this->thumbnailService->getThumbnailPath($asset, ImageThumbnails::ELEMENT_DETAIL),
            'thumbnailName' => ImageThumbnails::ELEMENT_DETAIL,
            'preview' => $this->getPreviewData($asset),
            'creationDate' => $this->formatter->formatDateTime(Carbon::createFromTimestamp($asset->getCreationDate()), IntlFormatter::DATETIME_SHORT),
            'modificationDate' => $this->formatter->formatDateTime(Carbon::createFromTimestamp($asset->getModificationDate()), IntlFormatter::DATETIME_SHORT),
            'fileSize' => formatBytes($asset->getFileSize()),
            'extension' => $this->getFileExtension($asset),
            'type' => $asset->getType(),
            'attributes' => $this->getAttributes($asset),
            'downloadShortcuts' => $this->getDirectDownloadShortcuts($asset),
            'metadata' => $this->metadataService->getMetadata($asset),
            'tags' => $this->tagService->getTagsSelectOptions($this->dataPoolConfigService->getCurrentDataPoolConfig()->getRootTag()),
            'assignedTags' => array_map(function (Tag $tag) {
                return ['value' => $tag->getId(), 'label' => $tag->getName()];
            }, Tag::getTagsForElement('asset', $asset->getId())),
            'permissions' => $this->permissionService->getPermissionsForUser(
                $this->securityService->getPortalUser(),
                $this->dataPoolConfigService->getCurrentDataPoolConfig()->getId(),
                $asset->getRealFullPath(),
                true,
                true,
                true
            ),
            'directEditSupported' => $this->isDirectEditSupported($this->dataPoolConfigService->getCurrentDataPoolConfig()),
            'workflow' => $this->workflowService->getWorkflowDetails($asset)
        ];
    }

    /**
     * @param Asset $asset
     *
     * @return array
     */
    protected function getPreviewData(Asset $asset)
    {
        if ($asset instanceof Asset\Document) {
            return [
                'pageCount' => $asset->getPageCount()
            ];
        }

        if ($asset instanceof Asset\Video) {
            return [
                'data' => $asset->getFullPath()
            ];
        }

        return [];
    }

    /**
     * @return array
     */
    public function getLayoutDefinitions()
    {
        return $this->metadataService->getLayoutDefinitions();
    }

    public function getAttributes(Asset $asset)
    {
        $config = $this->dataPoolConfigService->getCurrentDataPoolConfig();

        if (!$config instanceof AssetConfig) {
            return [];
        }

        $attributes = $config->getGeneralAttributes();
        $data = [];

        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $data[$attribute] = $this->metadataService->getNormalizedMetadataValue($asset, $attribute);
            }
        }

        return array_filter($data);
    }

    public function getDirectDownloadShortcuts(Asset $asset)
    {
        $config = $this->dataPoolConfigService->getCurrentDataPoolConfig();

        if (!$config instanceof AssetConfig) {
            return [];
        }

        if ($this->securityService->getPortalUser()->isPortalShareUser()) {
            return [];
        }

        $data = $config->getDirectDownloadShortcuts();

        if (empty($data) || !$asset instanceof Asset\Image) {
            $data = [OriginalAssetGenerator::ORIGINAL_FORMAT];
        }

        return array_values(array_filter($data, function ($item) use ($config) {
            $downloadAccess = DownloadAccess::create($config->getId(), Type::ASSET, null, [$item]);

            return $this->authorizationChecker->isGranted(Permission::DOWNLOAD, $downloadAccess);
        }));
    }

    /**
     * @param Asset $asset
     *
     * @return array
     */
    public function getVersionHistory(Asset $asset)
    {
        return $this->extractVersionsData($asset, $this->formatter, $this->eventDispatcher);
    }

    /**
     * @param Asset $src
     * @param Version[] $versions
     */
    public function getVersionComparison(Asset $src, array $versions)
    {
        $comparison = [];

        foreach ($versions as $version) {
            $asset = $this->extractVersionElement($version);

            if (!$asset instanceof Asset) {
                continue;
            }

            if ($asset instanceof Asset\Image) {
                $type = pathinfo($asset->getFilename(), PATHINFO_EXTENSION);
                $comparison['_preview'][$version->getId()] = '<img style="max-width: 200px;" src="data:image/' . $type . ';base64,' . base64_encode($asset->getData()) . '"/>';
            }

            $allMetadata = $asset->getMetadata();

            foreach ($allMetadata as $metadata) {
                $key = "{$metadata['name']}_{$metadata['type']}_{$metadata['language']}";

                // prefill for all versions with null
                if (!array_key_exists($key, $comparison)) {
                    $data = [];

                    foreach ($versions as $v) {
                        $data[$v->getId()] = ['dirty' => false, 'displayValue' => null, 'value' => null];
                    }

                    $comparison[$key] = [
                        'name' => $metadata['name'],
                        'type' => $metadata['type'],
                        'language' => $metadata['language'],
                        'data' => $data
                    ];
                }

                if (is_array($metadata['data'])) {
                    $value = implode("\n", $metadata['data']);
                    $displayValue = implode(', ', array_map([$this, 'getVersionDisplayValue'], $metadata['data']));
                } else {
                    $value = $metadata['data'] ? (string)$metadata['data'] : null;
                    $displayValue = $this->getVersionDisplayValue($metadata['data']);
                }

                $comparison[$key]['data'][$version->getId()]['value'] = $value;
                $comparison[$key]['data'][$version->getId()]['displayValue'] = $displayValue;
            }
        }

        // compare all stringified values
        foreach ($comparison as &$row) {
            if (!$row['data']) {
                continue;
            }

            $data = $row['data'];
            $keys = array_keys($data);
            $first = reset($data);
            $firstKey = reset($keys);

            foreach ($row['data'] as $key => &$column) {
                if ($first['value'] !== $column['value'] && $key !== $firstKey) {
                    $column['dirty'] = true;
                }
            }
        }

        return $comparison;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function getVersionDisplayValue($value)
    {
        if (is_array($value)) {
            return implode(' ', $value);
        }

        if ($value instanceof ElementInterface) {
            return $value->getFullPath();
        }

        if ($value instanceof \DateTime) {
            return $this->formatter->formatDateTime($value);
        }

        return (string)$value;
    }

    public function getResultListItemData(ListDataEntry $item, array $params): array
    {
        $data = parent::getResultListItemData($item, $params);
        $data['fileExtension'] = $this->fileNameParserService->getExtensionFromFilename($item->getName());

        return $data;
    }

    /**
     * @param DataPoolConfigInterface $dataPoolConfig
     *
     * @return bool
     */
    protected function isDirectEditSupported(DataPoolConfigInterface $dataPoolConfig)
    {
        if (!class_exists('\\Pimcore\\Bundle\\DirectEditBundle\\PimcoreDirectEditBundle')) {
            return false;
        }

        try {
            return (bool)$this->pimcoreBundleManager->getActiveBundle('Pimcore\\Bundle\\DirectEditBundle\\PimcoreDirectEditBundle');
        } catch (\Exception $e) {
            return false;
        }
    }
}
