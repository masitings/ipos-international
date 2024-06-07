<?php

namespace Pimcore\Model\DataObject\OurTeam;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\OurTeam|false current()
 * @method DataObject\OurTeam[] load()
 * @method DataObject\OurTeam[] getData()
 * @method DataObject\OurTeam[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "6";
protected $className = "OurTeam";


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
* Filter by rank (Title)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByRank ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("rank")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by teamType (Category)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTeamType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("teamType")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by link (link)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByLink ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("link")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by individualResume (Bio / Description)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByIndividualResume ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("individualResume")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by profilePhoto (Profile Image)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProfilePhoto ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("profilePhoto")->addListingFilter($this, $data, $operator);
	return $this;
}



}
