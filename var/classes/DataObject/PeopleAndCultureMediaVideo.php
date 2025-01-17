<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - videoTitle [input]
 * - videoDescription [textarea]
 * - videoThumbnail [image]
 * - video [video]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
 @method static \Pimcore\Model\DataObject\PeopleAndCultureMediaVideo\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\PeopleAndCultureMediaVideo\Listing|\Pimcore\Model\DataObject\PeopleAndCultureMediaVideo|null getByVideoTitle(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PeopleAndCultureMediaVideo\Listing|\Pimcore\Model\DataObject\PeopleAndCultureMediaVideo|null getByVideoDescription(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\PeopleAndCultureMediaVideo\Listing|\Pimcore\Model\DataObject\PeopleAndCultureMediaVideo|null getByVideoThumbnail(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class PeopleAndCultureMediaVideo extends Concrete
{
public const FIELD_VIDEO_TITLE = 'videoTitle';
public const FIELD_VIDEO_DESCRIPTION = 'videoDescription';
public const FIELD_VIDEO_THUMBNAIL = 'videoThumbnail';
public const FIELD_VIDEO = 'video';

protected $classId = "26";
protected $className = "PeopleAndCultureMediaVideo";
protected $videoTitle;
protected $videoDescription;
protected $videoThumbnail;
protected $video;


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
* Get videoTitle - Video Title
* @return string|null
*/
public function getVideoTitle(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("videoTitle");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->videoTitle;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set videoTitle - Video Title
* @param string|null $videoTitle
* @return $this
*/
public function setVideoTitle(?string $videoTitle): static
{
	$this->markFieldDirty("videoTitle", true);

	$this->videoTitle = $videoTitle;

	return $this;
}

/**
* Get videoDescription - Video Description
* @return string|null
*/
public function getVideoDescription(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("videoDescription");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->videoDescription;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set videoDescription - Video Description
* @param string|null $videoDescription
* @return $this
*/
public function setVideoDescription(?string $videoDescription): static
{
	$this->markFieldDirty("videoDescription", true);

	$this->videoDescription = $videoDescription;

	return $this;
}

/**
* Get videoThumbnail - Video Thumbnail
* @return \Pimcore\Model\Asset\Image|null
*/
public function getVideoThumbnail(): ?\Pimcore\Model\Asset\Image
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("videoThumbnail");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->videoThumbnail;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set videoThumbnail - Video Thumbnail
* @param \Pimcore\Model\Asset\Image|null $videoThumbnail
* @return $this
*/
public function setVideoThumbnail(?\Pimcore\Model\Asset\Image $videoThumbnail): static
{
	$this->markFieldDirty("videoThumbnail", true);

	$this->videoThumbnail = $videoThumbnail;

	return $this;
}

/**
* Get video - Video
* @return \Pimcore\Model\DataObject\Data\Video|null
*/
public function getVideo(): ?\Pimcore\Model\DataObject\Data\Video
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("video");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->video;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set video - Video
* @param \Pimcore\Model\DataObject\Data\Video|null $video
* @return $this
*/
public function setVideo(?\Pimcore\Model\DataObject\Data\Video $video): static
{
	$this->markFieldDirty("video", true);

	$this->video = $video;

	return $this;
}

}

