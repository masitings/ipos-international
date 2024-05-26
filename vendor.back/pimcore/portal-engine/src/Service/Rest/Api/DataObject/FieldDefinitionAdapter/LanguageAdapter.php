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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\FieldDefinitionAdapter;

use Pimcore\Bundle\PortalEngineBundle\Service\DataObject\LanguageNameService;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\AbstractObject;

/**
 * Class LanguageAdapter
 *
 * @package Pimcore\Bundle\PortalEngineBundle\Service\Rest\Api\DataObject\FieldDefinitionAdapter
 */
class LanguageAdapter extends DefaultAdapter
{
    /** @var LanguageNameService */
    protected $languageNameService;

    /**
     * LanguageAdapter constructor.
     *
     * @param LanguageNameService $languageNameService
     */
    public function __construct(LanguageNameService $languageNameService)
    {
        $this->languageNameService = $languageNameService;
    }

    /**
     *
     * @param AbstractObject $object
     * @param Image $data
     * @param array $params
     *
     * @return array|mixed
     */
    public function getDataForDetail(AbstractObject $object, $data, array $params = [])
    {
        if (empty($data)) {
            return null;
        }

        return [
            'label' => $this->languageNameService->getLanguageName($data),
            'value' => $data,
        ];
    }
}
