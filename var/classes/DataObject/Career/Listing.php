<?php

namespace Pimcore\Model\DataObject\Career;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Career|false current()
 * @method DataObject\Career[] load()
 * @method DataObject\Career[] getData()
 * @method DataObject\Career[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "23";
protected $className = "Career";


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
* Filter by author (Author)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByAuthor ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("author")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by content (Content)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByContent ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("content")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by releaseDate (Published Date)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByReleaseDate ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("releaseDate")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by videoTime (Duration)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVideoTime ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("videoTime")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by guideTitle (Title)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByGuideTitle ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("guideTitle")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by interestedTitle (Title)
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
* Filter by alsoList (Content)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByAlsoList ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("alsoList")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by IndustryTitle (Description)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByIndustryTitle ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("IndustryTitle")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by checkIndustry (Content)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCheckIndustry ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("checkIndustry")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by shares (Social Share)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByShares ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("shares")->addListingFilter($this, $data, $operator);
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
