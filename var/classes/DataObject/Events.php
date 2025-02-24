<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - title [input]
 * - eventType [select]
 * - topic [select]
 * - venue [select]
 * - venueText [input]
 * - cost [select]
 * - proficiency [select]
 * - audience [select]
 * - content [wysiwyg]
 * - planing [fieldcollections]
 * - interestedTitle [input]
 * - interestedList [manyToManyObjectRelation]
 * - guestsData [block]
 * -- name [input]
 * -- institution [input]
 * -- position [input]
 * -- detailUrl [input]
 * -- individualResume [wysiwyg]
 * -- profilePhoto [hotspotimage]
 * - coverImage [hotspotimage]
 * - email [input]
 * - regsiterUrl [input]
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
* @method static \Pimcore\Model\DataObject\Events\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByEventType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByTopic(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByVenue(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByVenueText(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByCost(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByProficiency(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByAudience(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByContent(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByInterestedTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByInterestedList(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByEmail(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByRegsiterUrl(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getBySeoTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getBySeoDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByTags(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Events\Listing|\Pimcore\Model\DataObject\Events|null getByVideoTime(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Events extends Concrete
{
public const FIELD_TITLE = 'title';
public const FIELD_EVENT_TYPE = 'eventType';
public const FIELD_TOPIC = 'topic';
public const FIELD_VENUE = 'venue';
public const FIELD_VENUE_TEXT = 'venueText';
public const FIELD_COST = 'cost';
public const FIELD_PROFICIENCY = 'proficiency';
public const FIELD_AUDIENCE = 'audience';
public const FIELD_CONTENT = 'content';
public const FIELD_PLANING = 'planing';
public const FIELD_INTERESTED_TITLE = 'interestedTitle';
public const FIELD_INTERESTED_LIST = 'interestedList';
public const FIELD_GUESTS_DATA = 'guestsData';
public const FIELD_COVER_IMAGE = 'coverImage';
public const FIELD_EMAIL = 'email';
public const FIELD_REGSITER_URL = 'regsiterUrl';
public const FIELD_SEO_TITLE = 'seoTitle';
public const FIELD_SEO_DESCRIPTION = 'seoDescription';
public const FIELD_TAGS = 'tags';
public const FIELD_VIDEO_TIME = 'videoTime';
public const FIELD_DETAIL_VIDEO = 'detailVideo';

protected $classId = "8";
protected $className = "Events";
protected $title;
protected $eventType;
protected $topic;
protected $venue;
protected $venueText;
protected $cost;
protected $proficiency;
protected $audience;
protected $content;
protected $planing;
protected $interestedTitle;
protected $interestedList;
protected $guestsData;
protected $coverImage;
protected $email;
protected $regsiterUrl;
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
* Get eventType - Event Type
* @return string|null
*/
public function getEventType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("eventType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->eventType;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set eventType - Event Type
* @param string|null $eventType
* @return $this
*/
public function setEventType(?string $eventType): static
{
	$this->markFieldDirty("eventType", true);

	$this->eventType = $eventType;

	return $this;
}

/**
* Get topic - Topic
* @return string|null
*/
public function getTopic(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("topic");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->topic;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set topic - Topic
* @param string|null $topic
* @return $this
*/
public function setTopic(?string $topic): static
{
	$this->markFieldDirty("topic", true);

	$this->topic = $topic;

	return $this;
}

/**
* Get venue - Venue
* @return string|null
*/
public function getVenue(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("venue");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->venue;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set venue - Venue
* @param string|null $venue
* @return $this
*/
public function setVenue(?string $venue): static
{
	$this->markFieldDirty("venue", true);

	$this->venue = $venue;

	return $this;
}

/**
* Get venueText - Venue Text
* @return string|null
*/
public function getVenueText(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("venueText");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->venueText;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set venueText - Venue Text
* @param string|null $venueText
* @return $this
*/
public function setVenueText(?string $venueText): static
{
	$this->markFieldDirty("venueText", true);

	$this->venueText = $venueText;

	return $this;
}

/**
* Get cost - Cost
* @return string|null
*/
public function getCost(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("cost");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->cost;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set cost - Cost
* @param string|null $cost
* @return $this
*/
public function setCost(?string $cost): static
{
	$this->markFieldDirty("cost", true);

	$this->cost = $cost;

	return $this;
}

/**
* Get proficiency - Proficiency
* @return string|null
*/
public function getProficiency(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("proficiency");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->proficiency;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set proficiency - Proficiency
* @param string|null $proficiency
* @return $this
*/
public function setProficiency(?string $proficiency): static
{
	$this->markFieldDirty("proficiency", true);

	$this->proficiency = $proficiency;

	return $this;
}

/**
* Get audience - Audience
* @return string|null
*/
public function getAudience(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("audience");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->audience;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set audience - Audience
* @param string|null $audience
* @return $this
*/
public function setAudience(?string $audience): static
{
	$this->markFieldDirty("audience", true);

	$this->audience = $audience;

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
* @return \Pimcore\Model\DataObject\Fieldcollection|null
*/
public function getPlaning(): ?\Pimcore\Model\DataObject\Fieldcollection
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("planing");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("planing")->preGetData($this);
	return $data;
}

/**
* Set planing - Schedule
* @param \Pimcore\Model\DataObject\Fieldcollection|null $planing
* @return $this
*/
public function setPlaning(?\Pimcore\Model\DataObject\Fieldcollection $planing): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections $fd */
	$fd = $this->getClass()->getFieldDefinition("planing");
	$this->planing = $fd->preSetData($this, $planing);
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
* Get interestedList - You May Also Like
* @return \Pimcore\Model\DataObject\WebinarRecordings[]|\Pimcore\Model\DataObject\PatentAnalytic[]|\Pimcore\Model\DataObject\CaseStudy[]|\Pimcore\Model\DataObject\Business[]|\Pimcore\Model\DataObject\Articles[]|\Pimcore\Model\DataObject\News[]|\Pimcore\Model\DataObject\Events[]|\Pimcore\Model\DataObject\CoursesDemand[]|\Pimcore\Model\DataObject\Course[]
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
* Set interestedList - You May Also Like
* @param \Pimcore\Model\DataObject\WebinarRecordings[]|\Pimcore\Model\DataObject\PatentAnalytic[]|\Pimcore\Model\DataObject\CaseStudy[]|\Pimcore\Model\DataObject\Business[]|\Pimcore\Model\DataObject\Articles[]|\Pimcore\Model\DataObject\News[]|\Pimcore\Model\DataObject\Events[]|\Pimcore\Model\DataObject\CoursesDemand[]|\Pimcore\Model\DataObject\Course[] $interestedList
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
* Get guestsData - Speaker/Trainer
* @return \Pimcore\Model\DataObject\Data\BlockElement[][]
*/
public function getGuestsData(): ?array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("guestsData");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("guestsData")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set guestsData - Speaker/Trainer
* @param \Pimcore\Model\DataObject\Data\BlockElement[][] $guestsData
* @return $this
*/
public function setGuestsData(?array $guestsData): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getClass()->getFieldDefinition("guestsData");
	$this->guestsData = $fd->preSetData($this, $guestsData);
	return $this;
}

/**
* Get coverImage - Cover Image
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
* Set coverImage - Cover Image
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
* Get email - Email Address
* @return string|null
*/
public function getEmail(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("email");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->email;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set email - Email Address
* @param string|null $email
* @return $this
*/
public function setEmail(?string $email): static
{
	$this->markFieldDirty("email", true);

	$this->email = $email;

	return $this;
}

/**
* Get regsiterUrl - Registration URL
* @return string|null
*/
public function getRegsiterUrl(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("regsiterUrl");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->regsiterUrl;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set regsiterUrl - Registration URL
* @param string|null $regsiterUrl
* @return $this
*/
public function setRegsiterUrl(?string $regsiterUrl): static
{
	$this->markFieldDirty("regsiterUrl", true);

	$this->regsiterUrl = $regsiterUrl;

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

