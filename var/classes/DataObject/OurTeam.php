<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - name [input]
 * - rank [input]
 * - teamType [select]
 * - link [input]
 * - individualResume [wysiwyg]
 * - profilePhoto [image]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\OurTeam\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\OurTeam\Listing|\Pimcore\Model\DataObject\OurTeam|null getByName(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\OurTeam\Listing|\Pimcore\Model\DataObject\OurTeam|null getByRank(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\OurTeam\Listing|\Pimcore\Model\DataObject\OurTeam|null getByTeamType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\OurTeam\Listing|\Pimcore\Model\DataObject\OurTeam|null getByLink(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\OurTeam\Listing|\Pimcore\Model\DataObject\OurTeam|null getByIndividualResume(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\OurTeam\Listing|\Pimcore\Model\DataObject\OurTeam|null getByProfilePhoto(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class OurTeam extends Concrete
{
public const FIELD_NAME = 'name';
public const FIELD_RANK = 'rank';
public const FIELD_TEAM_TYPE = 'teamType';
public const FIELD_LINK = 'link';
public const FIELD_INDIVIDUAL_RESUME = 'individualResume';
public const FIELD_PROFILE_PHOTO = 'profilePhoto';

protected $classId = "6";
protected $className = "OurTeam";
protected $name;
protected $rank;
protected $teamType;
protected $link;
protected $individualResume;
protected $profilePhoto;


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
* Get rank - Title
* @return string|null
*/
public function getRank(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("rank");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->rank;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set rank - Title
* @param string|null $rank
* @return $this
*/
public function setRank(?string $rank): static
{
	$this->markFieldDirty("rank", true);

	$this->rank = $rank;

	return $this;
}

/**
* Get teamType - Category
* @return string|null
*/
public function getTeamType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("teamType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->teamType;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set teamType - Category
* @param string|null $teamType
* @return $this
*/
public function setTeamType(?string $teamType): static
{
	$this->markFieldDirty("teamType", true);

	$this->teamType = $teamType;

	return $this;
}

/**
* Get link - link
* @return string|null
*/
public function getLink(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("link");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->link;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set link - link
* @param string|null $link
* @return $this
*/
public function setLink(?string $link): static
{
	$this->markFieldDirty("link", true);

	$this->link = $link;

	return $this;
}

/**
* Get individualResume - Bio / Description
* @return string|null
*/
public function getIndividualResume(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("individualResume");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("individualResume")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set individualResume - Bio / Description
* @param string|null $individualResume
* @return $this
*/
public function setIndividualResume(?string $individualResume): static
{
	$this->markFieldDirty("individualResume", true);

	$this->individualResume = $individualResume;

	return $this;
}

/**
* Get profilePhoto - Profile Image
* @return \Pimcore\Model\Asset\Image|null
*/
public function getProfilePhoto(): ?\Pimcore\Model\Asset\Image
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("profilePhoto");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->profilePhoto;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set profilePhoto - Profile Image
* @param \Pimcore\Model\Asset\Image|null $profilePhoto
* @return $this
*/
public function setProfilePhoto(?\Pimcore\Model\Asset\Image $profilePhoto): static
{
	$this->markFieldDirty("profilePhoto", true);

	$this->profilePhoto = $profilePhoto;

	return $this;
}

}

