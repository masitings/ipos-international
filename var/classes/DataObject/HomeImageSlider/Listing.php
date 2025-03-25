<?php

namespace Pimcore\Model\DataObject\HomeImageSlider;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\HomeImageSlider|false current()
 * @method DataObject\HomeImageSlider[] load()
 * @method DataObject\HomeImageSlider[] getData()
 * @method DataObject\HomeImageSlider[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "39";
protected $className = "HomeImageSlider";


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
