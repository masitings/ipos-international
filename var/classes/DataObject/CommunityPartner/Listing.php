<?php

namespace Pimcore\Model\DataObject\CommunityPartner;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\CommunityPartner|false current()
 * @method DataObject\CommunityPartner[] load()
 * @method DataObject\CommunityPartner[] getData()
 * @method DataObject\CommunityPartner[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "28";
protected $className = "CommunityPartner";


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
