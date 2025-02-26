<?php

namespace Pimcore\Model\DataObject\Testimony;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Testimony|false current()
 * @method DataObject\Testimony[] load()
 * @method DataObject\Testimony[] getData()
 * @method DataObject\Testimony[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "25";
protected $className = "Testimony";


/**
* Filter by profilePicture (Profile Picture)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProfilePicture ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("profilePicture")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by name (Name)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByName ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("name")->addListingFilter($this, $data, $operator);
	return $this;
}

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
* Filter by testimonies (Testimonies)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTestimonies ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("testimonies")->addListingFilter($this, $data, $operator);
	return $this;
}



}
