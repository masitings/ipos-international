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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download;

use Pimcore\Model\Tool\TmpStore;

class DownloadTmpStoreService
{
    public function appendToTmpStoreData(string $downloadUniqueId, array $exportData)
    {
        $data = $this->getTmpStoreData($downloadUniqueId);
        $data[] = $exportData;
        $tmpStoreKey = $this->getTmpStoreKey($downloadUniqueId);
        TmpStore::set($tmpStoreKey, $data);
    }

    public function getTmpStoreData(string $downloadUniqueId): array
    {
        $tmpStoreKey = $this->getTmpStoreKey($downloadUniqueId);

        $tmpStore = TmpStore::get($tmpStoreKey);
        if (empty($tmpStore)) {
            return [];
        }

        return $tmpStore->getData();
    }

    public function setTmpStoreData(string $downloadUniqueId, array $data)
    {
        $tmpStoreKey = $this->getTmpStoreKey($downloadUniqueId);
        TmpStore::set($tmpStoreKey, $data);
    }

    public function getTmpStoreKey(string $downloadUniqueId)
    {
        return 'portal-engine-download_' . $downloadUniqueId;
    }

    public function clearTmpSToreData(string $downloadUniqueId)
    {
        $tmpStoreKey = $this->getTmpStoreKey($downloadUniqueId);
        if (TmpStore::get($tmpStoreKey)) {
            TmpStore::delete($tmpStoreKey);
        }
    }
}
