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

namespace Pimcore\Bundle\StatisticsExplorerBundle\StatisticsStorageAdapter\Worker;

use Pimcore\Bundle\StatisticsExplorerBundle\PimcoreStatisticsExplorerBundle;
use Pimcore\Translation\Translator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TranslationAwareWorker extends EventDispatcherAwareWorker
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param Translator $translator
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, Translator $translator)
    {
        parent::__construct($eventDispatcher);
        $this->translator = $translator;
    }

    protected function translate($key)
    {
        return $this->translator->trans(PimcoreStatisticsExplorerBundle::TRANSLATION_PREFIX . $key);
    }
}
