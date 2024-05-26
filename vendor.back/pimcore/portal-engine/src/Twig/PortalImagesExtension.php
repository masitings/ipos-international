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

namespace Pimcore\Bundle\PortalEngineBundle\Twig;

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\PortalConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Bundle\PortalEngineBundle\Service\User\AvatarService;
use Pimcore\Http\Request\Resolver\EditmodeResolver;
use Pimcore\Model\Document\Editable\Image;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class PortalImagesExtension
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Twig
 */
class PortalImagesExtension extends AbstractExtension
{
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var \Pimcore\Model\Asset\Image[] */
    protected $storedImages = [];
    protected $portalConfigService;
    protected $editmodeResolver;
    protected $securityService;
    protected $avatarService;

    public function __construct(UrlGeneratorInterface $urlGenerator, PortalConfigService $portalConfigService, EditmodeResolver $editmodeResolver, SecurityService $securityService, AvatarService $avatarService)
    {
        $this->urlGenerator = $urlGenerator;
        $this->portalConfigService = $portalConfigService;
        $this->editmodeResolver = $editmodeResolver;
        $this->securityService = $securityService;
        $this->avatarService = $avatarService;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('portalEngine_images_logo', [$this, 'getLogoImagePath']),
            new TwigFunction('portalEngine_images_background', [$this, 'getBackgroundImagePath']),
            new TwigFunction('portalEngine_images_email', [$this, 'getEmailImagePath']),
            new TwigFunction('portalEngine_images_user', [$this, 'getUserImagePath']),
        ];
    }

    /**
     * @param string|null $thumbnail
     *
     * @return string|array|null
     *
     * @throws \Exception
     */
    public function getLogoImagePath($thumbnail = null)
    {
        return $this->getImagePathFromEditable(PortalConfig::LOGO, '/admin/settings/display-custom-logo?white=true', $thumbnail);
    }

    /**
     * @param string|null $thumbnail
     *
     * @return string|array|null
     *
     * @throws \Exception
     */
    public function getBackgroundImagePath($thumbnail = null)
    {
        return $this->getImagePathFromEditable(PortalConfig::BACKGROUND_IMAGE, '/bundles/pimcoreadmin/img/login/pimconaut-moon.svg', $thumbnail);
    }

    /**
     * @param string|null $thumbnail
     *
     * @return string|array|null
     *
     * @throws \Exception
     */
    public function getEmailImagePath($thumbnail = null)
    {
        $emailImage = $this->getImagePathFromEditable(PortalConfig::EMAIL_IMAGE, '', $thumbnail);
        if (!empty($emailImage)) {
            return $emailImage;
        }

        return $this->getImagePathFromEditable(PortalConfig::LOGO, '/bundles/pimcoreportalengine/img/email-default-logo.jpg', $thumbnail);
    }

    /**
     * @return string
     */
    public function getUserImagePath()
    {
        if ($this->editmodeResolver->isEditmode() || $this->securityService->isAdminPreviewCall()) {
            return '/bundles/pimcoreadmin/img/avatar.png';
        }

        return $this->avatarService->getAvatarPath();
    }

    /**
     * @param string $editable
     * @param string $fallBackImagePath
     * @param string|array|null $thumbnail
     *
     * @return string
     *
     * @throws \Exception
     */
    private function getImagePathFromEditable(string $editable, string $fallBackImagePath, $thumbnail = null)
    {
        /** @var string $imagePath */
        $imagePath = $fallBackImagePath;
        try {
            if (!array_key_exists($editable, $this->storedImages)) {
                $this->storedImages[$editable] = null;

                /** @var Image|null $imageTag */
                $imageTag = $this->portalConfigService->getCurrentPortalConfig()->getDocument()->getEditable($editable);
                if ($imageTag instanceof Image) {
                    $this->storedImages[$editable] = $imageTag->getImage();
                }
            }

            /** @var \Pimcore\Model\Asset\Image|null $image */
            $image = $this->storedImages[$editable];
            if ($image) {
                $imagePath = $thumbnail
                    ? $image->getThumbnail($thumbnail)
                    : $image->getFullPath();
            }
        } catch (\Exception $e) {
        }

        return $imagePath;
    }
}
