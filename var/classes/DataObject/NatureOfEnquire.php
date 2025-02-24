<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - state [select]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\NatureOfEnquire\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\NatureOfEnquire\Listing|\Pimcore\Model\DataObject\NatureOfEnquire|null getByState(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class NatureOfEnquire extends Concrete
{
public const FIELD_STATE = 'state';

protected $classId = "22";
protected $className = "NatureOfEnquire";
protected $state;


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
* Get state - state
* @return string|null
*/
public function getState(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("state");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->state;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set state - state
* @param string|null $state
* @return $this
*/
public function setState(?string $state): static
{
	$this->markFieldDirty("state", true);

	$this->state = $state;

	return $this;
}

}

