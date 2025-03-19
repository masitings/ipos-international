<?php

namespace Pimcore\Model\DataObject\IPStartMediaCarouselImage;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\IPStartMediaCarouselImage|false current()
 * @method DataObject\IPStartMediaCarouselImage[] load()
 * @method DataObject\IPStartMediaCarouselImage[] getData()
 * @method DataObject\IPStartMediaCarouselImage[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "34";
protected $className = "IPStartMediaCarouselImage";


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
* Filter by mediaDescription1 (Media Description 1)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMediaDescription1 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("mediaDescription1")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by mediaDescription2 (Media Description 2)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMediaDescription2 ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("mediaDescription2")->addListingFilter($this, $data, $operator);
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
