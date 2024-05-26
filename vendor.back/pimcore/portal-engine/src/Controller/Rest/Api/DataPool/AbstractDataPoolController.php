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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api\DataPool;

use Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api\AbstractRestApiController;
use Pimcore\Bundle\PortalEngineBundle\Enum\Permission;
use Pimcore\Bundle\PortalEngineBundle\Model\Configuration\DataPool\AbstractDataPoolConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\Document\PrefixService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AbstractRestApiController
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api
 */
abstract class AbstractDataPoolController extends AbstractRestApiController
{
    /**
     * @var AbstractDataPoolConfig|null
     */
    protected $dataPoolConfig;

    /**
     * @var PrefixService
     */
    protected $prefixService;

    /**
     * @param ControllerEvent $event
     */
    public function onKernelControllerEvent(ControllerEvent $event)
    {
        $this->setupDataPoolConfigByRequest($event->getRequest());
        $this->denyAccessUnlessGranted(Permission::DATA_POOL_ACCESS);
        $this->setupLocale($event->getRequest());
        parent::onKernelControllerEvent($event);
    }

    /**
     * @param Request $request
     */
    protected function setupDataPoolConfigByRequest(Request $request)
    {
        $this->dataPoolConfigService->setCurrentDataPoolConfigById((int)$request->get('dataPoolId'));
        $dataPoolConfig = $this->dataPoolConfigService->getCurrentDataPoolConfig();
        $this->dataPoolConfig = $dataPoolConfig;
        $this->prefixService->setupRoutingPrefix(true);
    }

    public function setupLocale(Request $request): bool
    {
        $parentLocaleSet = parent::setupLocale($request);

        if (!$parentLocaleSet) {
            $this->intlFormatter->setLocale($this->dataPoolConfig->getLanguage());
            $this->localeService->setLocale($this->dataPoolConfig->getLanguage());
            $this->translator->setLocale($this->dataPoolConfig->getLanguage());
        }

        return true;
    }

    /**
     * @throws NotFoundHttpException
     */
    abstract public function validateDataPoolConfig();

    /**
     * @param PrefixService $prefixService
     * @required
     */
    public function setPrefixService(PrefixService $prefixService): void
    {
        $this->prefixService = $prefixService;
    }
}
