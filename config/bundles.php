<?php

use Pimcore\Bundle\CustomReportsBundle\PimcoreCustomReportsBundle;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\OpenSearchClientBundle\PimcoreOpenSearchClientBundle;
use Pimcore\Bundle\TinymceBundle\PimcoreTinymceBundle;

return [
    //Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    PimcoreTinymceBundle::class => ['all' => true],
    PimcoreDataHubBundle::class => ['all' => true],
    PimcoreCustomReportsBundle::class => ['all' => true],
    Extendmate\Pimcore\LoginTracker\ExtendmateLoginTrackerBundle::class => ['all' => true],
    AdvancedObjectSearchBundle\AdvancedObjectSearchBundle::class => ['all' => true],
    PimcoreOpenSearchClientBundle::class => ['all' => true],
];
