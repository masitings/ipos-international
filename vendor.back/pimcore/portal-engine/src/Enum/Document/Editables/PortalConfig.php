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

namespace Pimcore\Bundle\PortalEngineBundle\Enum\Document\Editables;

use MyCLabs\Enum\Enum;

class PortalConfig extends Enum
{
    const PORTAL_NAME = 'portal_name';
    const FOOTER_SNIPPET = 'footer_snippet';
    const PUBLIC_FOOTER_SNIPPET = 'public_footer_snippet';
    const LOGO = 'logo';
    const BACKGROUND_IMAGE = 'background_image';
    const EMAIL_IMAGE = 'email_image';

    const COLOR_PRIMARY = 'color_primary';
    const COLOR_SECONDARY = 'color_secondary';
    const COLOR_DARK = 'color_dark';
    const COLOR_LIGHT = 'color_light';

    const BTN_PRIMARY_TEXT_COLOR = 'btn_primary_text_color';
    const BTN_SECONDARY_TEXT_COLOR = 'btn_secondary_text_color';
    const BTN_DARK_TEXT_COLOR = 'btn_dark_text_color';
    const BTN_LIGHT_TEXT_COLOR = 'btn_light_text_color';
    const HEADER_TEXT_COLOR = 'header_text_color';

    const COLOR_HEADER = 'color_header';

    const HEADER_GRADIENTS = 'header_gradients';
    const NAV_ICON_GRADIENTS = 'nav_icon_gradients';
    const MODAL_GRADIENTS = 'modal_gradients';

    const CUSTOMIZED_FRONTEND_BUILD = 'customized_frontend_build';

    const ENABLE_LANGUAGE_REDIRECT = 'enable_language_redirect';

    const COLOR_FIELD_PLACEHOLDERS = [
        self::COLOR_PRIMARY => 'rgba(43,0,128,.999)',
        self::COLOR_SECONDARY => 'rgba(40,40,40,.999)',
        self::COLOR_DARK => 'rgba(40,40,40,.998)',
        self::COLOR_LIGHT => 'hsla(0,0%,100%,.999)',
        self::COLOR_HEADER => 'rgba(43,0,128,.998)',
        self::BTN_PRIMARY_TEXT_COLOR => 'rgba(10,20,30,.999)',
        self::BTN_SECONDARY_TEXT_COLOR => 'rgba(10,20,30,.998)',
        self::BTN_DARK_TEXT_COLOR => 'rgba(10,20,30,.997)',
        self::BTN_LIGHT_TEXT_COLOR => 'rgba(10,20,30,.996)',
        self::HEADER_TEXT_COLOR => 'rgba(10,20,30,.995)',
    ];

    const LUMINANCE_REPLACE = [
        'rgba(30,0,90,.999)' => ['baseColor' => self::COLOR_PRIMARY, 'luminance' => -0.075], //button hover bg
        'rgba(26,0,77,.999)' => ['baseColor' => self::COLOR_PRIMARY, 'luminance' => -0.1], //button hover border
        'rgba(22,0,64,.999)' => ['baseColor' => self::COLOR_PRIMARY, 'luminance' => -0.125], //button active border
        'rgba(21,21,21,.999)' => ['baseColor' => self::COLOR_SECONDARY, 'luminance' => -0.075], //button hover bg
        'rgba(15,15,15,.999)' => ['baseColor' => self::COLOR_SECONDARY, 'luminance' => -0.1], //button hover border
        'rgba(8,8,8,.999)' => ['baseColor' => self::COLOR_SECONDARY, 'luminance' => -0.125], //button active border
        'rgba(21,21,21,.998)' => ['baseColor' => self::COLOR_DARK, 'luminance' => -0.075], //button hover bg
        'rgba(15,15,15,.998)' => ['baseColor' => self::COLOR_DARK, 'luminance' => -0.1], //button hover border
        'rgba(8,8,8,.998)' => ['baseColor' => self::COLOR_DARK, 'luminance' => -0.125], //button active border
        'hsla(0,0%,92.5%,.999)' => ['baseColor' => self::COLOR_LIGHT, 'luminance' => -0.075], //button hover bg
        'hsla(0,0%,90.2%,.999)' => ['baseColor' => self::COLOR_LIGHT, 'luminance' => -0.1], //button hover border
        'hsla(0,0%,87.5%,.999)' => ['baseColor' => self::COLOR_LIGHT, 'luminance' => -0.125], //button active border
        'rgba(9,0,26,.999)' => ['baseColor' => self::COLOR_PRIMARY, 'luminance' => -0.2], //darken($primary, 20%)

    ];

    const COLOR_YIQ_REPLACE = [
        'rgba(51,51,51,.997)' => ['baseColor' => self::COLOR_SECONDARY, 'colorYIQ' => [self::COLOR_DARK, self::COLOR_LIGHT]], //color-yiq($secondary, $dark, $light)
        'rgba(52,52,52,.997)' => ['baseColor' => self::COLOR_SECONDARY, 'colorYIQ' => [self::COLOR_LIGHT, self::COLOR_DARK]], //color-yiq($secondary, $light, $dark)
        'rgba(51,51,51,.3)' => ['baseColor' => self::COLOR_SECONDARY, 'colorYIQ' => [self::COLOR_DARK, self::COLOR_LIGHT], 'rgba' => .3], //rgba(color-yiq($secondary, $dark, $light), .3)
        'rgba(51,51,51,.5)' => ['baseColor' => self::COLOR_SECONDARY, 'colorYIQ' => [self::COLOR_DARK, self::COLOR_LIGHT], 'rgba' => .5], //rgba(color-yiq($secondary, $dark, $light), .5)
        'rgba(51,51,51,.7)' => ['baseColor' => self::COLOR_SECONDARY, 'colorYIQ' => [self::COLOR_DARK, self::COLOR_LIGHT], 'rgba' => .7], //rgba(color-yiq($secondary, $dark, $light), .7)
    ];

    const REMOVE_FROM_CUSTOMIZED_BUILD = [
        self::HEADER_GRADIENTS => 'background:linear-gradient(45deg,rgba(43,0,128,.998),#181818 80%)',
        self::NAV_ICON_GRADIENTS => 'background:linear-gradient(.69deg,rgba(43,0,128,.999),rgba(40,40,40,.998))',
        self::MODAL_GRADIENTS => 'background:linear-gradient(10deg,rgba(43,0,128,.999),#181818)',
    ];
}
