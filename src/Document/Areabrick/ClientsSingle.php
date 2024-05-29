<?php
// src/Document/Areabrick/Clients.php

namespace App\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;

class ClientsSingle extends AbstractTemplateAreabrick
{
    public function getName(): string
    {
        return 'ClientsSingle';
    }

    public function getDescription(): string
    {
        return 'Clients Single';
    }

    public function getTemplateLocation(): string
    {
        return static::TEMPLATE_LOCATION_GLOBAL;
    }

    public function needsReload(): bool
    {
        // optional
        // here you can decide whether adding this bricks should trigger a reload
        // in the editing interface, this could be necessary in some cases. default=false
        return false;
    }
    /**
     * {@inheritdoc}
     */
    public function getHtmlTagOpen(Info $info): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlTagClose(Info $info): string
    {
        return '';
    }
}
