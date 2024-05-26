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

namespace Pimcore\Bundle\PortalEngineBundle\EventSubscriber;

use Pimcore\Bundle\PortalEngineBundle\Model\DataObject\PortalUserInterface;
use Pimcore\Bundle\PortalEngineBundle\Service\Collection\CollectionService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\PortalUser;
use Pimcore\Model\Element\ValidationException;
use Pimcore\Model\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class IndexUpdateListener
 *
 * @package Pimcore\Bundle\PortalEngineBundle\EventListener
 */
class SaveUserSubscriber implements EventSubscriberInterface
{
    const FALLBACK_USER_NAME = 'portal-engine-default-user';

    /**
     * @var CollectionService
     */
    protected $collectionService;
    /**
     * @var PublicShareService
     */
    protected $publicShareService;

    /**
     * SaveUserSubscriber constructor.
     *
     * @param CollectionService $collectionService
     * @param PublicShareService $publicShareService
     */
    public function __construct(CollectionService $collectionService, PublicShareService $publicShareService)
    {
        $this->collectionService = $collectionService;
        $this->publicShareService = $publicShareService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            DataObjectEvents::PRE_UPDATE => 'onPreUpdate',
            DataObjectEvents::POST_DELETE => 'onPostDelete',
        ];
    }

    /**
     * @param DataObjectEvent $dataObjectEvent
     *
     * @throws \Exception
     */
    public function onPreUpdate(DataObjectEvent $dataObjectEvent)
    {
        $user = $dataObjectEvent->getObject();
        if ($user instanceof PortalUserInterface) {

            /** @var bool $existingPortalUser */
            $existingPortalUser = PortalUser::getByEmail($user->getEmail())
                    ->addConditionParam('o_id != ? and externalUserId is null', $user->getId())
                    ->count() > 0;

            if ($existingPortalUser) {
                throw new ValidationException(sprintf('PortalUser with email %s already exists', $user->getEmail()));
            }

            if (empty($user->getPimcoreUser())) {
                $user->setPimcoreUser($this->getFallbackPimcoreUser()->getId());
            }
        }
    }

    /**
     * @param DataObjectEvent $dataObjectEvent
     *
     * @throws \Exception
     */
    public function onPostDelete(DataObjectEvent $dataObjectEvent)
    {
        $user = $dataObjectEvent->getElement();
        if ($user instanceof PortalUserInterface) {
            $this->collectionService->cleanupDeletedUser($user);
            $this->publicShareService->cleanupDeletedUser($user);
        }
    }

    /**
     * @return User
     *
     * @throws \Exception
     */
    protected function getFallbackPimcoreUser(): User
    {
        $user = User::getByName(self::FALLBACK_USER_NAME);

        if (empty($user)) {
            $user = new User();
            $user->setName(self::FALLBACK_USER_NAME);
            $user->setActive(true);
            $user->setParentId(0);
            $user->save();
        }

        return $user;
    }
}
