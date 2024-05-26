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

namespace Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Download;

use Pimcore\Bundle\PortalEngineBundle\Entity\BatchTask;
use Pimcore\Bundle\PortalEngineBundle\Enum\BatchTask\Payload\Download;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Event\Download\DownloadAssetEvent;
use Pimcore\Bundle\PortalEngineBundle\Message\BatchTask\Download\ProcessChunkMessage;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits\BatchTaskHandlerTrait;
use Pimcore\Bundle\PortalEngineBundle\MessageHandler\BatchTask\Traits\DownloadTaskHandlerTrait;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\BundleStructuredData;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableAsset;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\Downloadable\DownloadableStructuredData;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadService;
use Pimcore\Bundle\PortalEngineBundle\Service\Download\DownloadZipGenerationService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\PermissionService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Bundle\PortalEngineBundle\Service\Zip\ContainedInZipService;
use Pimcore\Bundle\PortalEngineBundle\Service\Zip\ZipArchiveService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ProcessChunkHandler implements MessageHandlerInterface
{
    use BatchTaskHandlerTrait;
    use DownloadTaskHandlerTrait;

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var ZipArchiveService
     */
    protected $zipArchiveService;

    /**
     * @var ContainedInZipService
     */
    protected $containedInZipService;

    /**
     * @var DownloadService
     */
    protected $downloadService;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var DownloadZipGenerationService
     */
    protected $downloadZipGenerationService;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var SecurityService
     */
    protected $securityService;

    public function __construct(
        MessageBusInterface $messageBus,
        ContainedInZipService $containedInZipService,
        DownloadService $downloadService,
        EventDispatcherInterface $eventDispatcher,
        DownloadZipGenerationService $downloadZipGenerationService,
        PermissionService $permissionService,
        DataPoolConfigService $dataPoolConfigService,
        SecurityService $securityService
    ) {
        $this->messageBus = $messageBus;
        $this->containedInZipService = $containedInZipService;
        $this->downloadService = $downloadService;
        $this->eventDispatcher = $eventDispatcher;
        $this->downloadZipGenerationService = $downloadZipGenerationService;
        $this->permissionService = $permissionService;
        $this->dataPoolConfigService = $dataPoolConfigService;
        $this->securityService = $securityService;
    }

    /**
     * @param ProcessChunkMessage $message
     *
     * @throws \Exception
     */
    public function __invoke(ProcessChunkMessage $message)
    {
        $this->logger->notice(
            sprintf(
                '[Task ID %s] Process download chunk %s with %s item(s) [current memory usage: %s]',
                $message->getTaskId(),
                $message->getChunkIndex(),
                sizeof($message->getItems()),
                formatBytes(memory_get_usage())
            )
        );

        //if task was deleted/stopped or user is not available anymore nothing should be processed
        if (!$batchTask = $this->getBatchTask($message->getTaskId())) {
            return;
        }
        if (!$user = $this->getUserFromBatchTask($batchTask)) {
            return;
        }
        $this->setupPublicShare($batchTask);
        $publicShare = $this->publicShareService->getCurrentPublicShare();

        $this->securityService->setPortalUser($user);

        $zipId = $batchTask->getPayload()[Download::ZIP_ID];

        $this->storageService->initLocalZipFileFromStorage($zipId);

        foreach ($message->getItems() as $itemIndex => $item) {
            if ($this->batchTaskService->isItemIndexProcessed($batchTask, $itemIndex)) {
                continue;
            }

            $element = $item->getElement();
            $this->dataPoolConfigService->setCurrentDataPoolConfigById($item->getDataPoolId());

            if (empty($element)) {
                $this->logger->info(
                    sprintf(
                        '[Task ID %s] Skip item %s (not found anymore)',
                        $message->getTaskId(),
                        $item->getElementId()
                    )
                );
            } elseif ($publicShare && !$this->publicShareService->isElementInPublicShare($publicShare, $element)) {
                $this->logger->info(
                    sprintf(
                        '[Task ID %s] Skip item %s (not in public share)',
                        $message->getTaskId(),
                        $item->getElementId()
                    )
                );
            } elseif (!$this->permissionService->isPermissionAllowed(Permission::DOWNLOAD, $user, $item->getDataPoolId(), $element->getRealFullPath(), true)) {
                $this->logger->info(
                    sprintf(
                        '[Task ID %s] Skip item %s (not allowed)',
                        $message->getTaskId(),
                        $item->getElementId()
                    )
                );
            } else {
                $this->logger->info(
                    sprintf(
                        '[Task ID %s] Process item %s',
                        $message->getTaskId(),
                        $item->getElement()
                    )
                );

                $downloadables = $this->downloadService->getDownloadablesFromDownloadItem($item, $zipId);

                if (sizeof($downloadables) === 1 && !$this->getSingleFile($batchTask) && !$this->localZipExists($batchTask)) {
                    $this->processFirstSingleDownloadable($batchTask, array_pop($downloadables), $message->getIncludeDataPoolFolder());
                } elseif (sizeof($downloadables) > 0 && $this->getSingleFile($batchTask)) {
                    $this->moveSingleFileToZip($batchTask, $message);
                    $this->addDownloadablesToZip($downloadables, $batchTask, $message->getIncludeDataPoolFolder());
                } elseif (sizeof($downloadables) > 0) {
                    $this->addDownloadablesToZip($downloadables, $batchTask, $message->getIncludeDataPoolFolder());
                }
                $this->dispatchDownloadEvents($downloadables, $message);
            }

            //cleanup local files of downloadables if necessary
            foreach ($downloadables as $downloadable) {
                if ($downloadable->shouldDeleteAfter()) {
                    @unlink($downloadable->getDownloadFilePath());
                }
            }

            $this->batchTaskService->markItemIndexAsProcessed($batchTask, $itemIndex);
        }

        if ($this->localZipExists($batchTask)) {
            $this->storageService->commitLocalZipFileToStorage($zipId);
        }
    }

    protected function dispatchDownloadEvents(array $downloadables, ProcessChunkMessage $message)
    {
        foreach ($downloadables as $downloadable) {
            if ($downloadable instanceof DownloadableAsset) {
                $event = new DownloadAssetEvent($downloadable, $message->getDownloadContext());
                $this->eventDispatcher->dispatch($event);
            }
        }
    }

    protected function processFirstSingleDownloadable(BatchTask $batchTask, DownloadableInterface $downloadable, bool $includeDataPoolFolder)
    {
        if ($downloadable instanceof DownloadableStructuredData) {
            $downloadable->generate();
            $targetSubFolder = $includeDataPoolFolder ? $downloadable->getDataPoolConfig()->getDataPoolName() : '';
            $bundleStructuredData = new BundleStructuredData($downloadable->getDownloadUniqid(), $downloadable->getDownloadFormat(), $targetSubFolder);
            $this->rememberBundleStructuredData($batchTask, [$bundleStructuredData]);
        } else {
            $fileSystemPath = $downloadable->generate()->getDownloadFilePath();
            $storageFilePath = $this->storageService->commitLocalSingleFileToStorage($this->getZipId($batchTask), $fileSystemPath);

            $payload = $batchTask->getPayload();
            $payload[Download::SINGLE_FILE] = $storageFilePath;
            $payload[Download::SINGLE_FILE_POTENTIAL_PATH_IN_ZIP] = $this->downloadZipGenerationService->buildPathInZip($downloadable, $includeDataPoolFolder); //  serialize($downloadable);
            $batchTask->setPayload($payload);
            $this->batchTaskService->saveBatchTask($batchTask);
        }
    }

    /**
     * @param BatchTask $batchTask
     * @param ProcessChunkMessage $message
     *
     * @throws \Exception
     */
    protected function moveSingleFileToZip(BatchTask $batchTask, ProcessChunkMessage $message)
    {
        $payload = $batchTask->getPayload();

        $this->downloadZipGenerationService->addStreamToZip(
            $this->storageService->openStreamForSingleFileFromStorage($payload[Download::SINGLE_FILE]),
            $payload[Download::SINGLE_FILE_POTENTIAL_PATH_IN_ZIP], $this->getZipId($batchTask)
        );

        $this->storageService->cleanupSingleFileInStorage($payload[Download::SINGLE_FILE]);

        unset($payload[Download::SINGLE_FILE]);
        unset($payload[Download::SINGLE_FILE_POTENTIAL_PATH_IN_ZIP]);
        $batchTask->setPayload($payload);
        $this->batchTaskService->saveBatchTask($batchTask);
    }

    protected function getZipId(BatchTask $batchTask): string
    {
        $payload = $batchTask->getPayload();

        return $payload[Download::ZIP_ID];
    }

    /**
     * @param DownloadableInterface[] $downloadables
     * @param string $zipId
     * @param bool $includeDataPoolFolder
     *
     * @throws \Exception
     */
    protected function addDownloadablesToZip(array $downloadables, BatchTask $batchTask, bool $includeDataPoolFolder)
    {
        $zipId = $this->getZipId($batchTask);

        $bundleStructuredData = $this->downloadZipGenerationService->addDownloadablesToZip($downloadables, $zipId, $includeDataPoolFolder);
        $this->rememberBundleStructuredData($batchTask, $bundleStructuredData);
    }

    /**
     * @param BatchTask $batchTask
     * @param BundleStructuredData[] $bundleStructuredData
     */
    protected function rememberBundleStructuredData(BatchTask $batchTask, array $bundleStructuredData)
    {
        $payload = $batchTask->getPayload();

        $originalBundleStructuredData = isset($payload[Download::BUNDLE_STRUCTURED_DATA])
            ? unserialize($payload[Download::BUNDLE_STRUCTURED_DATA]) : [];

        $bundleStructuredData = array_merge($originalBundleStructuredData, $bundleStructuredData);
        $bundleStructuredData = $this->downloadZipGenerationService->uniqueBundleStructuredData($bundleStructuredData);

        if (sizeof($bundleStructuredData) != sizeof($originalBundleStructuredData)) {
            $payload[Download::BUNDLE_STRUCTURED_DATA] = serialize($bundleStructuredData);
            $batchTask->setPayload($payload);
            $this->batchTaskService->saveBatchTask($batchTask);
        }
    }
}
