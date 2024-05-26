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

namespace Pimcore\Bundle\PortalEngineBundle\Model\DataObject;

use Symfony\Component\Security\Core\User\UserInterface;

interface PortalUserInterface extends UserInterface
{
    /**
     * @return int|string
     */
    public function getId();

    /**
     * @param string|null $publicShareUserId
     *
     * @return static
     */
    public function setPublicShareUserId(string $publicShareUserId = null);

    /**
     * @return string|null
     */
    public function getPublicShareUserId(): ?string;

    /**
     * @return string|int|null
     */
    public function getPortalUserId();

    /**
     * @return string
     */
    public function getLastname();

    /**
     * @return string
     */
    public function getFirstname();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return int|null
     */
    public function getPimcoreUser();

    /**
     * @return bool|null
     */
    public function getUsePimcoreUserPassword();

    /**
     * @return PortalUserGroupInterface[]|null
     */
    public function getGroups();

    /**
     * @return \Pimcore\Model\DataObject\Data\ElementMetadata[]
     */
    public function getAssetWorkspaceDefinition();

    /**
     * @return \Pimcore\Model\DataObject\Data\ElementMetadata[]
     */
    public function getDataObjectWorkspaceDefinition();

    /**
     * @return bool|null
     */
    public function getAdmin();

    /**
     * @return array|null
     */
    public function getVisibleLanguages();

    /**
     * @return array|null
     */
    public function getEditableLanguages();

    /**
     * @return string|null
     */
    public function getPreferredLanguage();

    /**
     * @param string|null
     */
    public function setPreferredLanguage($preferredLanguage);

    /**
     * @return string|null
     */
    public function getExternalUserId();

    /**
     * @param string|null
     *
     * @return static
     */
    public function setExternalUserId($externalUserId);

    /**
     * @param string $fieldName
     * @param string|null $language
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function get($fieldName, $language = null);

    /**
     * @return bool
     */
    public function isPortalShareUser(): bool;

    /**
     * @param bool $portalShareUser
     *
     * @return static
     */
    public function setPortalShareUser(bool $portalShareUser);

    /**
     * @return \Pimcore\Model\Asset\Image|null
     */
    public function getAvatar();

    /**
     * @param \Pimcore\Model\Asset\Image|null $avatar
     *
     * @return \Pimcore\Model\DataObject\PortalUser
     */
    public function setAvatar($avatar);
}
