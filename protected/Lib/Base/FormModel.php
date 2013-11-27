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

	/**
	 * @param str $attr		//attribute name
	 * @param arr $params
	 * 	UniqueSQL can be passed in and the :val param is the post value as string(no refernce)<br/>
	 * 	UniqueErrMsg {attribute} and {value} are accessible
	 */
	public function IsUnique($attr, $params) {
		$val = $this->attributes[$attr];
		if ($val) {
			$result = \Yii::app()->db
					->createCommand($params['UniqueSQL'])
					->query(array(':val' => $val))
					->read();
			if ($result && (!is_array($result) || end($result)))
				$this->addError($attr, str_replace(array('{attribute}', '{value}'), array($attr, $val), $params['UniqueErrMsg']));
		}
	}

}

?>
