<?php

namespace Pimcore\Model\DataObject\Consultant;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Consultant|false current()
 * @method DataObject\Consultant[] load()
 * @method DataObject\Consultant[] getData()
 * @method DataObject\Consultant[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "10";
protected $className = "Consultant";


/**
* Filter by name (name)
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
* Filter by job (job)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByJob ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("job")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by rank (rank)
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
* Filter by position (position)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPosition ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("position")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by company ( company)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCompany ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("company")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by detailUrl (detailUrl)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDetailUrl ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("detailUrl")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by profilePhoto (profilePhoto)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProfilePhoto ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("profilePhoto")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by individualResume (individualResume)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByIndividualResume ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("individualResume")->addListingFilter($this, $data, $operator);
	return $this;
}



}
