<?php

namespace Base;

use \Tools as T;
use \Consts as C;

/**
 * Tondarweb Container control base(ASP.Net simulation base)
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 2
 * @copyright (c) Abbas Ali Hashemian
 * @access public
 */
class Container extends \CComponent implements \Interfaces\String {

	private $Contents = array();
	private $Keywords = array();
	private $Indexer = array();
	protected $Visible = true;
	public $ID = null;

	private static function TakeGlobalID($ID, $Obj) {
		if (\F3::get($ID))
			\Err::ErrMsg_Method(__METHOD__, 'Control ID conflict(repeated)', $ID);
		\F3::set($ID, $Obj);
		$ID = str_replace(array('][', '].', '.', '[', ']'), array('_', '_', '_', '_', ''), $ID); //arrays (like form fields)
//		if (preg_match('/^\w+$/', $ID))
//			eval("\$GLOBALS['CONTROLS']['$ID'] = \$Obj;");
//		"function $ID(){return \$GLOBALS['CONTROLS']['$ID'];}"
		return $ID;
	}

	function __construct($ID = NULL, $mixedContent = NULL) {
		if ($ID)
			$ID = self::TakeGlobalID($ID, $this);
		$this->ID = $ID;
		if ($mixedContent)
			$this->AddContent($mixedContent);
	}

	/**
	 * @return \Base\Container 
	 */
	public function Visible($State = null) {
		if ($State === null)
			return $this->Visible;
		$this->Visible = $State;
		return $this;
	}

	/**
	 * @param mixed $mixedContent //fnc ...($Params){}
	 * @param int $Idx
	 * @param mixed $Params
	 * @param str $UKW for unique add
	 * @return \Base\Container 
	 */
	public function AddContentAt($mixedContent, $Idx = -1, $Params = NULL, $UKW = NULL) {
		if (!is_int($Idx) || !(is_callable($mixedContent) || is_string($mixedContent) || is_a($mixedContent, '\Interfaces\String')))
			\Err::ErrMsg_Method(__METHOD__, 'Atleast one invalid argument is passed!', func_get_args());

		$mixedContent = array('Content' => $mixedContent, 'Params' => $Params);
		$Contents = &$this->Contents;
		$Keywords = &$this->Keywords;
		$CntIdx = count($Contents);
		$UKW = $UKW ? $UKW : $CntIdx;

		$Contents[$CntIdx] = $mixedContent;
		$this->RemoveContent($UKW);
		$Keywords[$UKW] = $CntIdx;
		T\Basics::array_insertAt($this->Indexer, $CntIdx, $Idx);

		return $this;
	}

	/**
	 * @param mixed $mixedContent //fnc ...($Params){}
	 * @param mixed $Params
	 * @param str $UKW for unique add
	 * @return \Base\Container
	 */
	public function AddContent($mixedContent, $Params = NULL, $UKW = null) {
		return $this->AddContentAt($mixedContent, -1, $Params, $UKW);
	}

	/**
	 * @return \Base\Container 
	 */
	public function ClearContents() {
		$this->Contents = array();
		$this->Keywords = array();
		$this->Indexer = array();
		return $this;
	}

	/**
	 * @return \Base\Container 
	 */
	public function RemoveContent($KW) {
		if ($this->HasContent($KW)) {
			$this->Contents[$this->Keywords[$KW]] = NULL;
			unset($this->Keywords[$KW]);
		}
		return $this;
	}

	public function HasContent($KW) {
		return isset($this->Keywords[$KW]);
	}

	public function GetContent($KW, &$refParams_ToGet = NULL) {
		if ($this->HasContent($KW)) {
			$Case = &$this->Contents[$this->Keywords[$KW]];
			$refParams_ToGet = $Case['Params'];
			return $Case['Content'];
		}
		return null;
	}

	protected $EventHandlers = array();

	protected function TriggerEventHandler(&$HandlersCollection) {
		if (count($HandlersCollection) > 0) {
			foreach ($HandlersCollection as $Idx => $Handler) {
				$Result = NULL;
				if (is_callable($Handler['FNC']))
					$Result = $Handler['FNC']($this, @$Handler['PARAMS']);
				if ($Handler['ONCE'])
					unset($HandlersCollection[$Idx]);
				if ($Result === false)
					return false;
			}
		}
		return true;
	}

	/**
	 * @param func $fnc function ...($Sender, $Params){}
	 * @param mixed $Params	//will be passed to $fnc
	 * @param int $KW = NULL
	 * @param bool $JustInFirstRender	//run this event handler just for first render time
	 * @return \Base\Container 
	 */
	function OnPreRender($fnc, $Params = null, $KW = null, $JustInFirstRender = true) {
		if ($KW)
			$this->EventHandlers['PreRender'][$KW] = array('FNC' => $fnc, 'ONCE' => $JustInFirstRender, 'PARAMS' => $Params);
		else
			$this->EventHandlers['PreRender'][] = array('FNC' => $fnc, 'ONCE' => $JustInFirstRender, 'PARAMS' => $Params);
		return $this;
	}

	protected function Render() {
		$Result = '';
		if ($this->Visible) {
			ksort($this->Indexer);
			foreach ($this->Indexer as $Idx => $CntIdx) {
				$Cnt = $this->Contents[$CntIdx];
				if ($Cnt) {
					$Params = $Cnt['Params'];
					$Cnt = $Cnt['Content'];
					if (is_string($Cnt) || is_a($Cnt, '\Interfaces\String'))
						$Result.=$Cnt . '';
					elseif (is_callable($Cnt))
						$Cnt($Params);
				}
			}
		}
		return $Result;
	}

	function __toString() {
		if ($this->TriggerEventHandler($this->EventHandlers['PreRender']) === false)
			return '';
		return $this->Render();
	}

}

?>
