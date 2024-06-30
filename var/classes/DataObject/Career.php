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
 * - tags [multiselect]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
 * @method static \Pimcore\Model\DataObject\Career\Listing getList(array $config = [])
 * @method static \Pimcore\Model\DataObject\Career\Listing|\Pimcore\Model\DataObject\Career|null getByLocalizedfields(string $field, mixed $value, ?string $locale = null, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
 * @method static \Pimcore\Model\DataObject\Career\Listing|\Pimcore\Model\DataObject\Career|null getByTitle(mixed $value, ?string $locale = null, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
 * @method static \Pimcore\Model\DataObject\Career\Listing|\Pimcore\Model\DataObject\Career|null getByAuthor(mixed $value, ?string $locale = null, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
 * @method static \Pimcore\Model\DataObject\Career\Listing|\Pimcore\Model\DataObject\Career|null getByContent(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
 * @method static \Pimcore\Model\DataObject\Career\Listing|\Pimcore\Model\DataObject\Career|null getByReleaseDate(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
 * @method static \Pimcore\Model\DataObject\Career\Listing|\Pimcore\Model\DataObject\Career|null getByVideoTime(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
 * @method static \Pimcore\Model\DataObject\Career\Listing|\Pimcore\Model\DataObject\Career|null getByGuideTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
 * @method static \Pimcore\Model\DataObject\Career\Listing|\Pimcore\Model\DataObject\Career|null getByInterestedTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
 * @method static \Pimcore\Model\DataObject\Career\Listing|\Pimcore\Model\DataObject\Career|null getByAlsoList(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
 * @method static \Pimcore\Model\DataObject\Career\Listing|\Pimcore\Model\DataObject\Career|null getByIndustryTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
 * @method static \Pimcore\Model\DataObject\Career\Listing|\Pimcore\Model\DataObject\Career|null getByCheckIndustry(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
 * @method static \Pimcore\Model\DataObject\Career\Listing|\Pimcore\Model\DataObject\Career|null getByShares(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
 * @method static \Pimcore\Model\DataObject\Career\Listing|\Pimcore\Model\DataObject\Career|null getByTags(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
 */

class Career extends Concrete
{
    public const FIELD_TITLE = 'title';
    public const FIELD_AUTHOR = 'author';
    public const FIELD_CONTENT = 'content';
    public const FIELD_RELEASE_DATE = 'releaseDate';
    public const FIELD_COVER_IMAGE = 'coverImage';
    public const FIELD_AUTHOR_ICON = 'authorIcon';
    public const FIELD_VIDEO_TIME = 'videoTime';
    public const FIELD_DETAIL_VIDEO = 'detailVideo';
    public const FIELD_MORE_CONTENT = 'moreContent';
    public const FIELD_FILE = 'file';
    public const FIELD_GUIDE_TITLE = 'guideTitle';
    public const FIELD_FULL_GUIDE = 'fullGuide';
    public const FIELD_CHINESE_GUIDE = 'chineseGuide';
    public const FIELD_INTERESTED_TITLE = 'interestedTitle';
    public const FIELD_ALSO_LIST = 'alsoList';
    public const FIELD_INDUSTRY_TITLE = 'IndustryTitle';
    public const FIELD_CHECK_INDUSTRY = 'checkIndustry';
    public const FIELD_SHARES = 'shares';
    public const FIELD_TAGS = 'tags';

    protected $classId = "23";
    protected $className = "Career";
    protected $localizedfields;
    protected $content;
    protected $releaseDate;
    protected $coverImage;
    protected $authorIcon;
    protected $videoTime;
    protected $detailVideo;
    protected $moreContent;
    protected $file;
    protected $guideTitle;
    protected $fullGuide;
    protected $chineseGuide;
    protected $interestedTitle;
    protected $alsoList;
    protected $IndustryTitle;
    protected $checkIndustry;
    protected $shares;
    protected $tags;


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
     * Get localizedfields -
     * @return \Pimcore\Model\DataObject\Localizedfield|null
     */
    public function getLocalizedfields(): ?\Pimcore\Model\DataObject\Localizedfield
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("localizedfields");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->getClass()->getFieldDefinition("localizedfields")->preGetData($this);

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Get title - Title
     * @return string|null
     */
    public function getTitle(?string $language = null): ?string
    {
        $data = $this->getLocalizedfields()->getLocalizedValue("title", $language);
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("title");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Get author - Author
     * @return string|null
     */
    public function getAuthor(?string $language = null): ?string
    {
        $data = $this->getLocalizedfields()->getLocalizedValue("author", $language);
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("author");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set localizedfields -
     * @param \Pimcore\Model\DataObject\Localizedfield|null $localizedfields
     * @return $this
     */
    public function setLocalizedfields(?\Pimcore\Model\DataObject\Localizedfield $localizedfields): static
    {
        $hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
        \Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
        $currentData = $this->getLocalizedfields();
        \Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
        $this->markFieldDirty("localizedfields", true);
        $this->markFieldDirty("localizedfields", true);

        $this->localizedfields = $localizedfields;

        return $this;
    }

    /**
     * Set title - Title
     * @param string|null $title
     * @return $this
     */
    public function setTitle(?string $title, ?string $language = null): static
    {
        $isEqual = false;
        $this->getLocalizedfields()->setLocalizedValue("title", $title, $language, !$isEqual);

        return $this;
    }

    /**
     * Set author - Author
     * @param string|null $author
     * @return $this
     */
    public function setAuthor(?string $author, ?string $language = null): static
    {
        $isEqual = false;
        $this->getLocalizedfields()->setLocalizedValue("author", $author, $language, !$isEqual);

        return $this;
    }

    /**
     * Get content - Content
     * @return string|null
     */
    public function getContent(): ?string
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("content");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->getClass()->getFieldDefinition("content")->preGetData($this);

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set content - Content
     * @param string|null $content
     * @return $this
     */
    public function setContent(?string $content): static
    {
        $this->markFieldDirty("content", true);

        $this->content = $content;

        return $this;
    }

    /**
     * Get releaseDate - Published Date
     * @return \Carbon\Carbon|null
     */
    public function getReleaseDate(): ?\Carbon\Carbon
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("releaseDate");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->releaseDate;

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set releaseDate - Published Date
     * @param \Carbon\Carbon|null $releaseDate
     * @return $this
     */
    public function setReleaseDate(?\Carbon\Carbon $releaseDate): static
    {
        $this->markFieldDirty("releaseDate", true);

        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * Get coverImage - Cover
     * @return \Pimcore\Model\DataObject\Data\Hotspotimage|null
     */
    public function getCoverImage(): ?\Pimcore\Model\DataObject\Data\Hotspotimage
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("coverImage");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->coverImage;

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
    public function setCoverImage(?\Pimcore\Model\DataObject\Data\Hotspotimage $coverImage): static
    {
        $this->markFieldDirty("coverImage", true);

        $this->coverImage = $coverImage;

        return $this;
    }

    /**
     * Get authorIcon - Author
     * @return \Pimcore\Model\DataObject\Data\Hotspotimage|null
     */
    public function getAuthorIcon(): ?\Pimcore\Model\DataObject\Data\Hotspotimage
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("authorIcon");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->authorIcon;

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set authorIcon - Author
     * @param \Pimcore\Model\DataObject\Data\Hotspotimage|null $authorIcon
     * @return $this
     */
    public function setAuthorIcon(?\Pimcore\Model\DataObject\Data\Hotspotimage $authorIcon): static
    {
        $this->markFieldDirty("authorIcon", true);

        $this->authorIcon = $authorIcon;

        return $this;
    }

    /**
     * Get videoTime - Duration
     * @return string|null
     */
    public function getVideoTime(): ?string
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("videoTime");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->videoTime;

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set videoTime - Duration
     * @param string|null $videoTime
     * @return $this
     */
    public function setVideoTime(?string $videoTime): static
    {
        $this->markFieldDirty("videoTime", true);

        $this->videoTime = $videoTime;

        return $this;
    }

    /**
     * Get detailVideo - URL
     * @return \Pimcore\Model\DataObject\Data\Video|null
     */
    public function getDetailVideo(): ?\Pimcore\Model\DataObject\Data\Video
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("detailVideo");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->detailVideo;

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
    public function setDetailVideo(?\Pimcore\Model\DataObject\Data\Video $detailVideo): static
    {
        $this->markFieldDirty("detailVideo", true);

        $this->detailVideo = $detailVideo;

        return $this;
    }

    /**
     * Get moreContent - Want More Great Content
     * @return \Pimcore\Model\DataObject\Data\Link|null
     */
    public function getMoreContent(): ?\Pimcore\Model\DataObject\Data\Link
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("moreContent");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->moreContent;

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set moreContent - Want More Great Content
     * @param \Pimcore\Model\DataObject\Data\Link|null $moreContent
     * @return $this
     */
    public function setMoreContent(?\Pimcore\Model\DataObject\Data\Link $moreContent): static
    {
        $this->markFieldDirty("moreContent", true);

        $this->moreContent = $moreContent;

        return $this;
    }

    /**
     * Get file - Preview of the Guide
     * @return \Pimcore\Model\DataObject\Data\Link|null
     */
    public function getFile(): ?\Pimcore\Model\DataObject\Data\Link
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("file");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->file;

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set file - Preview of the Guide
     * @param \Pimcore\Model\DataObject\Data\Link|null $file
     * @return $this
     */
    public function setFile(?\Pimcore\Model\DataObject\Data\Link $file): static
    {
        $this->markFieldDirty("file", true);

        $this->file = $file;

        return $this;
    }

    /**
     * Get guideTitle - Title
     * @return string|null
     */
    public function getGuideTitle(): ?string
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("guideTitle");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->guideTitle;

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set guideTitle - Title
     * @param string|null $guideTitle
     * @return $this
     */
    public function setGuideTitle(?string $guideTitle): static
    {
        $this->markFieldDirty("guideTitle", true);

        $this->guideTitle = $guideTitle;

        return $this;
    }

    /**
     * Get fullGuide - English Version
     * @return \Pimcore\Model\DataObject\Data\Link|null
     */
    public function getFullGuide(): ?\Pimcore\Model\DataObject\Data\Link
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("fullGuide");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->fullGuide;

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set fullGuide - English Version
     * @param \Pimcore\Model\DataObject\Data\Link|null $fullGuide
     * @return $this
     */
    public function setFullGuide(?\Pimcore\Model\DataObject\Data\Link $fullGuide): static
    {
        $this->markFieldDirty("fullGuide", true);

        $this->fullGuide = $fullGuide;

        return $this;
    }

    /**
     * Get chineseGuide - Chinese Version
     * @return \Pimcore\Model\DataObject\Data\Link|null
     */
    public function getChineseGuide(): ?\Pimcore\Model\DataObject\Data\Link
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("chineseGuide");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->chineseGuide;

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set chineseGuide - Chinese Version
     * @param \Pimcore\Model\DataObject\Data\Link|null $chineseGuide
     * @return $this
     */
    public function setChineseGuide(?\Pimcore\Model\DataObject\Data\Link $chineseGuide): static
    {
        $this->markFieldDirty("chineseGuide", true);

        $this->chineseGuide = $chineseGuide;

        return $this;
    }

    /**
     * Get interestedTitle - Title
     * @return string|null
     */
    public function getInterestedTitle(): ?string
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("interestedTitle");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->interestedTitle;

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set interestedTitle - Title
     * @param string|null $interestedTitle
     * @return $this
     */
    public function setInterestedTitle(?string $interestedTitle): static
    {
        $this->markFieldDirty("interestedTitle", true);

        $this->interestedTitle = $interestedTitle;

        return $this;
    }

    /**
     * Get alsoList - Content
     * @return \Pimcore\Model\DataObject\Career[]
     */
    public function getAlsoList(): array
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("alsoList");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->getClass()->getFieldDefinition("alsoList")->preGetData($this);

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set alsoList - Content
     * @param \Pimcore\Model\DataObject\Career[] $alsoList
     * @return $this
     */
    public function setAlsoList(?array $alsoList): static
    {
        /** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
        $fd = $this->getClass()->getFieldDefinition("alsoList");
        $hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
        \Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
        $currentData = $this->getAlsoList();
        \Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
        $isEqual = $fd->isEqual($currentData, $alsoList);
        if (!$isEqual) {
            $this->markFieldDirty("alsoList", true);
        }
        $this->alsoList = $fd->preSetData($this, $alsoList);
        return $this;
    }

    /**
     * Get IndustryTitle - Description
     * @return string|null
     */
    public function getIndustryTitle(): ?string
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("IndustryTitle");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->IndustryTitle;

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set IndustryTitle - Description
     * @param string|null $IndustryTitle
     * @return $this
     */
    public function setIndustryTitle(?string $IndustryTitle): static
    {
        $this->markFieldDirty("IndustryTitle", true);

        $this->IndustryTitle = $IndustryTitle;

        return $this;
    }

    /**
     * Get checkIndustry - Content
     * @return \Pimcore\Model\DataObject\CaseStudy[]|\Pimcore\Model\DataObject\Course[]|\Pimcore\Model\DataObject\Events[]|\Pimcore\Model\DataObject\News[]|\Pimcore\Model\DataObject\Articles[]|\Pimcore\Model\DataObject\Business[]|\Pimcore\Model\DataObject\PatentAnalytic[]|\Pimcore\Model\DataObject\WebinarRecordings[]|\Pimcore\Model\DataObject\Career[]
     */
    public function getCheckIndustry(): array
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("checkIndustry");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->getClass()->getFieldDefinition("checkIndustry")->preGetData($this);

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set checkIndustry - Content
     * @param \Pimcore\Model\DataObject\CaseStudy[]|\Pimcore\Model\DataObject\Course[]|\Pimcore\Model\DataObject\Events[]|\Pimcore\Model\DataObject\News[]|\Pimcore\Model\DataObject\Articles[]|\Pimcore\Model\DataObject\Business[]|\Pimcore\Model\DataObject\PatentAnalytic[]|\Pimcore\Model\DataObject\WebinarRecordings[]|\Pimcore\Model\DataObject\Career[] $checkIndustry
     * @return $this
     */
    public function setCheckIndustry(?array $checkIndustry): static
    {
        /** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
        $fd = $this->getClass()->getFieldDefinition("checkIndustry");
        $hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
        \Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
        $currentData = $this->getCheckIndustry();
        \Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
        $isEqual = $fd->isEqual($currentData, $checkIndustry);
        if (!$isEqual) {
            $this->markFieldDirty("checkIndustry", true);
        }
        $this->checkIndustry = $fd->preSetData($this, $checkIndustry);
        return $this;
    }

    /**
     * Get shares - Social Share
     * @return \Pimcore\Model\DataObject\Shares[]
     */
    public function getShares(): array
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("shares");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->getClass()->getFieldDefinition("shares")->preGetData($this);

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set shares - Social Share
     * @param \Pimcore\Model\DataObject\Shares[] $shares
     * @return $this
     */
    public function setShares(?array $shares): static
    {
        /** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
        $fd = $this->getClass()->getFieldDefinition("shares");
        $hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
        \Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
        $currentData = $this->getShares();
        \Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
        $isEqual = $fd->isEqual($currentData, $shares);
        if (!$isEqual) {
            $this->markFieldDirty("shares", true);
        }
        $this->shares = $fd->preSetData($this, $shares);
        return $this;
    }

    /**
     * Get tags - Keywords
     * @return string[]|null
     */
    public function getTags(): ?array
    {
        if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
            $preValue = $this->preGetValue("tags");
            if ($preValue !== null) {
                return $preValue;
            }
        }

        $data = $this->tags;

        if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
            return $data->getPlain();
        }

        return $data;
    }

    /**
     * Set tags - Keywords
     * @param string[]|null $tags
     * @return $this
     */
    public function setTags(?array $tags): static
    {
        $this->markFieldDirty("tags", true);

        $this->tags = $tags;

        return $this;
    }
}
