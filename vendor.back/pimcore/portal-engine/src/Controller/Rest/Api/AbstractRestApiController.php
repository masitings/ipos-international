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

namespace Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api;

use Pimcore\Bundle\PortalEngineBundle\Controller\AbstractSiteController;
use Pimcore\Bundle\PortalEngineBundle\Service\DataPool\DataPoolConfigService;
use Pimcore\Controller\KernelControllerEventInterface;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Localization\IntlFormatter;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\Document;
use Pimcore\Tool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AbstractRestApiController
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Controller\Rest\Api
 */
abstract class AbstractRestApiController extends AbstractSiteController implements KernelControllerEventInterface
{
    /**
     * @var DataPoolConfigService
     */
    protected $dataPoolConfigService;

    /**
     * @var LocaleServiceInterface
     */
    protected $localeService;

    /**
     * @var IntlFormatter
     */
    protected $intlFormatter;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var DocumentResolver
     */
    protected $documentResolver;

    /**
     * @param ControllerEvent $event
     */
    public function onKernelControllerEvent(ControllerEvent $event)
    {
        parent::onKernelControllerEvent($event);
        $this->setupLocale($event->getRequest());
        $this->setupDocument($event->getRequest());
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function setupLocale(Request $request): bool
    {
        if ($locale = $request->get('_locale')) {
            if (in_array($locale, Tool::getValidLanguages())) {
                $this->localeService->setLocale($locale);
                $this->intlFormatter->setLocale($locale);
                $this->translator->setLocale($locale);

                return true;
            }
        }

        return false;
    }

    /**
     * @param Request $request
     */
    public function setupDocument(Request $request)
    {
        if (!$request->get('documentId')) {
            return;
        }
        if ($document = Document::getById($request->get('documentId'))) {
            if (strpos($document->getRealFullPath(), $this->documentResolver->getDocument()->getRealFullPath()) !== 0) {
                return;
            }

            $this->documentResolver->setDocument($request, $document);
        }
    }

    /**
     * @param DataPoolConfigService $dataPoolConfigService
     * @required
     */
    public function setDataPoolConfigService(DataPoolConfigService $dataPoolConfigService): void
    {
        $this->dataPoolConfigService = $dataPoolConfigService;
    }

    /**
     * @param LocaleServiceInterface $localeServiceö
     * @required
     */
    public function setLocaleService(LocaleServiceInterface $localeService)
    {
        $this->localeService = $localeService;
    }

    /**
     * @param IntlFormatter $intlFormatter
     * @required
     */
    public function setIntlFormatter(IntlFormatter $intlFormatter)
    {
        $this->intlFormatter = $intlFormatter;
    }

    /**
     * @param TranslatorInterface $translator
     * @required
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param DocumentResolver $documentResolver
     * @required
     */
    public function setDocumentResolver(DocumentResolver $documentResolver)
    {
        $this->documentResolver = $documentResolver;
    }
}
