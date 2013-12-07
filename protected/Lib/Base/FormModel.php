<?php

namespace Base;

use \Tools as T;
use \Consts as C;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class FormModel extends \CFormModel {

	public $DontValidateCaptcha = false;
	private static $UniqueMsg = '{attribute} "{value}" has been used previously. It should be unique.';
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

	/**
	 * @param str $attr		//attribute name
	 * @param arr $params
	 * 	SQL //can be passed in and the :val param is the post value as string(no refernce)<br/>
	 * 	SQLParams //params to pass into SQL
	 * 	Msg //{attribute} and {value} are accessible. It can be omitted to use default msg.
	 * 	MsgTModule //translation module
	 * 	MsgTCat //must be set when the Msg is absent to use the default msg
	 */
	public function IsUnique($attr, $params) {
		$val = $this->attributes[$attr];
		if (!isset($params['SQL']))
			\Err::ErrMsg_Method(__METHOD__, 'Unique SQL has not been set', func_get_args());
		if ($val) {
			$SQLParams = isset($params['SQLParams']) ? $params['SQLParams'] : array();
			$SQLParams[':val'] = $val;
			$result = T\DB::GetField($params['SQL'], $SQLParams);
			if (!isset($params['Msg']) && !isset($params['MsgTCat']))
				\Err::ErrMsg_Method(__METHOD__, 'Set at least Msg or translation cat to generate validation msg.', func_get_args());
			if ($result) {
				if (!isset($params['Msg']))
					$params['Msg'] = \Lng::t(
									isset($params['MsgTModule']) ? $params['MsgTModule'] : null
									, $params['MsgTCat']
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

	/**
	 * Summarized ajax client-server validation safe for captcha sessions
	 * @param type $AjaxKW
	 * @param \Base\FormModel $Model
	 * @param type $DontValidateCaptcha
	 * @param type $AjaxKWPostName
	 */
	static function AjaxValidation($AjaxKW, \Base\FormModel $Model, $DontValidateCaptcha = false, $AjaxKWPostName = 'ajax') {
		if (\GPCS::POST($AjaxKWPostName) == $AjaxKW) {
			if (property_exists($Model, 'DontValidateCaptcha'))
				$Model->DontValidateCaptcha = $DontValidateCaptcha;
			echo \CActiveForm::validate($Model);
			\Yii::app()->end();
		}
	}

}

?>
