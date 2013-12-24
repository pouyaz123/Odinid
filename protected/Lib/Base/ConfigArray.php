<?php

namespace Base;

use \Tools as T;
use \Consts as C;

/**
 * Description of ConfigArray
 *
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class ConfigArray {

	function __construct($arrInitialArray = NULL) {
		if (isset($arrInitialArray) && !is_array($arrInitialArray))
			\Err::ErrMsg_Method(__METHOD__, '$arrInitialArray should be an array!', func_get_args());
		if (isset($arrInitialArray))
			$this->Array = $arrInitialArray;
	}

	/**
	 * @return \Base\ConfigArray 
	 */
	function SetBaseConfig($arrBaseConfig) {
		if (!is_array($arrBaseConfig))
			\Err::ErrMsg_Method(__METHOD__, '$arrBaseArray should be an array!', func_get_args());
		$this->Array = T\Basics::Merge_MultiDimension($arrBaseConfig, $this->Array);
		return $this;
	}

	/**
	 * Multidimentionally merges and override the new config array on the original config
	 * @return \Base\ConfigArray 
	 */
	function MergeWith($arrNewConfigs) {
		if (!is_array($arrNewConfigs))
			\Err::ErrMsg_Method(__METHOD__, '$arrNewConfigs should be an array!', func_get_args());
		$this->Array = T\Basics::Merge_MultiDimension($this->Array, $arrNewConfigs);
		return $this;
	}

	/**
	 * @return \Base\ConfigArray 
	 */
	function _unset($Key) {
		if (isset($this->Array[$Key]))
			unset($this->Array[$Key]);
		return $this;
	}

	private $Array = array();

	/**
	 * @param str $FN
	 * @param arr $Args
	 * @return \Base\ConfigArray 
	 */
	function __call($FN, $Args) {
		if (!$Args || !count($Args))
			return isset($this->Array[$FN]) ? $this->Array[$FN] : NULL;
		if (is_object($Args[0]) && is_a($Args[0], '\Base\ConfigArray'))
			$Args[0] = $Args[0]->_getArray();
		if (isset($this->Array[$FN]) && is_array($this->Array[$FN]) && is_array($Args[0]))
			$this->Array[$FN] = T\Basics::Merge_MultiDimension($this->Array[$FN], $Args[0]);
		else
			$this->Array[$FN] = $Args[0];
		return $this;
	}

	/**
	 * @param str $PropName 
	 */
	function __get($PropName) {
		return isset($this->Array[$PropName]) ? $this->Array[$PropName] : NULL;
	}

	function __set($PropName, $Value) {
		$this->__call($PropName, $Value);
	}

	function _getArray($arrBaseConfig = NULL) {
		if ($arrBaseConfig)
			$this->SetBaseConfig($arrBaseConfig);
		return $this->Array;
	}

}
