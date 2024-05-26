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

namespace Pimcore\Bundle\PortalEngineBundle\Service\Element;

use Pimcore\Bundle\PortalEngineBundle\Service\Asset;
use Pimcore\Bundle\PortalEngineBundle\Service\DataObject;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Tool;

class NameExtractorService
{
    /**
     * @var Asset\NameExtractorService
     */
    protected $assetNameExtractor;

    /**
     * @var DataObject\NameExtractorService
     */
    protected $dataObjectNameExtractor;

    /**
     * NameExtractorService constructor.
     *
     * @param Asset\NameExtractorService $assetNameExtractor
     * @param DataObject\NameExtractorService $dataObjectNameExtractor
     */
    public function __construct(
        Asset\NameExtractorService $assetNameExtractor,
        DataObject\NameExtractorService $dataObjectNameExtractor
    ) {
        $this->assetNameExtractor = $assetNameExtractor;
        $this->dataObjectNameExtractor = $dataObjectNameExtractor;
    }

    /**
     * @param ElementInterface $element
     *
     * @return array
     */
    public function extractAllLanguageNames(ElementInterface $element): array
    {
        $result = [];
        foreach (Tool::getValidLanguages() as $language) {
            $result[$language] = $this->extractName($element, $language);
        }

        return $result;
    }

    public function extractName(ElementInterface $element, string $locale = null): string
    {
        if ($element instanceof AbstractObject) {
            return $this->dataObjectNameExtractor->extractName($element, $locale);
        } elseif ($element instanceof \Pimcore\Model\Asset) {
            return $this->assetNameExtractor->extractName($element, $locale);
        }

        return '';
    }
}
