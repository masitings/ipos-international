<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - localizedfields [localizedfields]
 * -- title [input]
 * - Content [wysiwyg]
 * - eventType [select]
 * - level [select]
 * - topic [select]
 * - academyType [select]
 * - venue [select]
 * - venueText [input]
 * - learningType [select]
 * - fee [select]
 * - interestedRegister [link]
 * - registerLinks [block]
 * -- registerUrl [link]
 * - logos [imageGallery]
 * - planing [fieldcollections]
 * - viewUrl [input]
 * - textData [block]
 * -- text [input]
 * - otherInfo [wysiwyg]
 * - lerningObjects [block]
 * -- Text [textarea]
 * - crowdData [block]
 * -- Text [input]
 * - ProgrammeDetails [block]
 * -- title [input]
 * -- Overview [wysiwyg]
 * - CourseFeesData [block]
 * -- title [input]
 * -- feeDetail [wysiwyg]
 * - speakerData [block]
 * -- name [input]
 * -- job [input]
 * -- introduction [textarea]
 * -- detailUrl [input]
 * -- individualResume [wysiwyg]
 * -- profilePhoto [image]
 * - Contact [block]
 * -- contactName [input]
 * -- contactMobile [input]
 * -- contactFax [input]
 * -- contactEmail [input]
 * - manual [link]
 * - coverImage [hotspotimage]
 * - backGround [hotspotimage]
 * - videoTitle [input]
 * - video [video]
 * - Comments [block]
 * -- name [input]
 * -- position [input]
 * -- content [textarea]
 * - interestedTitle [input]
 * - InterestedList [manyToManyObjectRelation]
 * - seoTitle [input]
 * - seoDescription [textarea]
 * - tags [multiselect]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Course\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByLocalizedfields(string $field, mixed $value, ?string $locale = null, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByTitle(mixed $value, ?string $locale = null, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByContent(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByEventType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByLevel(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByTopic(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByAcademyType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByVenue(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByVenueText(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByLearningType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByFee(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByViewUrl(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByOtherInfo(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByVideoTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByInterestedTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByInterestedList(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getBySeoTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getBySeoDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Course\Listing|\Pimcore\Model\DataObject\Course|null getByTags(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Course extends Concrete
{
public const FIELD_TITLE = 'title';
public const FIELD_CONTENT = 'Content';
public const FIELD_EVENT_TYPE = 'eventType';
public const FIELD_LEVEL = 'level';
public const FIELD_TOPIC = 'topic';
public const FIELD_ACADEMY_TYPE = 'academyType';
public const FIELD_VENUE = 'venue';
public const FIELD_VENUE_TEXT = 'venueText';
public const FIELD_LEARNING_TYPE = 'learningType';
public const FIELD_FEE = 'fee';
public const FIELD_INTERESTED_REGISTER = 'interestedRegister';
public const FIELD_REGISTER_LINKS = 'registerLinks';
public const FIELD_LOGOS = 'logos';
public const FIELD_PLANING = 'planing';
public const FIELD_VIEW_URL = 'viewUrl';
public const FIELD_TEXT_DATA = 'textData';
public const FIELD_OTHER_INFO = 'otherInfo';
public const FIELD_LERNING_OBJECTS = 'lerningObjects';
public const FIELD_CROWD_DATA = 'crowdData';
public const FIELD_PROGRAMME_DETAILS = 'ProgrammeDetails';
public const FIELD_COURSE_FEES_DATA = 'CourseFeesData';
public const FIELD_SPEAKER_DATA = 'speakerData';
public const FIELD_CONTACT = 'Contact';
public const FIELD_MANUAL = 'manual';
public const FIELD_COVER_IMAGE = 'coverImage';
public const FIELD_BACK_GROUND = 'backGround';
public const FIELD_VIDEO_TITLE = 'videoTitle';
public const FIELD_VIDEO = 'video';
public const FIELD_COMMENTS = 'Comments';
public const FIELD_INTERESTED_TITLE = 'interestedTitle';
public const FIELD_INTERESTED_LIST = 'InterestedList';
public const FIELD_SEO_TITLE = 'seoTitle';
public const FIELD_SEO_DESCRIPTION = 'seoDescription';
public const FIELD_TAGS = 'tags';

protected $classId = "1";
protected $className = "Course";
protected $localizedfields;
protected $Content;
protected $eventType;
protected $level;
protected $topic;
protected $academyType;
protected $venue;
protected $venueText;
protected $learningType;
protected $fee;
protected $interestedRegister;
protected $registerLinks;
protected $logos;
protected $planing;
protected $viewUrl;
protected $textData;
protected $otherInfo;
protected $lerningObjects;
protected $crowdData;
protected $ProgrammeDetails;
protected $CourseFeesData;
protected $speakerData;
protected $Contact;
protected $manual;
protected $coverImage;
protected $backGround;
protected $videoTitle;
protected $video;
protected $Comments;
protected $interestedTitle;
protected $InterestedList;
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
* Get Content - Content
* @return string|null
*/
public function getContent(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("Content");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("Content")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set Content - Content
* @param string|null $Content
* @return $this
*/
public function setContent(?string $Content): static
{
	$this->markFieldDirty("Content", true);

	$this->Content = $Content;

	return $this;
}

/**
* Get eventType - Type
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
* Set eventType - Type
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
* Get level - Proficiency
* @return string|null
*/
public function getLevel(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("level");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->level;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set level - Proficiency
* @param string|null $level
* @return $this
*/
public function setLevel(?string $level): static
{
	$this->markFieldDirty("level", true);

	$this->level = $level;

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
* Get academyType - Audience
* @return string|null
*/
public function getAcademyType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("academyType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->academyType;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set academyType - Audience
* @param string|null $academyType
* @return $this
*/
public function setAcademyType(?string $academyType): static
{
	$this->markFieldDirty("academyType", true);

	$this->academyType = $academyType;

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
* Get learningType - Class Type
* @return string|null
*/
public function getLearningType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("learningType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->learningType;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set learningType - Class Type
* @param string|null $learningType
* @return $this
*/
public function setLearningType(?string $learningType): static
{
	$this->markFieldDirty("learningType", true);

	$this->learningType = $learningType;

	return $this;
}

/**
* Get fee - Fee
* @return string|null
*/
public function getFee(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("fee");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->fee;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set fee - Fee
* @param string|null $fee
* @return $this
*/
public function setFee(?string $fee): static
{
	$this->markFieldDirty("fee", true);

	$this->fee = $fee;

	return $this;
}

/**
* Get interestedRegister - Register Now CTA
* @return \Pimcore\Model\DataObject\Data\Link|null
*/
public function getInterestedRegister(): ?\Pimcore\Model\DataObject\Data\Link
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("interestedRegister");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->interestedRegister;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set interestedRegister - Register Now CTA
* @param \Pimcore\Model\DataObject\Data\Link|null $interestedRegister
* @return $this
*/
public function setInterestedRegister(?\Pimcore\Model\DataObject\Data\Link $interestedRegister): static
{
	$this->markFieldDirty("interestedRegister", true);

	$this->interestedRegister = $interestedRegister;

	return $this;
}

/**
* Get registerLinks - registerLinks
* @return \Pimcore\Model\DataObject\Data\BlockElement[][]
*/
public function getRegisterLinks(): ?array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("registerLinks");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("registerLinks")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set registerLinks - registerLinks
* @param \Pimcore\Model\DataObject\Data\BlockElement[][] $registerLinks
* @return $this
*/
public function setRegisterLinks(?array $registerLinks): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getClass()->getFieldDefinition("registerLinks");
	$this->registerLinks = $fd->preSetData($this, $registerLinks);
	return $this;
}

/**
* Get logos - Logo Image
* @return \Pimcore\Model\DataObject\Data\ImageGallery|null
*/
public function getLogos(): ?\Pimcore\Model\DataObject\Data\ImageGallery
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("logos");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->logos;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set logos - Logo Image
* @param \Pimcore\Model\DataObject\Data\ImageGallery|null $logos
* @return $this
*/
public function setLogos(?\Pimcore\Model\DataObject\Data\ImageGallery $logos): static
{
	$this->markFieldDirty("logos", true);

	$this->logos = $logos;

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
* Set planing - Programme Dates
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
* Get viewUrl - View Course Fee URL
* @return string|null
*/
public function getViewUrl(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("viewUrl");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->viewUrl;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set viewUrl - View Course Fee URL
* @param string|null $viewUrl
* @return $this
*/
public function setViewUrl(?string $viewUrl): static
{
	$this->markFieldDirty("viewUrl", true);

	$this->viewUrl = $viewUrl;

	return $this;
}

/**
* Get textData - Bullet Point Text Line
* @return \Pimcore\Model\DataObject\Data\BlockElement[][]
*/
public function getTextData(): ?array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("textData");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("textData")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set textData - Bullet Point Text Line
* @param \Pimcore\Model\DataObject\Data\BlockElement[][] $textData
* @return $this
*/
public function setTextData(?array $textData): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getClass()->getFieldDefinition("textData");
	$this->textData = $fd->preSetData($this, $textData);
	return $this;
}

/**
* Get otherInfo - Other Info
* @return string|null
*/
public function getOtherInfo(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("otherInfo");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("otherInfo")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set otherInfo - Other Info
* @param string|null $otherInfo
* @return $this
*/
public function setOtherInfo(?string $otherInfo): static
{
	$this->markFieldDirty("otherInfo", true);

	$this->otherInfo = $otherInfo;

	return $this;
}

/**
* Get lerningObjects - Learning Objectives
* @return \Pimcore\Model\DataObject\Data\BlockElement[][]
*/
public function getLerningObjects(): ?array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("lerningObjects");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("lerningObjects")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set lerningObjects - Learning Objectives
* @param \Pimcore\Model\DataObject\Data\BlockElement[][] $lerningObjects
* @return $this
*/
public function setLerningObjects(?array $lerningObjects): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getClass()->getFieldDefinition("lerningObjects");
	$this->lerningObjects = $fd->preSetData($this, $lerningObjects);
	return $this;
}

/**
* Get crowdData - Who Should Attend
* @return \Pimcore\Model\DataObject\Data\BlockElement[][]
*/
public function getCrowdData(): ?array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("crowdData");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("crowdData")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set crowdData - Who Should Attend
* @param \Pimcore\Model\DataObject\Data\BlockElement[][] $crowdData
* @return $this
*/
public function setCrowdData(?array $crowdData): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getClass()->getFieldDefinition("crowdData");
	$this->crowdData = $fd->preSetData($this, $crowdData);
	return $this;
}

/**
* Get ProgrammeDetails - ProgrammeDetails
* @return \Pimcore\Model\DataObject\Data\BlockElement[][]
*/
public function getProgrammeDetails(): ?array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("ProgrammeDetails");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("ProgrammeDetails")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set ProgrammeDetails - ProgrammeDetails
* @param \Pimcore\Model\DataObject\Data\BlockElement[][] $ProgrammeDetails
* @return $this
*/
public function setProgrammeDetails(?array $ProgrammeDetails): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getClass()->getFieldDefinition("ProgrammeDetails");
	$this->ProgrammeDetails = $fd->preSetData($this, $ProgrammeDetails);
	return $this;
}

/**
* Get CourseFeesData - Course Fee Info
* @return \Pimcore\Model\DataObject\Data\BlockElement[][]
*/
public function getCourseFeesData(): ?array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("CourseFeesData");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("CourseFeesData")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set CourseFeesData - Course Fee Info
* @param \Pimcore\Model\DataObject\Data\BlockElement[][] $CourseFeesData
* @return $this
*/
public function setCourseFeesData(?array $CourseFeesData): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getClass()->getFieldDefinition("CourseFeesData");
	$this->CourseFeesData = $fd->preSetData($this, $CourseFeesData);
	return $this;
}

/**
* Get speakerData - Speaker Information
* @return \Pimcore\Model\DataObject\Data\BlockElement[][]
*/
public function getSpeakerData(): ?array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("speakerData");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("speakerData")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set speakerData - Speaker Information
* @param \Pimcore\Model\DataObject\Data\BlockElement[][] $speakerData
* @return $this
*/
public function setSpeakerData(?array $speakerData): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getClass()->getFieldDefinition("speakerData");
	$this->speakerData = $fd->preSetData($this, $speakerData);
	return $this;
}

/**
* Get Contact - Contact Info
* @return \Pimcore\Model\DataObject\Data\BlockElement[][]
*/
public function getContact(): ?array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("Contact");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("Contact")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set Contact - Contact Info
* @param \Pimcore\Model\DataObject\Data\BlockElement[][] $Contact
* @return $this
*/
public function setContact(?array $Contact): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getClass()->getFieldDefinition("Contact");
	$this->Contact = $fd->preSetData($this, $Contact);
	return $this;
}

/**
* Get manual - Download Brochure
* @return \Pimcore\Model\DataObject\Data\Link|null
*/
public function getManual(): ?\Pimcore\Model\DataObject\Data\Link
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("manual");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->manual;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set manual - Download Brochure
* @param \Pimcore\Model\DataObject\Data\Link|null $manual
* @return $this
*/
public function setManual(?\Pimcore\Model\DataObject\Data\Link $manual): static
{
	$this->markFieldDirty("manual", true);

	$this->manual = $manual;

	return $this;
}

/**
* Get coverImage - Thumbnail
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
* Set coverImage - Thumbnail
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
* Get backGround - Background Image
* @return \Pimcore\Model\DataObject\Data\Hotspotimage|null
*/
public function getBackGround(): ?\Pimcore\Model\DataObject\Data\Hotspotimage
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("backGround");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->backGround;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set backGround - Background Image
* @param \Pimcore\Model\DataObject\Data\Hotspotimage|null $backGround
* @return $this
*/
public function setBackGround(?\Pimcore\Model\DataObject\Data\Hotspotimage $backGround): static
{
	$this->markFieldDirty("backGround", true);

	$this->backGround = $backGround;

	return $this;
}

/**
* Get videoTitle - Title
* @return string|null
*/
public function getVideoTitle(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("videoTitle");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->videoTitle;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set videoTitle - Title
* @param string|null $videoTitle
* @return $this
*/
public function setVideoTitle(?string $videoTitle): static
{
	$this->markFieldDirty("videoTitle", true);

	$this->videoTitle = $videoTitle;

	return $this;
}

/**
* Get video - URL
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
* Set video - URL
* @param \Pimcore\Model\DataObject\Data\Video|null $video
* @return $this
*/
public function setVideo(?\Pimcore\Model\DataObject\Data\Video $video): static
{
	$this->markFieldDirty("video", true);

	$this->video = $video;

	return $this;
}

/**
* Get Comments - See What Others Say
* @return \Pimcore\Model\DataObject\Data\BlockElement[][]
*/
public function getComments(): ?array
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("Comments");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("Comments")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set Comments - See What Others Say
* @param \Pimcore\Model\DataObject\Data\BlockElement[][] $Comments
* @return $this
*/
public function setComments(?array $Comments): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getClass()->getFieldDefinition("Comments");
	$this->Comments = $fd->preSetData($this, $Comments);
	return $this;
}

/**
* Get interestedTitle - You May Also Like
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
* Set interestedTitle - You May Also Like
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
* Get InterestedList - You May Also Like
* @return \Pimcore\Model\DataObject\PatentAnalytic[]|\Pimcore\Model\DataObject\WebinarRecordings[]|\Pimcore\Model\DataObject\CaseStudy[]|\Pimcore\Model\DataObject\Business[]|\Pimcore\Model\DataObject\Articles[]|\Pimcore\Model\DataObject\News[]|\Pimcore\Model\DataObject\Events[]|\Pimcore\Model\DataObject\Course[]
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
* Set InterestedList - You May Also Like
* @param \Pimcore\Model\DataObject\PatentAnalytic[]|\Pimcore\Model\DataObject\WebinarRecordings[]|\Pimcore\Model\DataObject\CaseStudy[]|\Pimcore\Model\DataObject\Business[]|\Pimcore\Model\DataObject\Articles[]|\Pimcore\Model\DataObject\News[]|\Pimcore\Model\DataObject\Events[]|\Pimcore\Model\DataObject\Course[] $InterestedList
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

