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

use Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables\PortalConfig;
use Pimcore\Bundle\PortalEngineBundle\Service\Document\LanguageVariantService;
use Pimcore\Db;
use Pimcore\Event\DocumentEvents;
use Pimcore\Event\Model\DocumentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DocumentCacheClearSubscriber
 *
 * @package Pimcore\Bundle\PortalEngineBundle\EventListener
 */
class DocumentCacheClearSubscriber implements EventSubscriberInterface
{
    /**
     * @var LanguageVariantService
     */
    protected $languageVariantService;

    /**
     * DocumentCacheClearSubscriber constructor.
     *
     * @param LanguageVariantService $languageVariantService
     */
    public function __construct(LanguageVariantService $languageVariantService)
    {
        $this->languageVariantService = $languageVariantService;
    }

    public static function getSubscribedEvents()
    {
        return [
            DocumentEvents::POST_UPDATE => 'onDocumentSave',
        ];
    }

    /**
     * @param DocumentEvent $event
     *
     * @throws \Exception
     */
    public function onDocumentSave(DocumentEvent $event)
    {
        $document = $event->getDocument();

        if ($this->languageVariantService->isLanguageVariantDocument($document)) {
            $this->languageVariantService->clearCache();
        }

        $parentLanguageRedirectEnabled = (bool) Db::get()->fetchOne('select data from documents_editables where name = ? and documentId = ? limit 1', [
            PortalConfig::ENABLE_LANGUAGE_REDIRECT,
            $document->getParentId()
        ]);

        if ($parentLanguageRedirectEnabled) {
            $this->languageVariantService->clearCache();
        }
    }
}
