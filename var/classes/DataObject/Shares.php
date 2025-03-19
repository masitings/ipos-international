<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - share [link]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Shares\Listing getList(array $config = [])
*/

class Shares extends Concrete
{
public const FIELD_SHARE = 'share';

protected $classId = "14";
protected $className = "Shares";
protected $share;


/**
* @param array $values
* @return static
*/
public static function create(array $values = []): static
{
	$object = new static();
	$object->setValues($values);
	return $object;
}

/**
* Get share - Social Share
* @return \Pimcore\Model\DataObject\Data\Link|null
*/
public function getShare(): ?\Pimcore\Model\DataObject\Data\Link
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("share");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->share;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set share - Social Share
* @param \Pimcore\Model\DataObject\Data\Link|null $share
* @return $this
*/
public function setShare(?\Pimcore\Model\DataObject\Data\Link $share): static
{
	$this->markFieldDirty("share", true);

	$this->share = $share;

	return $this;
}

}

