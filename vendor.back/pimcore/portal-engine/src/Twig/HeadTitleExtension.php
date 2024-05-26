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

use Pimcore\Bundle\PortalEngineBundle\Service\Content\HeadTitleService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class HeadTitleExtension
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Twig
 */
class HeadTitleExtension extends AbstractExtension
{
    /** @var HeadTitleService */
    protected $headTitleService;

    /**
     * HeadTitleExtension constructor.
     *
     * @param HeadTitleService $headTitleService
     */
    public function __construct(HeadTitleService $headTitleService)
    {
        $this->headTitleService = $headTitleService;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('portalEngine_head_title', [$this, 'getTitle']),
        ];
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->headTitleService->getTitle();
    }
}
