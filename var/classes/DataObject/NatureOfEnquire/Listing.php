<?php

namespace Pimcore\Model\DataObject\NatureOfEnquire;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\NatureOfEnquire|false current()
 * @method DataObject\NatureOfEnquire[] load()
 * @method DataObject\NatureOfEnquire[] getData()
 * @method DataObject\NatureOfEnquire[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "22";
protected $className = "NatureOfEnquire";


/**
* Filter by state (state)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByState ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("state")->addListingFilter($this, $data, $operator);
	return $this;
}



}
