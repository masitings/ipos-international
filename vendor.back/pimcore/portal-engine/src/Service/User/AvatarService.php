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

namespace Pimcore\Bundle\PortalEngineBundle\Service\User;

use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Service;
use Pimcore\Model\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AvatarService
{
    const UPLOADED_AVATAR_FILENAME_PREFIX = 'uploded-avatar_';

    private $baseFolder = '/Portal Engine/Uploaded Avatars';

    /**
     * @var SecurityService
     */
    private $securityService;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /**
     * @param SecurityService $securityService
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(SecurityService $securityService, UrlGeneratorInterface $urlGenerator)
    {
        $this->securityService = $securityService;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws \Exception
     */
    public function updateAvatar(UploadedFile $uploadedFile)
    {
        $this->cleanupExistingUploadedAvatar();
        $avatar = (new Image())
        ->setParent(Service::createFolderByPath($this->baseFolder))
        ->setFilename(
            self::UPLOADED_AVATAR_FILENAME_PREFIX
            .$this->securityService->getPortalUser()->getId()
            .'.'
            .$uploadedFile->getClientOriginalExtension()
        )
        ->setData(file_get_contents($uploadedFile->getRealPath()))
        ->save();

        $this->securityService
            ->getPortalUser()
            ->setAvatar($avatar)
            ->save();
    }

    /**
     * @throws \Exception
     */
    public function deleteAvatar()
    {
        $this->securityService->getPortalUser()->setAvatar(null)->save();
        $this->cleanupExistingUploadedAvatar();
    }

    /**
     * @return string
     */
    public function getAvatarPath(): string
    {
        /** @var string $imageFilePath */
        $imageFilePath = '/bundles/pimcoreadmin/img/avatar.png';

        /** @var Image|null $avatar */
        $avatar = $this->securityService->getPortalUser()->getAvatar();
        if ($avatar) {
            $imageFilePath = $avatar->getThumbnail([
                'width' => 46,
                'height' => 46,
                'cover' => true
            ])->getPath();
        } else {
            /** @var User|null $user */
            $user = User::getById($this->securityService->getPortalUser()->getPimcoreUser());
            if ($user) {
                $imageFilePath = $this->urlGenerator->generate('pimcore_portalengine_user_image');
            }
        }

        return $imageFilePath;
    }

    public function getAvatarStream()
    {
        /** @var Image|null $avatar */
        $avatar = $this->securityService->getPortalUser()->getAvatar();
        if ($avatar) {
            return $avatar->getThumbnail([
                'width' => 46,
                'height' => 46,
                'cover' => true
            ])->getStream();
        } else {
            /** @var User|null $user */
            $user = User::getById($this->securityService->getPortalUser()->getPimcoreUser());
            if ($user) {
                $imageFile = $user->getImage();
                if (is_string($imageFile)) {
                    $imageFile = fopen($imageFile, 'rb');
                }

                return $imageFile;
            }
        }

        return null;
    }

    /**
     * @throws \Exception
     */
    protected function cleanupExistingUploadedAvatar()
    {
        $portalUser = $this->securityService->getPortalUser();
        $list = Image::getList();
        $list->setCondition('concat(path, filename) like ?',
            $this->baseFolder.'/'.self::UPLOADED_AVATAR_FILENAME_PREFIX.$portalUser->getId().'.%'
        );

        foreach ($list as $existingAvatar) {
            if ($existingAvatar instanceof Image) {
                $existingAvatar->delete();
            }
        }
    }
}
