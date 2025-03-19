<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - question [input]
 * - questionType [select]
 * - answer [wysiwyg]
 * - videoTime [input]
 * - detailVideo [video]
 */

namespace Pimcore\Model\DataObject;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;
use Pimcore\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \Pimcore\Model\DataObject\Faqs\Listing getList(array $config = [])
* @method static \Pimcore\Model\DataObject\Faqs\Listing|\Pimcore\Model\DataObject\Faqs|null getByQuestion(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Faqs\Listing|\Pimcore\Model\DataObject\Faqs|null getByQuestionType(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Faqs\Listing|\Pimcore\Model\DataObject\Faqs|null getByAnswer(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
* @method static \Pimcore\Model\DataObject\Faqs\Listing|\Pimcore\Model\DataObject\Faqs|null getByVideoTime(mixed $value, ?int $limit = null, int $offset = 0, ?array $objectTypes = null)
*/

class Faqs extends Concrete
{
public const FIELD_QUESTION = 'question';
public const FIELD_QUESTION_TYPE = 'questionType';
public const FIELD_ANSWER = 'answer';
public const FIELD_VIDEO_TIME = 'videoTime';
public const FIELD_DETAIL_VIDEO = 'detailVideo';

protected $classId = "3";
protected $className = "faqs";
protected $question;
protected $questionType;
protected $answer;
protected $videoTime;
protected $detailVideo;


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
* Get question - Question
* @return string|null
*/
public function getQuestion(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("question");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->question;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set question - Question
* @param string|null $question
* @return $this
*/
public function setQuestion(?string $question): static
{
	$this->markFieldDirty("question", true);

	$this->question = $question;

	return $this;
}

/**
* Get questionType - Topics
* @return string|null
*/
public function getQuestionType(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("questionType");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->questionType;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set questionType - Topics
* @param string|null $questionType
* @return $this
*/
public function setQuestionType(?string $questionType): static
{
	$this->markFieldDirty("questionType", true);

	$this->questionType = $questionType;

	return $this;
}

/**
* Get answer - Answer
* @return string|null
*/
public function getAnswer(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("answer");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->getClass()->getFieldDefinition("answer")->preGetData($this);

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set answer - Answer
* @param string|null $answer
* @return $this
*/
public function setAnswer(?string $answer): static
{
	$this->markFieldDirty("answer", true);

	$this->answer = $answer;

	return $this;
}

/**
* Get videoTime - Duration
* @return string|null
*/
public function getVideoTime(): ?string
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("videoTime");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->videoTime;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set videoTime - Duration
* @param string|null $videoTime
* @return $this
*/
public function setVideoTime(?string $videoTime): static
{
	$this->markFieldDirty("videoTime", true);

	$this->videoTime = $videoTime;

	return $this;
}

/**
* Get detailVideo - URL
* @return \Pimcore\Model\DataObject\Data\Video|null
*/
public function getDetailVideo(): ?\Pimcore\Model\DataObject\Data\Video
{
	if ($this instanceof PreGetValueHookInterface && !\Pimcore::inAdmin()) {
		$preValue = $this->preGetValue("detailVideo");
		if ($preValue !== null) {
			return $preValue;
		}
	}

	$data = $this->detailVideo;

	if ($data instanceof \Pimcore\Model\DataObject\Data\EncryptedField) {
		return $data->getPlain();
	}

	return $data;
}

/**
* Set detailVideo - URL
* @param \Pimcore\Model\DataObject\Data\Video|null $detailVideo
* @return $this
*/
public function setDetailVideo(?\Pimcore\Model\DataObject\Data\Video $detailVideo): static
{
	$this->markFieldDirty("detailVideo", true);

	$this->detailVideo = $detailVideo;

	return $this;
}

}

