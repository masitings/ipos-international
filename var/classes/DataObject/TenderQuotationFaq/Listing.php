<?php

namespace Pimcore\Model\DataObject\TenderQuotationFaq;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\TenderQuotationFaq|false current()
 * @method DataObject\TenderQuotationFaq[] load()
 * @method DataObject\TenderQuotationFaq[] getData()
 * @method DataObject\TenderQuotationFaq[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "24";
protected $className = "TenderQuotationFaq";


/**
* Filter by question (Question)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByQuestion ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("question")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by questionType (Topics)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByQuestionType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("questionType")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by answer (Answer)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByAnswer ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("answer")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by videoTime (Duration)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVideoTime ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("videoTime")->addListingFilter($this, $data, $operator);
	return $this;
}



}
