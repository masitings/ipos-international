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

use Pimcore\Bundle\PortalEngineBundle\Document\AbstractAreabrick;
use Pimcore\Bundle\PortalEngineBundle\Service\Security\SecurityService;
use Pimcore\Extension\Document\Areabrick\AreabrickManager;
use Pimcore\Http\Request\Resolver\EditmodeResolver;
use Pimcore\Model\Document\PageSnippet;
use Pimcore\Templating\Renderer\EditableRenderer;
use Pimcore\Translation\Translator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EditmodeExtension extends AbstractExtension
{
    protected $editmodeResolver;
    protected $translator;
    protected $contentExtension;
    protected $editableRenderer;
    protected $areabrickManager;
    protected $securityService;

    public function __construct(
        EditmodeResolver $editmodeResolver,
        Translator $translator,
        ContentExtension $contentExtension,
        EditableRenderer $editableRenderer,
        AreabrickManager $areabrickManager,
        SecurityService $securityService
    ) {
        $this->editmodeResolver = $editmodeResolver;
        $this->translator = $translator;
        $this->contentExtension = $contentExtension;
        $this->editableRenderer = $editableRenderer;
        $this->areabrickManager = $areabrickManager;
        $this->securityService = $securityService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('portalEngine_editmode_classes', [$this, 'getEditmodeClasses']),
            new TwigFunction('portalEngine_editmode_hint_class', [$this, 'getEditmodeHintClass']),
            new TwigFunction('portalEngine_editmode_label', [$this, 'getEditmodeLabel']),
            new TwigFunction('portalEngine_editmode_uniqid', [$this, 'uniqid']),
            new TwigFunction('portalEngine_editmode_icon_store', [$this, 'getIconStore']),
            new TwigFunction('portalEngine_editmode_areablock', [$this, 'renderAreablock'], ['needs_context' => true, 'is_safe' => ['html']]),
            new TwigFunction('portalEngine_editmode_admin_language', [$this, 'getAdminLanguage']),
        ];
    }

    public function getEditmodeClasses(string $classes): ?string
    {
        return $this->editmodeResolver->isEditmode() ? $classes : null;
    }

    public function uniqid()
    {
        return uniqid();
    }

    public function getEditmodeHintClass(): ?string
    {
        return $this->getEditmodeClasses('pee-hint');
    }

    public function getEditmodeLabel(string $label, array $params = [], bool $translate = true): ?string
    {
        return $translate ? $this->translator->trans("portal-engine.{$label}", $params, 'admin', $this->getAdminLanguage()) : $label;
    }

    public function getIconStore()
    {
        $icons = [];

        try {
            foreach (new \DirectoryIterator($this->contentExtension->getIconDirectory()) as $file) {
                if ($file->isDir() || $file->isDot() || $file->getExtension() !== 'svg') {
                    continue;
                }

                $icons[] = [$file->getFilename(), file_get_contents($this->contentExtension->getIconDirectory() . '/' . $file->getFilename())];
            }
        } catch (\Exception $e) {
            // icon directory does not exist
        }

        return $icons;
    }

    public function renderAreablock($context, $name, $place, $options = [])
    {
        $document = $context['document'];
        $editmode = $context['editmode'];

        if (!$document instanceof PageSnippet) {
            return null;
        }

        $options = array_merge([
            'allowed' => [],
            'group' => [],
            'params' => []
        ], $options);

        foreach ($this->areabrickManager->getBricks() as $brick) {
            if (!$brick instanceof AbstractAreabrick) {
                continue;
            }

            if (empty($brick->getAllowedPlaces()) || !in_array($place, $brick->getAllowedPlaces())) {
                continue;
            }

            $options['allowed'][] = $brick->getId();
            $options['group'][$brick->getGroup()][] = $brick->getId();
            $options['params'][$brick->getId()] = [
                'forceEditInView' => $brick->forceEditInView()
            ];
        }

        return $this->editableRenderer->render($document, 'areablock', $name, $options, $editmode);
    }

    public function getAdminLanguage()
    {
        $pimcoreUser = $this->securityService->getPimcoreUser();

        return $pimcoreUser ? $pimcoreUser->getLanguage() : 'en';
    }
}
