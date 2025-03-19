<?php

namespace Pimcore\Model\DataObject\PeopleAndCultureMediaVideo;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\PeopleAndCultureMediaVideo|false current()
 * @method DataObject\PeopleAndCultureMediaVideo[] load()
 * @method DataObject\PeopleAndCultureMediaVideo[] getData()
 * @method DataObject\PeopleAndCultureMediaVideo[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "26";
protected $className = "PeopleAndCultureMediaVideo";


/**
* Filter by videoTitle (Video Title)
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
* Filter by videoDescription (Video Description)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVideoDescription ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("videoDescription")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by videoThumbnail (Video Thumbnail)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVideoThumbnail ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("videoThumbnail")->addListingFilter($this, $data, $operator);
	return $this;
}



}
