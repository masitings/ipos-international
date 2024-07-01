<?php

use Pimcore\Bundle\CustomReportsBundle\PimcoreCustomReportsBundle;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\TinymceBundle\PimcoreTinymceBundle;

return [
    //Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    PimcoreTinymceBundle::class => ['all' => true],
    PimcoreDataHubBundle::class => ['all' => true],
    PimcoreCustomReportsBundle::class => ['all' => true],
];
