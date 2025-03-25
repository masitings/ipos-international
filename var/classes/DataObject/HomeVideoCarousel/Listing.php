<?php

namespace Pimcore\Model\DataObject\HomeVideoCarousel;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\HomeVideoCarousel|false current()
 * @method DataObject\HomeVideoCarousel[] load()
 * @method DataObject\HomeVideoCarousel[] getData()
 * @method DataObject\HomeVideoCarousel[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "40";
protected $className = "HomeVideoCarousel";


/**
* Filter by mediaTitle (Media Title)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMediaTitle ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("mediaTitle")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by mediaSubTitle (Media Sub Title)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMediaSubTitle ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("mediaSubTitle")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by mediaDescription (Media Description)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMediaDescription ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("mediaDescription")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by mediaThumbnail (Media Thumbnail)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMediaThumbnail ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("mediaThumbnail")->addListingFilter($this, $data, $operator);
	return $this;
}



}
