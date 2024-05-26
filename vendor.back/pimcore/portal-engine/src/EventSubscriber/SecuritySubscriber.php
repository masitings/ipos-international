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

use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Service\PortalConfig\PortalConfigService;
use Pimcore\Bundle\PortalEngineBundle\Service\PublicShare\PublicShareService;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Controller\FrontendController;
use Pimcore\Event\AssetEvents;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Tool;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

/**
 * Class IndexUpdateListener
 *
 * @package Pimcore\Bundle\PortalEngineBundle\EventListener
 */
class SecuritySubscriber implements EventSubscriberInterface
{
    /**
     * @var PortalConfigService
     */
    protected $portalConfigService;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var SecurityService
     */
    protected $securityService;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var PublicShareService
     */
    protected $publicShareService;

    protected $publicRoutes = [
        'pimcore_portalengine_auth_login',
        'pimcore_portalengine_auth_recover_password',
        'pimcore_portalengine_public_share_public_list',
        'pimcore_portalengine_rest_api_translation_load_catalogue',
        'pimcore_directedit_downloadfile',
        'pimcore_directedit_renotifybrowser',
        'pimcore_directedit_uploadfile',
        'pimcore_directedit_client_askactivity',
    ];

    protected $publicShareHashRoutes = [
        'pimcore_portalengine_public_share_public_asset_detail',
        'pimcore_portalengine_public_share_public_object_detail',
        'pimcore_portalengine_rest_api_public_share_asset_list',
        'pimcore_portalengine_rest_api_public_share_asset_list_filters',
        'pimcore_portalengine_rest_api_public_share_asset_detail',
        'pimcore_portalengine_rest_api_public_share_asset_detail_results_list',
        'pimcore_portalengine_rest_api_public_share_data_object_list',
        'pimcore_portalengine_rest_api_public_share_data_object_list_filters',
        'pimcore_portalengine_rest_api_public_share_data_object_detail',
        'pimcore_portalengine_rest_api_public_share_data_object_detail_results_list',
        'pimcore_portalengine_rest_api_public_share_download_download_types',
        'pimcore_portalengine_rest_api_batch_task_list',
        'pimcore_portalengine_rest_api_batch_task_delete',
        'pimcore_portalengine_rest_api_batch_task_process_notification_action',
        'pimcore_portalengine_rest_api_asset_download',
        'pimcore_portalengine_rest_api_download_trigger_download',
        'pimcore_portalengine_rest_api_download_get_estimation_result',
        'pimcore_portalengine_rest_api_download_multi_download_trigger_download_estimation',
        'pimcore_portalengine_rest_api_download_single_download',
        'pimcore_portalengine_rest_api_public_share_trigger_download_estimation',
        'pimcore_portalengine_rest_api_public_share_detail_actions',
        'pimcore_portalengine_rest_api_translation_valid_languages',
        'pimcore_portalengine_rest_api_asset_metadata_layout',
    ];

    /**
     * SecuritySubscriber constructor.
     *
     * @param PortalConfigService $portalConfigService
     * @param Security $security
     * @param SecurityService $securityService
     * @param RequestStack $requestStack
     * @param PublicShareService $publicShareService
     */
    public function __construct(PortalConfigService $portalConfigService, Security $security, SecurityService $securityService, RequestStack $requestStack, PublicShareService $publicShareService)
    {
        $this->portalConfigService = $portalConfigService;
        $this->security = $security;
        $this->securityService = $securityService;
        $this->requestStack = $requestStack;
        $this->publicShareService = $publicShareService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => ['onKernelController', 19],
            DataObjectEvents::PRE_UPDATE => 'onPreUpdate',
            AssetEvents::PRE_UPDATE => 'onPreUpdate',
        ];
    }

    /**
     * @param ControllerEvent $controllerEvent
     */
    public function onKernelController(ControllerEvent $controllerEvent)
    {
        if (!$controllerEvent->isMasterRequest()) {
            return;
        }

        if (!$this->portalConfigService->isPortalEngineSite()) {
            return;
        }

        if (!Tool::isFrontend()) {
            return;
        }

        if (!$controllerEvent->getController()[0] instanceof FrontendController) {
            return;
        }

        $route = $controllerEvent->getRequest()->attributes->get('_route');
        $isPublicRoute = in_array(
            $route,
            $this->publicRoutes
        );

        $request = $controllerEvent->getRequest();
        if (in_array($route, $this->publicShareHashRoutes) && $request->get('publicShareHash')) {
            $publicShare = $this->publicShareService->validateByHash($request->get('publicShareHash'));
            $this->publicShareService->setUpPublicShare($publicShare);
        }

        if (!$isPublicRoute && !$this->security->isGranted(Permission::PORTAL_ACCESS)) {
            throw new AuthenticationException('invalid login');
        }
    }

    /**
     * @param DataObjectEvent|AssetEvent $event
     */
    public function onPreUpdate($event)
    {
        if ($this->requestStack->getMasterRequest() && !$this->portalConfigService->isPortalEngineSite()) {
            return;
        }
        if (!$portalUser = $this->securityService->getPortalUser()) {
            return;
        }
        $event->getElement()->setUserModification($this->securityService->getPimcoreUserId());
    }
}
