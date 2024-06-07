<?php

/**
 * Fields Summary:
 * - selectRank [select]
 */

namespace Pimcore\Model\DataObject\Fieldcollection\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

class Ranks extends DataObject\Fieldcollection\Data\AbstractData
{
public const FIELD_SELECT_RANK = 'selectRank';

protected string $type = "Ranks";
protected $selectRank;


/**
* Get selectRank - Position / Title
* @return string|null
*/
public function getSelectRank(): ?string
{
	$data = $this->selectRank;
	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set selectRank - Position / Title
* @param string|null $selectRank
* @return $this
*/
public function setSelectRank(?string $selectRank): static
{
	$this->selectRank = $selectRank;

	return $this;
}

}

