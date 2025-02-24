<?php

namespace Pimcore\Model\DataObject\Emails;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Emails|false current()
 * @method DataObject\Emails[] load()
 * @method DataObject\Emails[] getData()
 * @method DataObject\Emails[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "21";
protected $className = "Emails";


/**
* Filter by email (Email)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByEmail ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("email")->addListingFilter($this, $data, $operator);
	return $this;
}



}
