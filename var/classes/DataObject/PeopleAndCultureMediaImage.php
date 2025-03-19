<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - image [image]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\PeopleAndCultureMediaImage\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\PeopleAndCultureMediaImage\Listing|\Pimcore\Model\DataObject\PeopleAndCultureMediaImage|null getByImage(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class PeopleAndCultureMediaImage extends Concrete
{
public const FIELD_IMAGE = 'image';

protected $classId = "25";
protected $className = "PeopleAndCultureMediaImage";
protected $image;


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
* Get image - Image
* @return \Pimcore\Model\Asset\Image|null
*/
public function getImage(): ?\Pimcore\Model\Asset\Image
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("image");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->image;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set image - Image
* @param \Pimcore\Model\Asset\Image|null $image
* @return $this
*/
public function setImage(?\Pimcore\Model\Asset\Image $image): static
{
	$this->markFieldDirty("image", true);

	$this->image = $image;

	return $this;
}

}

