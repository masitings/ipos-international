<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - mediaTitle [input]
 * - mediaSubTitle [input]
 * - mediaDescription [textarea]
 * - mediaThumbnail [image]
 * - mediaExternalLink [link]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\IPStartMediaCarouselImage\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\IPStartMediaCarouselImage\Listing|\Pimcore\Model\DataObject\IPStartMediaCarouselImage|null getByMediaTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\IPStartMediaCarouselImage\Listing|\Pimcore\Model\DataObject\IPStartMediaCarouselImage|null getByMediaSubTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\IPStartMediaCarouselImage\Listing|\Pimcore\Model\DataObject\IPStartMediaCarouselImage|null getByMediaDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\IPStartMediaCarouselImage\Listing|\Pimcore\Model\DataObject\IPStartMediaCarouselImage|null getByMediaThumbnail(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class IPStartMediaCarouselImage extends Concrete
{
public const FIELD_MEDIA_TITLE = 'mediaTitle';
public const FIELD_MEDIA_SUB_TITLE = 'mediaSubTitle';
public const FIELD_MEDIA_DESCRIPTION = 'mediaDescription';
public const FIELD_MEDIA_THUMBNAIL = 'mediaThumbnail';
public const FIELD_MEDIA_EXTERNAL_LINK = 'mediaExternalLink';

protected $classId = "32";
protected $className = "IPStartMediaCarouselImage";
protected $mediaTitle;
protected $mediaSubTitle;
protected $mediaDescription;
protected $mediaThumbnail;
protected $mediaExternalLink;


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
* Get mediaTitle - Media Title
* @return string|null
*/
public function getMediaTitle(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("mediaTitle");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->mediaTitle;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set mediaTitle - Media Title
* @param string|null $mediaTitle
* @return $this
*/
public function setMediaTitle(?string $mediaTitle): static
{
	$this->markFieldDirty("mediaTitle", true);

	$this->mediaTitle = $mediaTitle;

	return $this;
}

/**
* Get mediaSubTitle - Media Sub Title
* @return string|null
*/
public function getMediaSubTitle(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("mediaSubTitle");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->mediaSubTitle;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set mediaSubTitle - Media Sub Title
* @param string|null $mediaSubTitle
* @return $this
*/
public function setMediaSubTitle(?string $mediaSubTitle): static
{
	$this->markFieldDirty("mediaSubTitle", true);

	$this->mediaSubTitle = $mediaSubTitle;

	return $this;
}

/**
* Get mediaDescription - Media Description
* @return string|null
*/
public function getMediaDescription(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("mediaDescription");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->mediaDescription;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set mediaDescription - Media Description
* @param string|null $mediaDescription
* @return $this
*/
public function setMediaDescription(?string $mediaDescription): static
{
	$this->markFieldDirty("mediaDescription", true);

	$this->mediaDescription = $mediaDescription;

	return $this;
}

/**
* Get mediaThumbnail - Media Thumbnail
* @return \Pimcore\Model\Asset\Image|null
*/
public function getMediaThumbnail(): ?\Pimcore\Model\Asset\Image
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("mediaThumbnail");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->mediaThumbnail;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set mediaThumbnail - Media Thumbnail
* @param \Pimcore\Model\Asset\Image|null $mediaThumbnail
* @return $this
*/
public function setMediaThumbnail(?\Pimcore\Model\Asset\Image $mediaThumbnail): static
{
	$this->markFieldDirty("mediaThumbnail", true);

	$this->mediaThumbnail = $mediaThumbnail;

	return $this;
}

/**
* Get mediaExternalLink - Media External Link
* @return \Pimcore\Model\DataObject\Data\Link|null
*/
public function getMediaExternalLink(): ?\Pimcore\Model\DataObject\Data\Link
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("mediaExternalLink");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->mediaExternalLink;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set mediaExternalLink - Media External Link
* @param \Pimcore\Model\DataObject\Data\Link|null $mediaExternalLink
* @return $this
*/
public function setMediaExternalLink(?\Pimcore\Model\DataObject\Data\Link $mediaExternalLink): static
{
	$this->markFieldDirty("mediaExternalLink", true);

	$this->mediaExternalLink = $mediaExternalLink;

	return $this;
}

}

