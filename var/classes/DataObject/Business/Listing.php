<?php

namespace Pimcore\Model\DataObject\Business;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Business|false current()
 * @method DataObject\Business[] load()
 * @method DataObject\Business[] getData()
 * @method DataObject\Business[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "13";
protected $className = "Business";


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
* Filter by coverView (Featured Item)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCoverView ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("coverView")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by latest (Publish as Latest)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByLatest ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("latest")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by listType (List Type)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByListType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("listType")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by resourceType (Category)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByResourceType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("resourceType")->addListingFilter($this, $data, $operator);
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
