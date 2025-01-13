<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - localizedfields [localizedfields]
 * -- title [input]
 * -- author [input]
 * - content [wysiwyg]
 * - releaseDate [datetime]
 * - coverView [checkbox]
 * - latest [checkbox]
 * - listType [select]
 * - resourceType [select]
 * - coverImage [hotspotimage]
 * - authorIcon [hotspotimage]
 * - videoTime [input]
 * - detailVideo [video]
 * - moreContent [link]
 * - file [link]
 * - guideTitle [input]
 * - fullGuide [link]
 * - chineseGuide [link]
 * - interestedTitle [input]
 * - alsoList [manyToManyObjectRelation]
 * - IndustryTitle [input]
 * - checkIndustry [manyToManyObjectRelation]
 * - shares [manyToManyObjectRelation]
 * - seoTitle [input]
 * - seoDescription [textarea]
 * - tags [multiselect]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Business\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByLocalizedfields(string $field, mixed $value, ?string $locale = null, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByTitle(mixed $value, ?string $locale = null, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByAuthor(mixed $value, ?string $locale = null, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByContent(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByReleaseDate(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByCoverView(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByLatest(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByListType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByResourceType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByVideoTime(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByGuideTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByInterestedTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByAlsoList(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByIndustryTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByCheckIndustry(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByShares(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getBySeoTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getBySeoDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Business\Listing|\Pimcore\Model\DataObject\Business|null getByTags(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class PeopleAndCultureMediaVideo extends Concrete
{
    public const FIELD_TITLE = 'videoTitle';
    public const FIELD_DESCRIPTION = 'videoDescription';
    public const FIELD_THUMBNAIL = 'videoThumbnail';
    public const FIELD_VIDEO = 'video';
    protected $classId = "26";
    protected $className = "PeopleAndCultureMediaVideo";
    protected $videoTitle;
    protected $video;
    protected $thumbnail;
    protected $description;
    /**
    * @param array $values
    * @return static
    */
    public static function create(array $values = []): static
    {
        $object = new static();
        $object->setValues($values);
        return $object;
    }

    /**
    * Get Title - Title
    * @return string|null
    */
    public function getTitle(): ?string
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("videoTitle");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->getClass()->getFieldDefinition("videoTitle")->preGetData($this);

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
    * Set Title - Title
    * @param string|null $title
    * @return $this
    */
    public function setTitle(?string $title): static
    {
        $this->markFieldDirty("videoTitle", true);

        $this->videoTitle = $title;

        return $this;
    }

    /**
    * Get detailVideo - URL
    * @return \Pimcore\Model\DataObject\Data\Video|null
    */
    public function getVideo(): ?\Pimcore\Model\DataObject\Data\Video
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("video");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->video;

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
    * Set detailVideo - URL
    * @param \Pimcore\Model\DataObject\Data\Video|null $detailVideo
    * @return $this
    */
    public function setVideo(?\Pimcore\Model\DataObject\Data\Video $detailVideo): static
    {
        $this->markFieldDirty("video", true);

        $this->video = $detailVideo;

        return $this;
    }

    /**
    * Get Title - Title
    * @return string|null
    */
    public function getDescription(): ?string
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("videoDescription");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->getClass()->getFieldDefinition("videoDescription")->preGetData($this);

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
    * Set Title - Title
    * @param string|null $title
    * @return $this
    */
    public function setDescription(?string $desc): static
    {
        $this->markFieldDirty("videoDescription", true);

        $this->description = $desc;

        return $this;
    }

    /**
    * Get coverImage - Cover
    * @return \Pimcore\Model\DataObject\Data\Hotspotimage|null
    */
    public function getThumbnail(): ?\Pimcore\Model\DataObject\Data\Hotspotimage
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("videoThumbnail");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->thumbnail;

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
    * Set coverImage - Cover
    * @param \Pimcore\Model\DataObject\Data\Hotspotimage|null $coverImage
    * @return $this
    */
    public function setThumbnail(?\Pimcore\Model\DataObject\Data\Hotspotimage $coverImage): static
    {
        $this->markFieldDirty("coverImage", true);

        $this->thumbnail = $coverImage;

        return $this;
    }

}
