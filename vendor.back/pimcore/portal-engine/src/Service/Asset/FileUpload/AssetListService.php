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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Asset\FileUpload;

use Pimcore\Bundle\PortalEngineBundle\Enum\Index\DatabaseConfig;
use Pimcore\Bundle\PortalEngineBundle\Event\Asset\Upload\PostUploadEvent;
use Pimcore\Bundle\PortalEngineBundle\Exception\Asset\FileUploadException;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\Asset\Upload\AssetUploadList;
use Pimcore\Bundle\PortalEngineBundle\Model\Rest\Api\Asset\Upload\AssetUploadListEntry;
use Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\IndexQueueService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\Asset;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class AssetListService
 */
class AssetListService
{
    const SESSION_NAME_PREFIX = 'asset_list';

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var SecurityService */
    protected $securityService;
    /** @var IndexQueueService */
    protected $indexQueueService;
    /** @var SessionInterface */
    protected $session;

    /**
     * AssetListService constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param SecurityService $securityService
     * @param IndexQueueService $indexQueueService
     * @param SessionInterface $session
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, SecurityService $securityService, IndexQueueService $indexQueueService, SessionInterface $session)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->securityService = $securityService;
        $this->indexQueueService = $indexQueueService;
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function createList()
    {
        /** @var string $assetListId */
        $assetListId = $this->getUniqueAssetListId();

        $this->session->set($assetListId, (new AssetUploadList()));

        return $assetListId;
    }

    /**
     * @return string
     */
    protected function getUniqueAssetListId()
    {
        /** @var string $assetListId */
        $assetListId = static::SESSION_NAME_PREFIX . '_' . $this->securityService->getPortalUser()->getId() . '_' . uniqid();

        while ($this->session->has($assetListId)) {
            $assetListId = $this->getUniqueAssetListId();
        }

        return $assetListId;
    }

    /**
     * @param string $assetListId
     * @param bool $forceCreate
     *
     * @return AssetUploadList|null
     */
    public function getListById(string $assetListId, $forceCreate = false)
    {
        /** @var AssetUploadList|null $assetUploadList */
        $assetUploadList = null;

        if ($this->session->has($assetListId)) {
            $assetUploadList = $this->session->get($assetListId);
        }
        if (!$assetUploadList && $forceCreate) {
            $assetUploadList = new AssetUploadList();
            $this->session->set($assetListId, $assetUploadList);
        }

        return $assetUploadList;
    }

    /**
     * @param string $id
     *
     * @return AssetUploadList|null
     */
    public function getFinalAssetUploadListById(string $id)
    {
        /** @var AssetUploadList|null $assetUploadList */
        $assetUploadList = $this->getListById($id);
        if ($assetUploadList) {

            /** @var PostUploadEvent $postUploadEvent */
            $postUploadEvent = new PostUploadEvent($assetUploadList);
            $this->eventDispatcher->dispatch($postUploadEvent);
        }

        return $assetUploadList;
    }

    /**
     * @param string $assetListId
     *
     * @return $this
     */
    public function deleteListById(string $assetListId)
    {
        if ($this->session->has($assetListId)) {
            $this->session->remove($assetListId);
        }

        return $this;
    }

    /**
     * @param string $assetListId
     * @param AssetUploadListEntry $assetUploadListEntry
     *
     * @return $this
     */
    public function addEntry(string $assetListId, AssetUploadListEntry $assetUploadListEntry)
    {
        /** @var AssetUploadList $assetUploadList */
        $assetUploadList = $this->getListById($assetListId, true);
        $assetUploadList->addEntry($assetUploadListEntry);

        $this->session->set($assetListId, $assetUploadList);

        return $this;
    }

    /**
     * @param string $assetListId
     * @param Asset\Folder $folder
     *
     * @return $this
     */
    public function addAssetFolder(string $assetListId, Asset\Folder $folder)
    {
        /** @var AssetUploadList $assetUploadList */
        $assetUploadList = $this->getListById($assetListId, true);
        $assetUploadList->addAssetFolder($folder);

        $this->session->set($assetListId, $assetUploadList);

        return $this;
    }

    /**
     * @param string $assetListId
     * @param FileUploadException $exception
     *
     * @return AssetUploadListEntry
     */
    public function addException(string $assetListId, FileUploadException $exception)
    {
        /** @var AssetUploadListEntry $assetUploadListEntry */
        $assetUploadListEntry = (new AssetUploadListEntry())
            ->setName($exception->getFilename())
            ->setMessage($exception->getMessage());

        /** @var AssetUploadList $assetUploadList */
        $assetUploadList = $this->getListById($assetListId, true);
        $assetUploadList->addEntry($assetUploadListEntry);

        $this->session->set($assetListId, $assetUploadList);

        return $assetUploadListEntry;
    }

    /**
     * @param string $assetListId
     * @param string[] $messages
     *
     * @return $this
     */
    public function addGlobalMessages(string $assetListId, array $messages = [])
    {
        /** @var AssetUploadList $assetUploadList */
        $assetUploadList = $this->getListById($assetListId);
        if ($assetUploadList && !empty($messages)) {
            $assetUploadList->setMessages(array_merge($assetUploadList->getMessages(), $messages));

            $this->session->set($assetListId, $assetUploadList);
        }

        return $this;
    }

    /**
     * @param string $assetListId
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function rollbackList(string $assetListId)
    {
        /** @var AssetUploadList $assetUploadList */
        $assetUploadList = $this->getListById($assetListId);
        if ($assetUploadList) {

            /** @var AssetUploadListEntry[] $assetUploadListEntries */
            $assetUploadListEntries = [];
            foreach ($assetUploadList->getEntries() as $assetUploadListEntry) {
                if ($assetUploadListEntry->getAssetId()) {
                    /** @var Asset|null $asset */
                    $asset = Asset::getById($assetUploadListEntry->getAssetId());
                    if ($asset) {
                        $this->indexQueueService->updateIndexQueue($asset, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_DELETE, true);
                        $asset->delete();
                    }
                }

                $assetUploadListEntries[] = $assetUploadListEntry
                    ->setAssetId(null)
                    ->setFullPath(null)
                    ->setDetailLink(null);
            }

            $assetUploadList
                ->setEntries($assetUploadListEntries)
                ->setAddEntryAllowed(false);

            $this->rollbackAssetFolders($assetListId);

            $this->session->set($assetListId, $assetUploadList);
        }

        return $this;
    }

    /**
     * @param string $assetListId
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function rollbackListEntry(string $assetListId, AssetUploadListEntry $currentAssetUploadListEntry)
    {
        if ($currentAssetUploadListEntry->getAssetId()) {
            /** @var AssetUploadList $assetUploadList */
            $assetUploadList = $this->getListById($assetListId);
            if ($assetUploadList) {

                /** @var AssetUploadListEntry[] $assetUploadListEntries */
                $assetUploadListEntries = [];
                foreach ($assetUploadList->getEntries() as $assetUploadListEntry) {
                    if ($assetUploadListEntry->getAssetId() == $currentAssetUploadListEntry->getAssetId()) {

                        /** @var Asset|null $asset */
                        $asset = Asset::getById($assetUploadListEntry->getAssetId());
                        if ($asset) {
                            $this->indexQueueService->updateIndexQueue($asset, DatabaseConfig::QUEUE_TABLE_COLUMN_OPERATION_DELETE, true);
                            $asset->delete();
                        }

                        $assetUploadListEntry
                            ->setAssetId(null)
                            ->setFullPath(null)
                            ->setDetailLink(null);
                    }

                    $assetUploadListEntries[] = $assetUploadListEntry;
                }

                $assetUploadList->setEntries($assetUploadListEntries);

                $this->rollbackAssetFolders($assetListId);

                $this->session->set($assetListId, $assetUploadList);
            }
        }

        return $this;
    }

    /**
     * @param string $assetListId
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function rollbackAssetFolders(string $assetListId)
    {
        /** @var AssetUploadList $assetUploadList */
        $assetUploadList = $this->getListById($assetListId);
        if ($assetUploadList) {

            /** @var int[] $assetFolderIds */
            $assetFolderIds = $assetUploadList->getAssetFolderIds();
            // reverse folders to delete them recursive from child to parent
            $assetFolderIds = array_reverse($assetFolderIds);

            foreach ($assetFolderIds as $assetFolderId) {
                /** @var Asset\Folder $assetFolder */
                $assetFolder = Asset\Folder::getById($assetFolderId);
                if ($assetFolder && !$assetFolder->hasChildren()) {
                    $assetFolder->delete();
                }
            }
        }

        return $this;
    }
}
