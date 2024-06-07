<?php

/**
 * Fields Summary:
 * - startDate [date]
 * - lastDate [date]
 * - datePlaning [input]
 * - teachingArrangement [block]
 * -- startTime [time]
 * -- lastTime [time]
 * -- venue [select]
 * -- venueText [input]
 * -- timePlanning [input]
 */

namespace Pimcore\Model\DataObject\Fieldcollection\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

class ProgramPlanning extends DataObject\Fieldcollection\Data\AbstractData
{
public const FIELD_START_DATE = 'startDate';
public const FIELD_LAST_DATE = 'lastDate';
public const FIELD_DATE_PLANING = 'datePlaning';
public const FIELD_TEACHING_ARRANGEMENT = 'teachingArrangement';

protected string $type = "ProgramPlanning";
protected $startDate;
protected $lastDate;
protected $datePlaning;
protected $teachingArrangement;


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
* Get datePlaning - Event Day Label
* @return string|null
*/
public function getDatePlaning(): ?string
{
	$data = $this->datePlaning;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set datePlaning - Event Day Label
* @param string|null $datePlaning
* @return $this
*/
public function setDatePlaning(?string $datePlaning): static
{
	$this->datePlaning = $datePlaning;

	return $this;
}

/**
* Get teachingArrangement - Programme Timings
* @return \Pimcore\Model\DataObject\Data\BlockElement[][]
*/
public function getTeachingArrangement(): ?array
{
	$container = $this;
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getDefinition()->getFieldDefinition("teachingArrangement");
	$data = $fd->preGetData($container);
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set teachingArrangement - Programme Timings
* @param \Pimcore\Model\DataObject\Data\BlockElement[][] $teachingArrangement
* @return $this
*/
public function setTeachingArrangement(?array $teachingArrangement): static
{
	/** @var \Pimcore\Model\DataObject\ClassDefinition\Data\Block $fd */
	$fd = $this->getDefinition()->getFieldDefinition("teachingArrangement");
	$this->teachingArrangement = $fd->preSetData($this, $teachingArrangement);
	return $this;
}

}

