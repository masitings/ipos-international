# External Authentication Services

The portal engine provides several events to make it possible to integrate 
external authentication services via the login form.

* Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginCheckPasswordEvent
* Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginFieldTypeEvent
* Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginGetUsserEvent
* Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginPasswordChangeableEvent

Important: It is needed to create a `PortalUser` data object for all 
external users too to make the portal engine work correctly. 

## Example

This example event subscriber will allow Pimcore admin users to login 
to the portal engine as admin users too. 

```php
<?php

namespace AppBundle\EventListener;

use Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginCheckPasswordEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginFieldTypeEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginGetUserEvent;
use Pimcore\Bundle\PortalEngineBundle\Event\Auth\LoginPasswordChangeableEvent;
use Pimcore\Model\DataObject\PortalUser;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\User;
use Pimcore\Tool\Authentication;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PortalEngineAuthenticationSubscriber implements EventSubscriberInterface
{
    const USER_FOLDER = '/Portal Engine/Users/Pimcore';

    public static function getSubscribedEvents()
    {
        return [
            LoginFieldTypeEvent::class => 'onLoginFieldType',
            LoginGetUserEvent::class => 'onLoginGetUser',
            LoginCheckPasswordEvent::class => 'onLoginCheckPassword',
            LoginPasswordChangeableEvent::class => 'onPasswordChangeable',
        ];
    }

    public function onLoginFieldType(LoginFieldTypeEvent $event)
    {
        //switch the login form to allow non-email user names
        $event->setUseEmailField(false);
    }

    public function onLoginGetUser(LoginGetUserEvent $event)
    {
        $portalUser = null;
        $pimcoreUser = User::getByName($event->getUserName());
        if ($pimcoreUser && $pimcoreUser->getAdmin()) {
            $portalUser = $this->getOrCreatePortalUserByPimcoreUser($pimcoreUser);
            $event->setPortalUser($portalUser);
        }
    }

    public function onLoginCheckPassword(LoginCheckPasswordEvent $event)
    {
        $portalUser = $event->getPortalUser();
        if ($portalUser->getExternalUserId()) {
            $pimcoreUser = User::getByName($portalUser->getExternalUserId());
            $success = Authentication::isValidUser($pimcoreUser)
                && Authentication::verifyPassword(
                    $pimcoreUser,
                    $event->getPassword()
                );

            if ($success) {
                $event->setLoginValid(true);
            }
        }
    }

    public function onPasswordChangeable(LoginPasswordChangeableEvent $event)
    {
        /* If an external authentication service is used it might be needed to disable
         * the recover/change password functionality.
         *
         * In this example this is not necessary.
         */

        //$event->setPasswordChangeable(false);
    }

    protected function getOrCreatePortalUserByPimcoreUser(User $pimcoreUser)
    {
        // to make the portal engine work it is needed to create a portal user data object
        $portalUser = PortalUser::getByExternalUserId($pimcoreUser->getName(), 1);
        if (empty($portalUser)) {
            $portalUser = new PortalUser();
            $portalUser->setPimcoreUser($pimcoreUser->getId())
                ->setExternalUserId($pimcoreUser->getName())
                ->setAdmin(true)
                ->setKey($pimcoreUser->getName())
                ->setEmail($pimcoreUser->getEmail())
                ->setFirstname($pimcoreUser->getFirstname())
                ->setLastname($pimcoreUser->getLastname())
                ->setParent(Service::createFolderByPath(self::USER_FOLDER))
                ->setUsePimcoreUserPassword(true)
                ->setPublished(true)
                ->setKey(Service::getUniqueKey($portalUser))
                ->setPreferredLanguage($pimcoreUser->getLanguage());

            $portalUser->save();
        }

        return $portalUser;
    }
}

```

```yaml
# add this to your container service definition
services:
    AppBundle\EventListener\PortalEngineAuthenticationSubscriber:
        tags:
            - { name: kernel.event_subscriber }
```


