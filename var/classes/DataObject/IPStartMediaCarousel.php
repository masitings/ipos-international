<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - mediaTitle [input]
 * - mediaDescription [textarea]
 * - mediaThumbnail [image]
 * - mediaVideo [video]
 * - mediaExternalLink [link]
 * - mediaDescription1 [textarea]
 * - mediaDescription2 [textarea]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\IPStartMediaCarousel\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\IPStartMediaCarousel\Listing|\Pimcore\Model\DataObject\IPStartMediaCarousel|null getByMediaTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\IPStartMediaCarousel\Listing|\Pimcore\Model\DataObject\IPStartMediaCarousel|null getByMediaDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\IPStartMediaCarousel\Listing|\Pimcore\Model\DataObject\IPStartMediaCarousel|null getByMediaThumbnail(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\IPStartMediaCarousel\Listing|\Pimcore\Model\DataObject\IPStartMediaCarousel|null getByMediaDescription1(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\IPStartMediaCarousel\Listing|\Pimcore\Model\DataObject\IPStartMediaCarousel|null getByMediaDescription2(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class IPStartMediaCarousel extends Concrete
{
public const FIELD_MEDIA_TITLE = 'mediaTitle';
public const FIELD_MEDIA_DESCRIPTION = 'mediaDescription';
public const FIELD_MEDIA_THUMBNAIL = 'mediaThumbnail';
public const FIELD_MEDIA_VIDEO = 'mediaVideo';
public const FIELD_MEDIA_EXTERNAL_LINK = 'mediaExternalLink';
public const FIELD_MEDIA_DESCRIPTION1 = 'mediaDescription1';
public const FIELD_MEDIA_DESCRIPTION2 = 'mediaDescription2';

protected $classId = "30";
protected $className = "IPStartMediaCarousel";
protected $mediaTitle;
protected $mediaDescription;
protected $mediaThumbnail;
protected $mediaVideo;
protected $mediaExternalLink;
protected $mediaDescription1;
protected $mediaDescription2;


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
* Get mediaDescription - Media Desc
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
* Set mediaDescription - Media Desc
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
* Get mediaVideo - Media Video
* @return \Pimcore\Model\DataObject\Data\Video|null
*/
public function getMediaVideo(): ?\Pimcore\Model\DataObject\Data\Video
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("mediaVideo");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->mediaVideo;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set mediaVideo - Media Video
* @param \Pimcore\Model\DataObject\Data\Video|null $mediaVideo
* @return $this
*/
public function setMediaVideo(?\Pimcore\Model\DataObject\Data\Video $mediaVideo): static
{
	$this->markFieldDirty("mediaVideo", true);

	$this->mediaVideo = $mediaVideo;

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

/**
* Get mediaDescription1 - Media Description1
* @return string|null
*/
public function getMediaDescription1(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("mediaDescription1");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->mediaDescription1;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set mediaDescription1 - Media Description1
* @param string|null $mediaDescription1
* @return $this
*/
public function setMediaDescription1(?string $mediaDescription1): static
{
	$this->markFieldDirty("mediaDescription1", true);

	$this->mediaDescription1 = $mediaDescription1;

	return $this;
}

/**
* Get mediaDescription2 - Media Description2
* @return string|null
*/
public function getMediaDescription2(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("mediaDescription2");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->mediaDescription2;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set mediaDescription2 - Media Description2
* @param string|null $mediaDescription2
* @return $this
*/
public function setMediaDescription2(?string $mediaDescription2): static
{
	$this->markFieldDirty("mediaDescription2", true);

	$this->mediaDescription2 = $mediaDescription2;

	return $this;
}

}

