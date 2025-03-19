<?php

namespace Pimcore\Model\DataObject\Accelerator;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\Accelerator|false current()
 * @method DataObject\Accelerator[] load()
 * @method DataObject\Accelerator[] getData()
 * @method DataObject\Accelerator[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "27";
protected $className = "Accelerator";


/**
* Filter by image (Image)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByImage ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("image")->addListingFilter($this, $data, $operator);
	return $this;
}



}
