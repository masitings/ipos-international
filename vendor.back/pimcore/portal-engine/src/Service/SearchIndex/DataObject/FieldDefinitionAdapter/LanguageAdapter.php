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

namespace Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter;

use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\LanguageNameService;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class LanguageAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\SearchIndex\DataObject\FieldDefinitionAdapter
 */
class LanguageAdapter extends SelectAdapter implements FieldDefinitionAdapterInterface
{
    /** @var LanguageNameService */
    protected $languageNameService;

    /**
     * @param LanguageNameService $languageNameService
     * @required
     */
    public function setLanguageNameService(LanguageNameService $languageNameService): void
    {
        $this->languageNameService = $languageNameService;
    }

    /**
     * @param Concrete $object
     *
     * @return string|array
     */
    protected function doGetIndexDataValue($object)
    {
        return $this->languageNameService->getLanguageName($this->doGetRawIndexDataValue($object));
    }
}
