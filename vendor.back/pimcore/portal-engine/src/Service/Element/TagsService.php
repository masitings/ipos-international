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

use Pimcore\Bundle\PortalEngineBundle\Enum\TagsApplyMode;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;
use Pimcore\Model\Element\Tag;

class TagsService
{
    public function assignTagsOnElement(ElementInterface $element, array $tagIds, string $applyMode = TagsApplyMode::ADD)
    {
        if ($applyMode === TagsApplyMode::OVERWRITE) {
            foreach (Tag::getTagsForElement(Service::getType($element), $element->getId()) as $tag) {
                Tag::removeTagFromElement(Service::getType($element), $element->getId(), $tag);
            }
        }

        foreach ($tagIds as $tagId) {
            /** @var Tag|null $tag */
            $tag = Tag::getById($tagId);
            if ($tag && in_array($applyMode, [TagsApplyMode::ADD, TagsApplyMode::OVERWRITE])) {
                Tag::addTagToElement(Service::getType($element), $element->getId(), $tag);
            } elseif ($tag && $applyMode === TagsApplyMode::DELETE) {
                Tag::removeTagFromElement(Service::getType($element), $element->getId(), $tag);
            }
        }
    }
}
