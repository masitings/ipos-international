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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Zip;

use Carbon\Carbon;
use Pimcore\Bundle\PortalEngineBundle\Traits\LoggerAware;
use Pimcore\Tool\Console;

class ZipArchiveService
{
    use LoggerAware;

    const TMP_ZIP_DIRECTORY = PIMCORE_SYSTEM_TEMP_DIRECTORY . '/portal-engine/zip-tmp';

    /**
     * @param string $zipId
     *
     * @return string
     */
    public function getZipFilesytemPathByZipId(string $zipId)
    {
        return $this->getZipFolderFilesystemPath() . '/' . $zipId . '.zip';
    }

    /**
     * @return string
     */
    public function getZipFolderFilesystemPath()
    {
        $folder = self::TMP_ZIP_DIRECTORY;
        $this->createDirectory($folder);

        return $folder;
    }

    /**
     * @param string $zipId
     */
    public function createEmptyZipByZipId(string $zipId)
    {
        $file = $this->getZipFilesytemPathByZipId($zipId);
        file_put_contents($file, base64_decode('UEsFBgAAAAAAAAAAAAAAAAAAAAAAAA=='));
    }

    /**
     * @param string $zipFilePath
     * @param string $filePath
     * @param string $pathWithinZip
     * @param string|null $zipId
     *
     * @throws \Exception
     */
    public function addFileToZip(string $zipFilePath, string $filePath, string $pathWithinZip, string $zipId = null)
    {
        $zipId = $zipId ?? uniqid();

        $tempFileFolder = self::TMP_ZIP_DIRECTORY . '/' . $zipId;
        $path = pathinfo($tempFileFolder . '/' . $pathWithinZip)['dirname'];
        $this->createDirectory($path);

        $pathWithinZip = $this->getUniqueNameInArchive($zipFilePath, $pathWithinZip);

        copy($filePath, $tempFileFolder . '/' . $pathWithinZip);
        $this->moveToZip($zipFilePath, $tempFileFolder, $pathWithinZip);

        $this->removeDirectory($tempFileFolder);
    }

    /**
     * @return $this
     */
    public function cleanupOutdatedZipFiles()
    {
        /** @var Carbon $maxAgeTime */
        $maxAgeTime = Carbon::now()->subHours(24);
        /** @var string $zipFolderFilesystemPath */
        $zipFolderFilesystemPath = $this->getZipFolderFilesystemPath();
        if (is_dir($zipFolderFilesystemPath)) {

            /** @var array|false $filenames */
            $filenames = scandir($zipFolderFilesystemPath);
            if (is_array($filenames)) {
                foreach ($filenames as $filename) {
                    if ('zip' === pathinfo($filename, PATHINFO_EXTENSION)) {

                        /** @var string $fileFilesystemPath */
                        $fileFilesystemPath = $zipFolderFilesystemPath . '/' . $filename;
                        if (Carbon::createFromTimestamp(filemtime($fileFilesystemPath))->lessThan($maxAgeTime)) {
                            @unlink($fileFilesystemPath);
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param string $zipFilePath
     * @param string $pathWithinZip
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function getUniqueNameInArchive(string $zipFilePath, string $pathWithinZip): string
    {
        $baseFileName = null;
        $i = 0;
        while ($fileExistsInZip = $this->fileExistsInArchive($zipFilePath, $pathWithinZip)) {
            $i++;
            $pathinfo = pathinfo($pathWithinZip);
            if (is_null($baseFileName)) {
                $baseFileName = $pathinfo['filename'];
            }

            if ($pathinfo['dirname'] == '.') {
                $pathWithinZip = $baseFileName . '_' . $i . '.' . $pathinfo['extension'];
            } else {
                $pathWithinZip = $pathinfo['dirname'] . '/' . $baseFileName . '_' . $i . '.' . $pathinfo['extension'];
            }
        }

        return $pathWithinZip;
    }

    /**
     * @param string $zipFilePath
     * @param string $tempFileFolder
     * @param string $pathWithinZip
     *
     * @throws \Exception
     */
    protected function moveToZip(string $zipFilePath, string $tempFileFolder, string $pathWithinZip)
    {
        if ($zipBinary = Console::getExecutable('zip')) {
            chdir($tempFileFolder);
            $cmd = "$zipBinary '$zipFilePath' '$pathWithinZip' -m";
            exec($cmd);
            chdir(PIMCORE_PROJECT_ROOT);

            return;
        }

        $this->logger->warning(
            'zip shell binary not found - fallback to php\'s ZipArchive class. Enable the zip binary for a better zip performance.'
        );

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE) === true) {
            $zip->addFile($tempFileFolder . '/' . $pathWithinZip, $pathWithinZip);
            $zip->close();
        }
    }

    /**
     * @param string $zipFilePath
     * @param string $pathWithinZip
     *
     * @return bool
     *
     * @throws \Exception
     */
    protected function fileExistsInArchive(string $zipFilePath, string $pathWithinZip): bool
    {
        if (!file_exists($zipFilePath)) {
            return false;
        }

        $zipinfo = Console::getExecutable('zipinfo');
        $grep = Console::getExecutable('grep');

        if ($zipinfo && $grep) {
            return !empty(shell_exec("$zipinfo -1 '$zipFilePath' | $grep '$pathWithinZip'"));
        }

        $this->logger->warning(
            'zipinfo and/or grep shell binary not found - fallback to php\'s ZipArchive class. Enable these binaries for a better zip performance.'
        );

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE) === true) {
            return $zip->locateName($pathWithinZip) !== false;
        }

        return false;
    }

    /**
     * @param string $path
     */
    protected function removeDirectory(string $path)
    {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }
        rmdir($path);
    }

    /**
     * @param string $path
     */
    protected function createDirectory(string $path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
}
