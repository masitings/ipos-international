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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Enum;

use Pimcore\Bundle\PortalEngineBundle\Twig\EditmodeExtension;
use Symfony\Contracts\Translation\TranslatorInterface;

class EnumService
{
    protected $translator;
    protected $editmodeExtension;

    public function __construct(TranslatorInterface $translator, EditmodeExtension $editmodeExtension)
    {
        $this->translator = $translator;
        $this->editmodeExtension = $editmodeExtension;
    }

    public function getDocumentSelectStore(string $enumClass, string $translationKeyPrefix)
    {
        $result = [];
        foreach ($enumClass::toArray() as $value) {
            $result[] = [$value, $this->translator->trans($translationKeyPrefix . $value, [], 'admin', $this->editmodeExtension->getAdminLanguage())];
        }

        return $result;
    }
}
