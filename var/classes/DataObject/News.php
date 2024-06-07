<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - title [input]
 * - author [input]
 * - coverView [checkbox]
 * - releaseDate [datetime]
 * - content [wysiwyg]
 * - coverImage [hotspotimage]
 * - authorImage [hotspotimage]
 * - interestedTitle [input]
 * - InterestedList [manyToManyObjectRelation]
 * - file [link]
 * - seoTitle [input]
 * - seoDescription [textarea]
 * - tags [textarea]
 * - videoTime [input]
 * - detailVideo [video]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\News\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\News\Listing|\Pimcore\Model\DataObject\News|null getByTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\News\Listing|\Pimcore\Model\DataObject\News|null getByAuthor(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\News\Listing|\Pimcore\Model\DataObject\News|null getByCoverView(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\News\Listing|\Pimcore\Model\DataObject\News|null getByReleaseDate(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\News\Listing|\Pimcore\Model\DataObject\News|null getByContent(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\News\Listing|\Pimcore\Model\DataObject\News|null getByInterestedTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\News\Listing|\Pimcore\Model\DataObject\News|null getByInterestedList(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\News\Listing|\Pimcore\Model\DataObject\News|null getBySeoTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\News\Listing|\Pimcore\Model\DataObject\News|null getBySeoDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\News\Listing|\Pimcore\Model\DataObject\News|null getByTags(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\News\Listing|\Pimcore\Model\DataObject\News|null getByVideoTime(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class News extends Concrete
{
public const FIELD_TITLE = 'title';
public const FIELD_AUTHOR = 'author';
public const FIELD_COVER_VIEW = 'coverView';
public const FIELD_RELEASE_DATE = 'releaseDate';
public const FIELD_CONTENT = 'content';
public const FIELD_COVER_IMAGE = 'coverImage';
public const FIELD_AUTHOR_IMAGE = 'authorImage';
public const FIELD_INTERESTED_TITLE = 'interestedTitle';
public const FIELD_INTERESTED_LIST = 'InterestedList';
public const FIELD_FILE = 'file';
public const FIELD_SEO_TITLE = 'seoTitle';
public const FIELD_SEO_DESCRIPTION = 'seoDescription';
public const FIELD_TAGS = 'tags';
public const FIELD_VIDEO_TIME = 'videoTime';
public const FIELD_DETAIL_VIDEO = 'detailVideo';

protected $classId = "12";
protected $className = "News";
protected $title;
protected $author;
protected $coverView;
protected $releaseDate;
protected $content;
protected $coverImage;
protected $authorImage;
protected $interestedTitle;
protected $InterestedList;
protected $file;
protected $seoTitle;
protected $seoDescription;
protected $tags;
protected $videoTime;
protected $detailVideo;


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
* Get title - Title
* @return string|null
*/
public function getTitle(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("title");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->title;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set title - Title
* @param string|null $title
* @return $this
*/
public function setTitle(?string $title): static
{
	$this->markFieldDirty("title", true);

	$this->title = $title;

	return $this;
}

/**
* Get author - Author
* @return string|null
*/
public function getAuthor(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("author");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->author;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set author - Author
* @param string|null $author
* @return $this
*/
public function setAuthor(?string $author): static
{
	$this->markFieldDirty("author", true);

	$this->author = $author;

	return $this;
}

/**
* Get coverView - Featured Item
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
* Set coverView - Featured Item
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
* Get authorImage - Author
* @return \Pimcore\Model\DataObject\Data\Hotspotimage|null
*/
public function getAuthorImage(): ?\Pimcore\Model\DataObject\Data\Hotspotimage
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("authorImage");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->authorImage;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set authorImage - Author
* @param \Pimcore\Model\DataObject\Data\Hotspotimage|null $authorImage
* @return $this
*/
public function setAuthorImage(?\Pimcore\Model\DataObject\Data\Hotspotimage $authorImage): static
{
	$this->markFieldDirty("authorImage", true);

	$this->authorImage = $authorImage;

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
* Get InterestedList - Content
* @return \Pimcore\Model\DataObject\News[]|\Pimcore\Model\DataObject\Events[]|\Pimcore\Model\DataObject\Course[]|\Pimcore\Model\DataObject\Articles[]|\Pimcore\Model\DataObject\Business[]|\Pimcore\Model\DataObject\CaseStudy[]|\Pimcore\Model\DataObject\PatentAnalytic[]|\Pimcore\Model\DataObject\WebinarRecordings[]|\Pimcore\Model\DataObject\Shares[]
*/
public function getInterestedList(): array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("InterestedList");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("InterestedList")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set InterestedList - Content
* @param \Pimcore\Model\DataObject\News[]|\Pimcore\Model\DataObject\Events[]|\Pimcore\Model\DataObject\Course[]|\Pimcore\Model\DataObject\Articles[]|\Pimcore\Model\DataObject\Business[]|\Pimcore\Model\DataObject\CaseStudy[]|\Pimcore\Model\DataObject\PatentAnalytic[]|\Pimcore\Model\DataObject\WebinarRecordings[]|\Pimcore\Model\DataObject\Shares[] $InterestedList
* @return $this
*/
public function setInterestedList(?array $InterestedList): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation $fd */
	$fd = $this->getClass()->getFieldDefinition("InterestedList");
	$hideUnpublished = \Pimcore\Model\DataObject\Concrete::getHideUnpublished();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished(false);
	$currentData = $this->getInterestedList();
	\Pimcore\Model\DataObject\Concrete::setHideUnpublished($hideUnpublished);
	$isEqual = $fd->isEqual($currentData, $InterestedList);
	if (!$isEqual) {
		$this->markFieldDirty("InterestedList", true);
	}
	$this->InterestedList = $fd->preSetData($this, $InterestedList);
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
* @return string|null
*/
public function getTags(): ?string
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
* @param string|null $tags
* @return $this
*/
public function setTags(?string $tags): static
{
	$this->markFieldDirty("tags", true);

	$this->tags = $tags;

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

}

