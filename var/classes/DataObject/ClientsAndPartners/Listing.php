<?php

namespace Pimcore\Model\DataObject\ClientsAndPartners;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\ClientsAndPartners|false current()
 * @method DataObject\ClientsAndPartners[] load()
 * @method DataObject\ClientsAndPartners[] getData()
 * @method DataObject\ClientsAndPartners[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "5";
protected $className = "ClientsAndPartners";


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
* Filter by websiteUrl (URL)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByWebsiteUrl ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("websiteUrl")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by DataType (Page Selection)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDataType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("DataType")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by logImage (Logo Image)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByLogImage ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("logImage")->addListingFilter($this, $data, $operator);
	return $this;
}



}
