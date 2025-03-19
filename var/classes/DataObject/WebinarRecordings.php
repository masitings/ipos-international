<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - localizedfields [localizedfields]
 * -- title [input]
 * -- author [input]
 * - releaseDate [datetime]
 * - content [wysiwyg]
 * - coverView [checkbox]
 * - latest [checkbox]
 * - resourceType [select]
 * - coverImage [hotspotimage]
 * - authorIcon [hotspotimage]
 * - videoTime [input]
 * - detailVideo [video]
 * - interestedTitle [input]
 * - interestedList [manyToManyObjectRelation]
 * - shares [manyToManyObjectRelation]
 * - file [link]
 * - fullGuide [link]
 * - seoTitle [input]
 * - seoDescription [textarea]
 * - tags [multiselect]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getByLocalizedfields(string $field, mixed $value, ?string $locale = null, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getByTitle(mixed $value, ?string $locale = null, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getByAuthor(mixed $value, ?string $locale = null, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getByReleaseDate(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getByContent(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getByCoverView(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getByLatest(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getByResourceType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getByVideoTime(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getByInterestedTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getByInterestedList(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getByShares(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getBySeoTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getBySeoDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\WebinarRecordings\Listing|\Pimcore\Model\DataObject\WebinarRecordings|null getByTags(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class WebinarRecordings extends Concrete
{
public const FIELD_TITLE = 'title';
public const FIELD_AUTHOR = 'author';
public const FIELD_RELEASE_DATE = 'releaseDate';
public const FIELD_CONTENT = 'content';
public const FIELD_COVER_VIEW = 'coverView';
public const FIELD_LATEST = 'latest';
public const FIELD_RESOURCE_TYPE = 'resourceType';
public const FIELD_COVER_IMAGE = 'coverImage';
public const FIELD_AUTHOR_ICON = 'authorIcon';
public const FIELD_VIDEO_TIME = 'videoTime';
public const FIELD_DETAIL_VIDEO = 'detailVideo';
public const FIELD_INTERESTED_TITLE = 'interestedTitle';
public const FIELD_INTERESTED_LIST = 'interestedList';
public const FIELD_SHARES = 'shares';
public const FIELD_FILE = 'file';
public const FIELD_FULL_GUIDE = 'fullGuide';
public const FIELD_SEO_TITLE = 'seoTitle';
public const FIELD_SEO_DESCRIPTION = 'seoDescription';
public const FIELD_TAGS = 'tags';

protected $classId = "19";
protected $className = "WebinarRecordings";
protected $localizedfields;
protected $releaseDate;
protected $content;
protected $coverView;
protected $latest;
protected $resourceType;
protected $coverImage;
protected $authorIcon;
protected $videoTime;
protected $detailVideo;
protected $interestedTitle;
protected $interestedList;
protected $shares;
protected $file;
protected $fullGuide;
protected $seoTitle;
protected $seoDescription;
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
public function setTitle (?string $title, ?string $language = null): static
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
public function setAuthor (?string $author, ?string $language = null): static
{
	$isEqual = false;
	$this->getLocalizedfields()->setLocalizedValue("author", $author, $language, !$isEqual);

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
* Get coverView - Overview Feature
* @return bool|null
*/
public function getCoverView(): ?bool
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("coverView");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->coverView;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set coverView - Overview Feature
* @param bool|null $coverView
* @return $this
*/
public function setCoverView(?bool $coverView): static
{
	$this->markFieldDirty("coverView", true);

	$this->coverView = $coverView;

	return $this;
}

/**
* Get latest - Publish as Latest
* @return bool|null
*/
public function getLatest(): ?bool
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("latest");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->latest;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set latest - Publish as Latest
* @param bool|null $latest
* @return $this
*/
public function setLatest(?bool $latest): static
{
	$this->markFieldDirty("latest", true);

	$this->latest = $latest;

	return $this;
}

/**
* Get resourceType - Category
* @return string|null
*/
public function getResourceType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("resourceType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->resourceType;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set resourceType - Category
* @param string|null $resourceType
* @return $this
*/
public function setResourceType(?string $resourceType): static
{
	$this->markFieldDirty("resourceType", true);

	$this->resourceType = $resourceType;

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
* Get interestedList - Content
* @return \Pimcore\Model\DataObject\AbstractObject[]
*/
public function getInterestedList(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("interestedList");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("interestedList")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set interestedList - Content
* @param \Pimcore\Model\DataObject\AbstractObject[] $interestedList
* @return $this
*/
public function setInterestedList(?array $interestedList): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("interestedList");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getInterestedList();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $interestedList);
	if (!$isEqual) {
		$this->markFieldDirty("interestedList", true);
	}
	$this->interestedList = $fd->preSetData($this, $interestedList);
	return $this;
}

/**
* Get shares - Social Share
* @return \Pimcore\Model\DataObject\AbstractObject[]
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
* @param \Pimcore\Model\DataObject\AbstractObject[] $shares
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
* Get file - Download
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
* Set file - Download
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
* Get fullGuide - Want More Great Content
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
* Set fullGuide - Want More Great Content
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
* Get seoTitle - Title
* @return string|null
*/
public function getSeoTitle(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("seoTitle");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->seoTitle;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set seoTitle - Title
* @param string|null $seoTitle
* @return $this
*/
public function setSeoTitle(?string $seoTitle): static
{
	$this->markFieldDirty("seoTitle", true);

	$this->seoTitle = $seoTitle;

	return $this;
}

/**
* Get seoDescription - Description
* @return string|null
*/
public function getSeoDescription(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("seoDescription");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->seoDescription;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set seoDescription - Description
* @param string|null $seoDescription
* @return $this
*/
public function setSeoDescription(?string $seoDescription): static
{
	$this->markFieldDirty("seoDescription", true);

	$this->seoDescription = $seoDescription;

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

