<?php

/**
 * Fields Summary:
 * - startDate [date]
 * - lastDate [date]
 * - Time [block]
 * -- startTime [time]
 * -- lastTime [time]
 */

namespace Pimcore\Model\DataObject\Fieldcollection\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

class Fcfield extends DataObject\Fieldcollection\Data\AbstractData
{
public const FIELD_START_DATE = 'startDate';
public const FIELD_LAST_DATE = 'lastDate';
public const FIELD_TIME = 'Time';

protected string $type = "fcfield";
protected $startDate;
protected $lastDate;
protected $Time;


/**
* Get startDate - Start Date
* @return \Carbon\Carbon|null
*/
public function getStartDate(): ?\Carbon\Carbon
{
	$data = $this->startDate;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set startDate - Start Date
* @param \Carbon\Carbon|null $startDate
* @return $this
*/
public function setStartDate(?\Carbon\Carbon $startDate): static
{
	$this->startDate = $startDate;

	return $this;
}

/**
* Get lastDate - End Date
* @return \Carbon\Carbon|null
*/
public function getLastDate(): ?\Carbon\Carbon
{
	$data = $this->lastDate;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set lastDate - End Date
* @param \Carbon\Carbon|null $lastDate
* @return $this
*/
public function setLastDate(?\Carbon\Carbon $lastDate): static
{
	$this->lastDate = $lastDate;

	return $this;
}

/**
* Get Time - Time
* @return \Pimcore\Model\DataObject\Data\BlockElement[][]
*/
public function getTime(): ?array
{
	$container = $this;
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getDefinition()->getFieldDefinition("Time");
	$data = $fd->preGetData($container);
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set Time - Time
* @param \Pimcore\Model\DataObject\Data\BlockElement[][] $Time
* @return $this
*/
public function setTime(?array $Time): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getDefinition()->getFieldDefinition("Time");
	$this->Time = $fd->preSetData($this, $Time);
	return $this;
}

}

