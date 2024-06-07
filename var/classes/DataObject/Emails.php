<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - email [email]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Emails\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Emails\Listing|\Pimcore\Model\DataObject\Emails|null getByEmail(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Emails extends Concrete
{
public const FIELD_EMAIL = 'email';

protected $classId = "21";
protected $className = "Emails";
protected $email;


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
* Get email - Email
* @return string|null
*/
public function getEmail(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("email");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->email;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set email - Email
* @param string|null $email
* @return $this
*/
public function setEmail(?string $email): static
{
	$this->markFieldDirty("email", true);

	$this->email = $email;

	return $this;
}

}

