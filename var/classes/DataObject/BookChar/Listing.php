<?php

namespace Pimcore\Model\DataObject\BookChar;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\BookChar|false current()
 * @method DataObject\BookChar[] load()
 * @method DataObject\BookChar[] getData()
 * @method DataObject\BookChar[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "16";
protected $className = "BookChar";


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
* Filter by chatLogo (Icon)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByChatLogo ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("chatLogo")->addListingFilter($this, $data, $operator);
	return $this;
}



}
