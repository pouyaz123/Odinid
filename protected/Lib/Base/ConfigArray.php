<?php

namespace Base;

use \Tools as T;
use \Consts as C;

/**
 * Description of ConfigArray
 *
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class ConfigArray implements \ArrayAccess {

	function __construct($arrInitialArray = NULL) {
		if (isset($arrInitialArray) && !is_array($arrInitialArray))
			throw new \Err(__METHOD__, '$arrInitialArray should be an array!', func_get_args());
		if (isset($arrInitialArray))
			$this->Array = $arrInitialArray;
	}

	//------ array implementation
	/** not ready yet */
	public function offsetUnset($offset) {
		
	}

	/** not ready yet */
	public function offsetSet($offset, $value) {
		
	}

	public function offsetGet($offset) {
		return $this->$offset;
	}

	public function offsetExists($offset) {
		return isset($this->$offset);
	}

	/**
	 * @return \Base\ConfigArray 
	 */
	function SetBaseConfig($arrBaseConfig) {
		if (!is_array($arrBaseConfig))
			throw new \Err(__METHOD__, '$arrBaseArray should be an array!', func_get_args());
		$this->Array = T\Basics::Merge_MultiDimension($arrBaseConfig, $this->Array);
		return $this;
	}

	/**
	 * Multidimentionally merges and override the new config array on the original config
	 * @return \Base\ConfigArray 
	 */
	function MergeWith($arrNewConfigs) {
		if (!is_array($arrNewConfigs))
			throw new \Err(__METHOD__, '$arrNewConfigs should be an array!', func_get_args());
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
		$this->__call($PropName, array($Value));
	}

	function _getArray($arrBaseConfig = NULL) {
		if ($arrBaseConfig)
			$this->SetBaseConfig($arrBaseConfig);
		return $this->Array;
	}

}
