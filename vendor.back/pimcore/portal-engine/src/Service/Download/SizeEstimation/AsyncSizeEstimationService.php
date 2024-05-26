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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Download\SizeEstimation;

use Pimcore\Bundle\PortalEngineBundle\Enum\Download\DownloadContext;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadItemInterface;
use Pimcore\Bundle\PortalEngineBundle\Model\Download\DownloadSize;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\Authentication\UserProvider;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\Tool\TmpStore;
use Pimcore\Tool\Console;

class AsyncSizeEstimationService
{
    const DOWNLOAD_ITEMS = 'downloadItems';
    const DOWNLOAD_SIZE = 'downloadSize';
    const USER_ID = 'userId';
    const DOWNLOAD_CONTEXT = 'downloadContext';

    /**
     * @var SizeEstimationStrategyInterface
     */
    protected $sizeEstimationStrategy;
    /**
     * @var SecurityService
     */
    protected $securityService;
    /**
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * @var int
     */
    protected $zipWarningSize;

    /**
     * @var int
     */
    protected $zipRejectSize;

    /**
     * AsyncSizeEstimationService constructor.
     *
     * @param SizeEstimationStrategyInterface $sizeEstimationStrategy
     * @param SecurityService $securityService
     * @param UserProvider $userProvider
     * @param int $zipWarningSize
     * @param int $zipRejectSize
     */
    public function __construct(SizeEstimationStrategyInterface $sizeEstimationStrategy, SecurityService $securityService, UserProvider $userProvider, int $zipWarningSize, int $zipRejectSize)
    {
        $this->sizeEstimationStrategy = $sizeEstimationStrategy;
        $this->securityService = $securityService;
        $this->userProvider = $userProvider;
        $this->zipWarningSize = $zipWarningSize;
        $this->zipRejectSize = $zipRejectSize;
    }

    /**
     * @param DownloadItemInterface $downloadItems
     */
    public function startEstimation(array $downloadItems, string $downloadContext = DownloadContext::UNDEFINED): string
    {
        $uniqId = uniqid();
        $tmpStoreKey = 'download_estimation_' . $uniqId;

        $data = [
            self::DOWNLOAD_ITEMS => $downloadItems,
            self::DOWNLOAD_SIZE => null,
            self::USER_ID => $this->securityService->getPortalUser()->getPortalUserId(),
            self::DOWNLOAD_CONTEXT => $downloadContext
        ];

        TmpStore::add($tmpStoreKey, $data, 'download-estimation', 36000); //10h

        Console::runPhpScriptInBackground(PIMCORE_PROJECT_ROOT . '/bin/console', ['portal-engine:size-estimation', $tmpStoreKey]);

        return $tmpStoreKey;
    }

    /**
     * @param string $tmpStoreKey
     *
     * @return DownloadSize|null|false
     */
    public function getEstimationResult(string $tmpStoreKey)
    {
        $data = $this->getTmpStoreData($tmpStoreKey);

        if (empty($data)) {
            return null;
        }

        if ($data[self::DOWNLOAD_SIZE] instanceof DownloadSize) {
            return $data[self::DOWNLOAD_SIZE];
        }

        return false;
    }

    public function getDownloadItems(string $tmpStoreKey): ?array
    {
        if (!$this->getEstimationResult($tmpStoreKey) instanceof DownloadSize) {
            return null;
        }

        $data = $this->getTmpStoreData($tmpStoreKey);

        return $data[self::DOWNLOAD_ITEMS];
    }

    public function getDownloadContext(string $tmpStoreKey): string
    {
        $data = $this->getTmpStoreData($tmpStoreKey);

        return $data[self::DOWNLOAD_CONTEXT] ?? DownloadContext::UNDEFINED;
    }

    public function cleanupTmpStore(string $tmpStoreKey)
    {
        TmpStore::delete($tmpStoreKey);
    }

    /**
     * @param string $tmpStoreKey
     */
    public function executeEstimate(string $tmpStoreKey)
    {
        $data = $this->getTmpStoreData($tmpStoreKey);
        $this->securityService->setPortalUser($this->userProvider->getById($data[self::USER_ID]));
        if (!($data[self::DOWNLOAD_SIZE] instanceof DownloadSize)) {
            $data[self::DOWNLOAD_SIZE] = $this->sizeEstimationStrategy->estimate($data[self::DOWNLOAD_ITEMS]);
            TmpStore::set($tmpStoreKey, $data);
        }
    }

    /**
     * @param string $tmpStoreKey
     *
     * @return mixed
     */
    protected function getTmpStoreData(string $tmpStoreKey)
    {
        $tmpStore = TmpStore::get($tmpStoreKey);
        if (empty($tmpStore)) {
            return null;
        }

        return $tmpStore->getData();
    }

    /**
     * @return string
     */
    public function getWarningSizeString(): string
    {
        return formatBytes($this->zipWarningSize, 0);
    }

    /**
     * @return string
     */
    public function getRejectSizeString(): string
    {
        return formatBytes($this->zipRejectSize, 0);
    }

    /**
     * @param string $tmpStoreKey
     *
     * @return bool
     */
    public function fileSizeTooBig(string $tmpStoreKey): bool
    {
        $data = $this->getTmpStoreData($tmpStoreKey);

        $downloadSize = $data[self::DOWNLOAD_SIZE];
        if ($downloadSize instanceof DownloadSize) {
            return $downloadSize->toB() > $this->zipRejectSize;
        }

        throw new \RuntimeException('Download size not calculated yet.');
    }

    /**
     * @param string $tmpStoreKey
     *
     * @return bool
     */
    public function fileSizeWarning(string $tmpStoreKey): bool
    {
        $data = $this->getTmpStoreData($tmpStoreKey);
        $downloadSize = $data[self::DOWNLOAD_SIZE];
        if ($downloadSize instanceof DownloadSize) {
            return $downloadSize->toB() > $this->zipWarningSize;
        }

        throw new \RuntimeException('Download size not calculated yet.');
    }
}
