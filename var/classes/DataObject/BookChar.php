<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - title [input]
 * - targetLink [link]
 * - chatLogo [image]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\BookChar\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\BookChar\Listing|\Pimcore\Model\DataObject\BookChar|null getByTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\BookChar\Listing|\Pimcore\Model\DataObject\BookChar|null getByChatLogo(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class BookChar extends Concrete
{
public const FIELD_TITLE = 'title';
public const FIELD_TARGET_LINK = 'targetLink';
public const FIELD_CHAT_LOGO = 'chatLogo';

protected $classId = "16";
protected $className = "BookChar";
protected $title;
protected $targetLink;
protected $chatLogo;


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
* Get targetLink - URL
* @return \Pimcore\Model\DataObject\Data\Link|null
*/
public function getTargetLink(): ?\Pimcore\Model\DataObject\Data\Link
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("targetLink");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->targetLink;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set targetLink - URL
* @param \Pimcore\Model\DataObject\Data\Link|null $targetLink
* @return $this
*/
public function setTargetLink(?\Pimcore\Model\DataObject\Data\Link $targetLink): static
{
	$this->markFieldDirty("targetLink", true);

	$this->targetLink = $targetLink;

	return $this;
}

/**
* Get chatLogo - Icon
* @return \Pimcore\Model\Asset\Image|null
*/
public function getChatLogo(): ?\Pimcore\Model\Asset\Image
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("chatLogo");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->chatLogo;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set chatLogo - Icon
* @param \Pimcore\Model\Asset\Image|null $chatLogo
* @return $this
*/
public function setChatLogo(?\Pimcore\Model\Asset\Image $chatLogo): static
{
	$this->markFieldDirty("chatLogo", true);

	$this->chatLogo = $chatLogo;

	return $this;
}

}

