<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - profilePicture [image]
 * - name [input]
 * - title [input]
 * - testimonies [input]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Testimony\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Testimony\Listing|\Pimcore\Model\DataObject\Testimony|null getByProfilePicture(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Testimony\Listing|\Pimcore\Model\DataObject\Testimony|null getByName(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Testimony\Listing|\Pimcore\Model\DataObject\Testimony|null getByTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Testimony\Listing|\Pimcore\Model\DataObject\Testimony|null getByTestimonies(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Testimony extends Concrete
{
public const FIELD_PROFILE_PICTURE = 'profilePicture';
public const FIELD_NAME = 'name';
public const FIELD_TITLE = 'title';
public const FIELD_TESTIMONIES = 'testimonies';

protected $classId = "25";
protected $className = "Testimony";
protected $profilePicture;
protected $name;
protected $title;
protected $testimonies;


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
* Get profilePicture - Profile Picture
* @return \Pimcore\Model\Asset\Image|null
*/
public function getProfilePicture(): ?\Pimcore\Model\Asset\Image
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("profilePicture");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->profilePicture;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set profilePicture - Profile Picture
* @param \Pimcore\Model\Asset\Image|null $profilePicture
* @return $this
*/
public function setProfilePicture(?\Pimcore\Model\Asset\Image $profilePicture): static
{
	$this->markFieldDirty("profilePicture", true);

	$this->profilePicture = $profilePicture;

	return $this;
}

/**
* Get name - Name
* @return string|null
*/
public function getName(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("name");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->name;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set name - Name
* @param string|null $name
* @return $this
*/
public function setName(?string $name): static
{
	$this->markFieldDirty("name", true);

	$this->name = $name;

	return $this;
}

/**
* Get title - Title
* @return string|null
*/
public function getTitle(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("title");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->title;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set title - Title
* @param string|null $title
* @return $this
*/
public function setTitle(?string $title): static
{
	$this->markFieldDirty("title", true);

	$this->title = $title;

	return $this;
}

/**
* Get testimonies - Testimonies
* @return string|null
*/
public function getTestimonies(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("testimonies");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->testimonies;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set testimonies - Testimonies
* @param string|null $testimonies
* @return $this
*/
public function setTestimonies(?string $testimonies): static
{
	$this->markFieldDirty("testimonies", true);

	$this->testimonies = $testimonies;

	return $this;
}

}

