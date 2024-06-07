<?php

namespace Pimcore\Model\DataObject\Course;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Course|false current()
 * @method DataObject\Course[] load()
 * @method DataObject\Course[] getData()
 * @method DataObject\Course[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "1";
protected $className = "Course";


/**
* Filter by title (Title)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTitle ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("title")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by Content (Content)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByContent ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("Content")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by eventType (Type)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByEventType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("eventType")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by level (Proficiency)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByLevel ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("level")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by topic (Topic)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTopic ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("topic")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by academyType (Audience)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByAcademyType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("academyType")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by venue (Venue)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVenue ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("venue")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by venueText (Venue Text)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVenueText ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("venueText")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by learningType (Class Type)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByLearningType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("learningType")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by fee (Fee)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByFee ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("fee")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by viewUrl (View Course Fee URL)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByViewUrl ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("viewUrl")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by otherInfo (Other Info)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOtherInfo ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("otherInfo")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by videoTitle (Title)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVideoTitle ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("videoTitle")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by interestedTitle (You May Also Like)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByInterestedTitle ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("interestedTitle")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by InterestedList (You May Also Like)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByInterestedList ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("InterestedList")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by seoTitle (Title)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySeoTitle ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("seoTitle")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by seoDescription (Description)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySeoDescription ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("seoDescription")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by tags (Keywords)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTags ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("tags")->addListingFilter($this, $data, $operator);
	return $this;
}



}
