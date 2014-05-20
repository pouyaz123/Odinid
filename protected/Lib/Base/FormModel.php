<?php

namespace Base;

use \Tools as T;
use \Consts as C;

/**
 * BE CAREFUL : about the XSSPurification on password fields and ... because XSSPurification is on by default for all attributes<br/>
 * The IsUnique validator alias for \Validators\IsUnique will be registered through this class<br/>
 * ----<br/>
 * This event system can be used too:<br/>
 * function callMyModelBusiness(){}<br/>
 * function onBeforeMyModelBusiness(){}<br/>
 * function onAfterMyModelBusiness(){}<br/>
 * $model->MyModelBusiness();
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property string $PostName //post name will be used in rendered form
 * @method void getPostName()	//this is a not-forced(optional) abstract method<br/>
 * post name of this model in the $_POST<br/>
 * $_POST['modelPostName']['attributeName']<br/>
 * @method void call() Don't use call... methods. They have relative events must be triggered. The name of the right method is without call prefix such as : ->MyMethod() for callMyMethod
 * @property-read boolean $XSSPurification xss purification state
 */
class FormModel extends \CFormModel {

	public $DontValidateCaptcha = false;
	private static $MyValidatorsRegistered = false;

	public function __construct($scenario = '') {
		$MyValidatorsRegistered = &self::$MyValidatorsRegistered;
		if (!$MyValidatorsRegistered) {
			\CValidator::$builtInValidators['MyCaptcha'] = '\Validators\Captcha';
			\CValidator::$builtInValidators['IsUnique'] = '\Validators\DBNotExist';
			\CValidator::$builtInValidators['IsExist'] = '\Validators\DBExist';
//			\CValidator::$builtInValidators['DB'] = '\Validators\DBValidator';
			$MyValidatorsRegistered = true;
		}
		parent::__construct($scenario);
	}

	public function __call($name, $parameters) {
		$calller = "call$name";
		if (method_exists($this, $calller)) {
			$eventBeforeCall = "onBefore$name";
			$eventAfterCall = "onAfter$name";
			if (method_exists($this, $eventBeforeCall))
				call_user_func_array(array($this, $eventBeforeCall), $parameters);
			$result = call_user_func_array(array($this, $calller), $parameters);
			if (method_exists($this, $eventAfterCall))
				call_user_func_array(array($this, $eventAfterCall), $parameters);
			return $result;
		} else
			parent::__call($name, $parameters);
	}

	public function setAttributes($values, $safeOnly = true) {
		parent::setAttributes($values, $safeOnly);
		//HTML purification
		$this->XSSPurify();
	}

	//----------- XSS -----------//
//	protected $XSSPurification = true;
	/**
	 * the state of XSS purification will be used in setAttributes before any validation happens<br/>
	 * also "protected $XSSPurification = true;" can be used
	 * @return boolean
	 */
	function getXSSPurification() {
		return true;
	}

	/**
	 * @var array options for \CHtmlPurifier
	 */
	protected $XSSPurify_Options = array('URI.AllowedSchemes' => array(
			'http' => true,
			'https' => true,
	));

	/**
	 * @deprecated since version 1 because it's better to purify all and create exceptions to have more security.
	 * @return string|array|false list of attrs "attr1,attr2" or "*" or array(...) or false(no purification). "*" is by default
	 */
	protected function XSSPurify_Attrs() {
		return '*';
	}

	/**
	 * @return string|array list of attrs "attr1,attr2". array() is by default
	 */
	protected function XSSPurify_Exceptions() {
		return array();
	}

	/**
	 * Performs XSSPurification on this model only if ->XSSPurification was true<br/>
	 * This will be triggered automatically by setAttributes before any validation happens.
	 * So if you don't use massive assignment call this method by hand<br/>
	 */
	public function XSSPurify() {
		$XSSPurify_Attrs = $this->XSSPurify_Attrs();
		if ($this->XSSPurification && $XSSPurify_Attrs) {
			if ($XSSPurify_Attrs === '*') {
				//all attributes except ...
				$Exceptions = $this->XSSPurify_Exceptions();
				if ($Exceptions && is_string($Exceptions))
					$Exceptions = explode(',', preg_replace('/\t|\n|\s/', '', $Exceptions));
				foreach ($this->attributes as $attr => $val) {
					if (!in_array($attr, $Exceptions, true))
						$this->$attr = T\Security::XSSPurify($val, $this->XSSPurify_Options);
				}
			}elseif ($XSSPurify_Attrs) {
				//special attributes
				if (is_string($XSSPurify_Attrs))
					$XSSPurify_Attrs = explode(',', preg_replace('/\t|\n|\s/', '', $XSSPurify_Attrs));
				foreach ($XSSPurify_Attrs as $attr)
					$this->$attr = T\Security::XSSPurify($this->$attr, $this->XSSPurify_Options);
			}
		}
	}

	//----------- Ajax Validation -----------//
	/**
	 * Summarized ajax client-server validation safe for captcha sessions
	 * @param type $AjaxKW
	 * @param \Base\FormModel $Model
	 * @param bool $DontValidateCaptcha	//prevents refreshing captcha session on single field ajax validations
	 * @param string $AjaxKWPostName
	 */
	final static function AjaxValidation($AjaxKW, \Base\FormModel $Model, $DontValidateCaptcha = false, $AjaxKWPostName = 'ajax') {
		if (\GPCS::POST($AjaxKWPostName) == $AjaxKW || \GPCS::GET($AjaxKWPostName) == $AjaxKW) {
			if (property_exists($Model, 'DontValidateCaptcha'))
				$Model->DontValidateCaptcha = $DontValidateCaptcha;
			echo \CActiveForm::validate($Model);
			\Yii::app()->end();
		}
	}

	/** this is a not-forced(optional) abstract method and do nothing in the base \Base\FormModel.
	 * So children are not recalling this parent method<br/>
	 * clean the viewstate of special attrs<br/>
	 * Yii doesn't support ViewState management which is a security risk about password field.<br/>
	 * i told them but it is not considered : https://github.com/yiisoft/yii/pull/3107<br/>
	 * i added the viewState boolean to the $htmlOptions of \CHtml::activeInputField with a default false value for \CHtml::activePasswordField<br/>
	 */
	protected function CleanViewStateOfSpecialAttrs() {
		//don't put anything here
	}

}
