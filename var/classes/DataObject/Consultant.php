<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - name [input]
 * - job [input]
 * - rank [input]
 * - position [input]
 * - company [input]
 * - detailUrl [input]
 * - profilePhoto [image]
 * - individualResume [wysiwyg]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Consultant\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Consultant\Listing|\Pimcore\Model\DataObject\Consultant|null getByName(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Consultant\Listing|\Pimcore\Model\DataObject\Consultant|null getByJob(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Consultant\Listing|\Pimcore\Model\DataObject\Consultant|null getByRank(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Consultant\Listing|\Pimcore\Model\DataObject\Consultant|null getByPosition(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Consultant\Listing|\Pimcore\Model\DataObject\Consultant|null getByCompany(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Consultant\Listing|\Pimcore\Model\DataObject\Consultant|null getByDetailUrl(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Consultant\Listing|\Pimcore\Model\DataObject\Consultant|null getByProfilePhoto(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Consultant\Listing|\Pimcore\Model\DataObject\Consultant|null getByIndividualResume(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Consultant extends Concrete
{
public const FIELD_NAME = 'name';
public const FIELD_JOB = 'job';
public const FIELD_RANK = 'rank';
public const FIELD_POSITION = 'position';
public const FIELD_COMPANY = 'company';
public const FIELD_DETAIL_URL = 'detailUrl';
public const FIELD_PROFILE_PHOTO = 'profilePhoto';
public const FIELD_INDIVIDUAL_RESUME = 'individualResume';

protected $classId = "10";
protected $className = "Consultant";
protected $name;
protected $job;
protected $rank;
protected $position;
protected $company;
protected $detailUrl;
protected $profilePhoto;
protected $individualResume;


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
* Get name - name
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
* Set name - name
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
* Get job - job
* @return string|null
*/
public function getJob(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("job");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->job;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set job - job
* @param string|null $job
* @return $this
*/
public function setJob(?string $job): static
{
	$this->markFieldDirty("job", true);

	$this->job = $job;

	return $this;
}

/**
* Get rank - rank
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
* Set rank - rank
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
* Get position - position
* @return string|null
*/
public function getPosition(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("position");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->position;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set position - position
* @param string|null $position
* @return $this
*/
public function setPosition(?string $position): static
{
	$this->markFieldDirty("position", true);

	$this->position = $position;

	return $this;
}

/**
* Get company -  company
* @return string|null
*/
public function getCompany(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("company");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->company;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set company -  company
* @param string|null $company
* @return $this
*/
public function setCompany(?string $company): static
{
	$this->markFieldDirty("company", true);

	$this->company = $company;

	return $this;
}

/**
* Get detailUrl - detailUrl
* @return string|null
*/
public function getDetailUrl(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("detailUrl");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->detailUrl;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set detailUrl - detailUrl
* @param string|null $detailUrl
* @return $this
*/
public function setDetailUrl(?string $detailUrl): static
{
	$this->markFieldDirty("detailUrl", true);

	$this->detailUrl = $detailUrl;

	return $this;
}

/**
* Get profilePhoto - profilePhoto
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
* Set profilePhoto - profilePhoto
* @param \Pimcore\Model\Asset\Image|null $profilePhoto
* @return $this
*/
public function setProfilePhoto(?\Pimcore\Model\Asset\Image $profilePhoto): static
{
	$this->markFieldDirty("profilePhoto", true);

	$this->profilePhoto = $profilePhoto;

	return $this;
}

/**
* Get individualResume - individualResume
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
* Set individualResume - individualResume
* @param string|null $individualResume
* @return $this
*/
public function setIndividualResume(?string $individualResume): static
{
	$this->markFieldDirty("individualResume", true);

	$this->individualResume = $individualResume;

	return $this;
}

}

