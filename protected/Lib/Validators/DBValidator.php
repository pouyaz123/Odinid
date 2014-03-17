<?php
//mytodo x: \Validators\DBValidator db transactions : can perform multiple SQL validation rules of one model through one transaction (->onAfterValidate)
namespace Validators;

use \Tools as T;

/**
 * DBValidator extends \CValidator and is a base for DB validations such as IsUnique and Exist
 *
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-read string $CheckType
 * @property-write string $SQL
 */
abstract class DBValidator extends \CValidator {

	private $_SQL;

	public function setSQL($SQL) {
		$this->_SQL = $SQL;
	}

	public $SQLParams;
	public $message;

	abstract function getCheckType();

	const UniqueCheck = 1;
	const ExistCheck = 2;

	/**
	 * @var boolean whether the attribute value can be null or empty.
	 * Defaults to false, meaning the attribute is invalid if it is empty.
	 */
	public $allowEmpty = false;

	/**
	 * Validates the attribute of the object.
	 * If there is any error, the error message is added to the object.
	 * @param \CModel $object the object being validated
	 * @param string $attribute the attribute being validated
	 */
	protected function validateAttribute($object, $attribute) {
		if (!isset($this->_SQL))
			throw new \Err(__METHOD__, 'Unique SQL has not been set', func_get_args());

		$value = $object->$attribute;
		if ($this->allowEmpty && $this->isEmpty($value))
			return;
		$IsValid = true;
		// reason of array checking is explained here: https://github.com/yiisoft/yii/issues/1955
		if (is_array($value))
			$IsValid = false;
		$CheckType = $this->CheckType;
		if ($IsValid && $value) {
			$SQLParams = &$this->SQLParams;
			if (!isset($SQLParams) || !is_array($SQLParams))
				$SQLParams = array();
			$SQLParams[':val'] = $value;
			$result = T\DB::GetField($this->_SQL, $SQLParams);
			if (($CheckType == self::UniqueCheck && $result) ||
					($CheckType == self::ExistCheck && !$result))
				$IsValid = false;
		}
		if ($IsValid)
			return;
		//NOT VALID IS BELOW
		$message = $this->message;
		if (!$message) {
			switch ($CheckType) {
				case self::UniqueCheck:
					$message = \Yii::t('yii','{attribute} "{value}" has already been taken.');
					break;
				case self::ExistCheck:
					$message = \Yii::t('yii','{attribute} "{value}" is invalid.');
					break;
			}
			$message = str_replace(
					array('{attribute}', '{value}')
					, array($object->getAttributeLabel($attribute), $value)
					, $message
			);
		}
		$this->addError($object, $attribute, $message);
	}

}
