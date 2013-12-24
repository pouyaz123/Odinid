<?php

namespace Base;

use \Tools as T;
use \Consts as C;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property string $PostName //post name will be used in rendered form
 */
class FormModel extends \CFormModel {

	public $DontValidateCaptcha = false;
	public $ValidationMsg_TranslationModule = null;
	public $ValidationMsg_TranslationCategory = 'Common';
	//----------- XSS -----------//
	protected $XSSPurification = true;

	/**
	 * @var array options for \CHtmlPurifier
	 */
	protected $XSSPurify_Options = array('URI.AllowedSchemes' => array(
			'http' => true,
			'https' => true,
	));

	/**
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

	public function setAttributes($values, $safeOnly = true) {
		parent::setAttributes($values, $safeOnly);
		//HTML purification
		if ($this->XSSPurification) {
			$XSSPurify_Attrs = $this->XSSPurify_Attrs();
			if ($XSSPurify_Attrs == '*') {
				$Exceptions = $this->XSSPurify_Exceptions();
				if ($Exceptions && is_string($Exceptions))
					$Exceptions = explode(',', preg_replace('/\t|\n|\s/', '', $Exceptions));
				foreach ($this->attributes as $attr => $val) {
					if (!in_array($attr, $Exceptions, true))
						$this->$attr = T\Security::XSSPurify($this->$attr, $this->XSSPurify_Options);
				}
			}elseif ($XSSPurify_Attrs) {
				if (is_string($XSSPurify_Attrs))
					$XSSPurify_Attrs = explode(',', preg_replace('/\t|\n|\s/', '', $XSSPurify_Attrs));
				foreach ($XSSPurify_Attrs as $attr)
					$this->$attr = T\Security::XSSPurify($this->$attr, $this->XSSPurify_Options);
			}
		}
	}

	//----------- Unique Validation -----------//
	private static $UniqueMsg = 'The {attribute} {value} is not unique';

	/**
	 * @param str $attr		//attribute name
	 * @param arr $params
	 * 	SQL //can be passed in and the :val param is the post value as string(no refernce)<br/>
	 * 	SQLParams //params to pass into SQL
	 * 	Msg //{attribute} and {value} are accessible. It can be omitted to use default msg.
	 * 	MsgTransModule //translation module
	 * 	MsgTransCat //must be set when the Msg is absent to use the default msg
	 */
	public function IsUnique($attr, $params) {
		$val = $this->attributes[$attr];
		if (!isset($params['SQL']))
			\Err::ErrMsg_Method(__METHOD__, 'Unique SQL has not been set', func_get_args());
		if ($val) {
			if (!isset($params['MsgTransModule']))
				$params['MsgTransModule'] = $this->ValidationMsg_TranslationModule;
			if (!isset($params['MsgTransCat']))
				$params['MsgTransCat'] = $this->ValidationMsg_TranslationCategory;
			if (!isset($params['Msg']) && (!isset($params['MsgTransModule']) || !isset($params['MsgTransCat'])))
				\Err::ErrMsg_Method(__METHOD__, 'Set Msg or translation module and cat to generate a validation msg.', func_get_args());
			$SQLParams = isset($params['SQLParams']) ? $params['SQLParams'] : array();
			$SQLParams[':val'] = $val;
			$result = T\DB::GetField($params['SQL'], $SQLParams);
			if ($result) {
				if (!isset($params['Msg']))
					$params['Msg'] = \Lng::t(
									$params['MsgTransModule']
									, $params['MsgTransCat']
									, self::$UniqueMsg
					);

				$this->addError($attr, str_replace(array('{attribute}', '{value}'), array($this->getAttributeLabel($attr), $val), $params['Msg']));
			}
		}
	}

	/**
	 * Alias for IsUnique to avoid mistakes
	 * @param type $attr
	 * @param type $params
	 */
	public function Unique($attr, $params) {
		$this->IsUnique($attr, $params);
	}

	//----------- OrRequire Validation -----------//
//	private $_orRequire_Data = array('attrs'=>array(), 'isvalid');
//	private $_orRequire_IsValid = false;
//
//	/**
//	 * At least one of the attributes have been assigned to this validator must be required(filled)
//	 * @param str $attr		//attribute name
//	 * @param arr $params
//	 * 	Msg //{attribute} and {value} are accessible. It can be omitted to use default msg.
//	 * 	MsgTransModule //translation module
//	 * 	MsgTransCat //must be set when the Msg is absent to use the default msg
//	 */
//	function orRequire($attr, $param) {
//		$ErrAttrs = &$this->_orRequire_Data;
//		if (!isset($this->$attr)) {
//			$ErrAttrs[] = array('attr' => $attr, 'param' => $param);
//		} else
//			$this->_orRequire_IsValid = true;
//	}
//
//	private function afterValidate_OrRequire() {
//		if (!$this->_orRequire_IsValid) {
//			foreach ($this->_orRequire_Data as $attr => $param) {
//				$this->addError($attr, '');
//			}
//		}
//	}
//
//	protected function afterValidate() {
//		$this->afterValidate_OrRequire();
//		parent::afterValidate();
//	}

	//----------- Ajax Validation -----------//
	/**
	 * Summarized ajax client-server validation safe for captcha sessions
	 * @param type $AjaxKW
	 * @param \Base\FormModel $Model
	 * @param bool $DontValidateCaptcha	//prevents refreshing captcha session on single field ajax validations
	 * @param string $AjaxKWPostName
	 */
	static function AjaxValidation($AjaxKW, \Base\FormModel $Model, $DontValidateCaptcha = false, $AjaxKWPostName = 'ajax') {
		if (\GPCS::POST($AjaxKWPostName) == $AjaxKW) {
			if (property_exists($Model, 'DontValidateCaptcha'))
				$Model->DontValidateCaptcha = $DontValidateCaptcha;
			echo \CActiveForm::validate($Model);
			\Yii::app()->end();
		}
	}

	/**
	 * post name of this model in the $_POST
	 * $_POST['modelPostName']['attributeName']
	 */
	public function getPostName() {
		
	}

	/**
	 * clean the viewstate of special attrs
	 * Yii doesn't support ViewState management which is a security risk about password field.
	 * i told them but it is not considered : https://github.com/yiisoft/yii/pull/3107
	 * i added the viewState boolean to the $htmlOptions of \CHtml::activeInputField with a default false value for \CHtml::activePasswordField
	 */
	protected function CleanViewStateOfSpecialAttrs() {
		
	}

//	//NO VIEW STATE
//	private $_NoViewStateAttrs = array();
//
//	/**
//	 * No viewstate for fields such as password or captcha
//	 * @param type $attr
//	 * @param type $params
//	 */
//	public function NoViewState($attr, $params) {
//		$this->_NoViewStateAttrs[$attr] = $params;
//	}
//
//	protected function afterValidate() {
//		//bug: if we set it to null here we can't do related tasks in the model
//		//also we can't separate the behavior in successful and failed validation
//		foreach ($this->_NoViewStateAttrs as $attr => $param) {
//			$this->$attr = NULL;
//		}
//		parent::afterValidate();
//	}
}
