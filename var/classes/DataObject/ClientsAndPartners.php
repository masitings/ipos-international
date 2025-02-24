<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - name [input]
 * - websiteUrl [input]
 * - DataType [select]
 * - logImage [image]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\ClientsAndPartners\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\ClientsAndPartners\Listing|\Pimcore\Model\DataObject\ClientsAndPartners|null getByName(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ClientsAndPartners\Listing|\Pimcore\Model\DataObject\ClientsAndPartners|null getByWebsiteUrl(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ClientsAndPartners\Listing|\Pimcore\Model\DataObject\ClientsAndPartners|null getByDataType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\ClientsAndPartners\Listing|\Pimcore\Model\DataObject\ClientsAndPartners|null getByLogImage(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class ClientsAndPartners extends Concrete
{
public const FIELD_NAME = 'name';
public const FIELD_WEBSITE_URL = 'websiteUrl';
public const FIELD_DATA_TYPE = 'DataType';
public const FIELD_LOG_IMAGE = 'logImage';

protected $classId = "5";
protected $className = "ClientsAndPartners";
protected $name;
protected $websiteUrl;
protected $DataType;
protected $logImage;


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
* Get websiteUrl - URL
* @return string|null
*/
public function getWebsiteUrl(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("websiteUrl");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->websiteUrl;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set websiteUrl - URL
* @param string|null $websiteUrl
* @return $this
*/
public function setWebsiteUrl(?string $websiteUrl): static
{
	$this->markFieldDirty("websiteUrl", true);

	$this->websiteUrl = $websiteUrl;

	return $this;
}

/**
* Get DataType - Page Selection
* @return string|null
*/
public function getDataType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("DataType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->DataType;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set DataType - Page Selection
* @param string|null $DataType
* @return $this
*/
public function setDataType(?string $DataType): static
{
	$this->markFieldDirty("DataType", true);

	$this->DataType = $DataType;

	return $this;
}

/**
* Get logImage - Logo Image
* @return \Pimcore\Model\Asset\Image|null
*/
public function getLogImage(): ?\Pimcore\Model\Asset\Image
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("logImage");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->logImage;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set logImage - Logo Image
* @param \Pimcore\Model\Asset\Image|null $logImage
* @return $this
*/
public function setLogImage(?\Pimcore\Model\Asset\Image $logImage): static
{
	$this->markFieldDirty("logImage", true);

	$this->logImage = $logImage;

	return $this;
}

}

