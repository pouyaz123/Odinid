<?php

namespace Tools;

use \Tools as T;
use \Consts as C;

class Basics {
	#----------------- Basic Tools -----------------#
//	/**
//	 * @param mixed $multi_mixed (multi number of arguments)
//	 * @return mixed (first valued var / null)
//	 */
//
//	public static function GetValuedVar($multi_mixed = null) {
//		foreach (func_get_args() as $arg)
//			if (isset($arg))
//				return $arg;
//		return null;
//	}
//
//	/**
//	 * @param fun[] $multi_Func
//	 * @param arr $arrParams
//	 * @return nothing
//	 */
//	public static function GetCallableFun($multi_Func = null, $arrParams = null) {
//		$multi_Func = self::MultiArgs($multi_Func, func_get_args(), 0, ( func_num_args() > 1 ? -1 : 0));
//		if (!$multi_Func)
//			return null;
//		foreach ($multi_Func as $Func) {
//			if (!is_callable($Func))
//				continue;
//			$cmd = '$Func(';
//			if (is_array($arrParams))
//				foreach ($arrParams as $PIdx => $Param)
//					$cmd .= ( $PIdx > 0 ? ', ' : '' ) . '$arrParams[' . $PIdx . ']';
//			$cmd .= ');';
//			eval($cmd);
//			return;
//		}
//	}
//
//	/** same as GetCallableFun */
//	public static function CallCallableFun($multi_Func = null, $arrParams = null) {
//		$multi_Func = self::MultiArgs($multi_Func, func_get_args(), 0, -1);
//		self::GetCallableFun($multi_Func, $arrParams);
//	}
//	
	#----------------- Arrays -----------------#
//	static function is_associativeArray($arr) {
//		foreach (array_keys($arr) as $Key) {
//			if (!is_int($Key))
//				return true;
//		}
//		return false;
//	}
//	static function is_numericArray($arr) {
//		return !self::is_associativeArray($arr);
//	}
//	private static function Descending_Recursive(&$arrMain, &$arrSubs, $ParentIDField, $IDField, $OpenKW, $CloseKW, $MaxDepth, $Depth = 1) {
//		$arrResult = array();
//		foreach ($arrMain as $drMain) {
//			$arrResult[] = $drMain;
//			if (($Depth < $MaxDepth || $MaxDepth < 1) &&
//					(isset($drMain[$IDField]) && isset($arrSubs[$drMain[$IDField]]) && $arrSubs[$drMain[$IDField]]) &&
//					count($arrSubs[$drMain[$IDField]])
//			) {
//				$Depth++;
//				$arrResult[] = $OpenKW;
//				$arrResult = array_merge($arrResult, self::Descending_Recursive($arrSubs[$drMain[$IDField]], $arrSubs, $ParentIDField, $IDField, $OpenKW, $CloseKW, $MaxDepth, $Depth));
//				$arrResult[] = $CloseKW;
//				$Depth--;
//			}
//		}
//		return $arrResult;
//	}
//
//	/**
//	 * Descending system useful for dropdown and links
//	 */
//	public static function Descending_GetArrangedArray($DataTable, $ParentIDField = 'ParentID', $IDField = 'ID', $OpenKW = 'GROUP', $CloseKW = '/GROUP', $MaxDepth = -1) {
//		if (!$DataTable || !is_array($DataTable))
//			return array();
//		$Subs = array();
//		foreach ($DataTable as $idx => $dr) {
//			if (isset($dr[$ParentIDField]) && $dr[$ParentIDField] && $dr[$ParentIDField] != '0') {
//				if (!(
//						isset($dr[$ParentIDField]) &&
//						isset($Subs[$dr[$ParentIDField]]) &&
//						$Subs[$dr[$ParentIDField]]
//						)
//				)
//					$Subs[$dr[$ParentIDField]] = array();
//				$Subs[$dr[$ParentIDField]][] = $dr;
//				unset($DataTable[$idx]);
//			}
//		}
//		return self::Descending_Recursive($DataTable, $Subs, $ParentIDField, $IDField, $OpenKW, $CloseKW, $MaxDepth);
//	}

	static function ConfigureObject($Obj, $arrConfig) {
		if (is_array($arrConfig)) {
			foreach ($arrConfig as $key => $value)
				$Obj->$key = $value;
		}
	}

	/**
	 * @param mixed $arrValue
	 * //to pass a js fun use a php fun returning the js fun str : function(){reutrn "function jsFun(){}";}
	 * 
	 * @param int $Options [optional] //for json<br/>
	 * <br/>Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_UNESCAPED_UNICODE.
	 * @return string
	 */
	static function JSON_Advanced($arrValue, $JSFunKW = NULL, $Options = 0) {
		$arrValue = json_encode($arrValue, $Options);
		if ($JSFunKW) {
			$arrValue = preg_split("([\'\"]{$JSFunKW}|{$JSFunKW}[\'\"])", $arrValue);
			foreach ($arrValue as $Key => $Val) {
				if (($Key % 2) == 0)
					continue;
				$Val = json_decode('["' . $Val . '"]');
				$arrValue[$Key] = $Val[0];
			}
			$arrValue = implode('', $arrValue);
		}
		return $arrValue;
	}

	/**
	 * @param arr &$MultiArg (multi_var param of fun)
	 * @param arr $arr_func_get_args (result of func_get_args())
	 * @param int $StartIndexOf_MultiArgs (Start offset in func_get_args())
	 * @param int $MinusOffsetFromEnd (End offset in func_get_args())
	 * @return arr result
	 */
	public static function MultiArgs(&$MultiArg, $arr_func_get_args, $StartIndexOf_MultiArgs = 0, $MinusOffsetFromEnd = 0, $IsArrayArgs = false) {
		if (!is_int($MinusOffsetFromEnd) || $MinusOffsetFromEnd > 0)
			$MinusOffsetFromEnd = 0;

		if (count($arr_func_get_args) + $MinusOffsetFromEnd > $StartIndexOf_MultiArgs) {
			if (is_array($MultiArg) && !$IsArrayArgs)
				return $MultiArg;

			$MinusOffsetFromEnd = ( $MinusOffsetFromEnd < 0 ) ? count($arr_func_get_args) + $MinusOffsetFromEnd : count($arr_func_get_args);
			return $MultiArg = array_slice($arr_func_get_args, $StartIndexOf_MultiArgs, $MinusOffsetFromEnd);
		}
		return $MultiArg = array();
	}

	/**
	 * @param arr $Array
	 * @param mixed $Element
	 * @param int $Index
	 * @return arr result
	 */
	public static function array_insertAt(&$Array, $Element, $Index = -1, &$FinalIdx = NULL) {
		if (is_int($Index)) {
			$Len = count($Array);
			if ($Index < 0)
				$Index = $Len + ( $Index + 1 );
			$FinalIdx = $Index;
			if ($Index < $Len && $Index >= 0) {
//				$arrPart2 = array_slice($Array, $Index);
//				$Array = array_slice($Array, 0, $Index);
//				$Array[$Index] = $Element;
//				$Array = array_merge($Array, $arrPart2);
				for ($Cursor = $Len - 1; $Cursor >= $Index; $Cursor--)
					$Array[$Cursor + 1] = $Array[$Cursor];
			}
			$Array[$Index] = $Element;
		}
		return $Array;
	}

	public static function Merge_MultiDimension($arr, $arr2, $multiArr = NULL) {
		if (!is_array($arr) || !is_array($arr2))
			throw new \Err(__METHOD__, 'either of parameters should be array', func_get_args());
		foreach ($arr2 as $Key => $Value) {
			if (is_numeric($Key))
				$arr[] = $Value;
			elseif (isset($arr[$Key]) && is_array($arr[$Key]) && is_array($Value)) {
				$arr[$Key] = self::Merge_MultiDimension($arr[$Key], $Value);
			} else
				$arr[$Key] = $Value;
		}
		if ($multiArr) {
			self::MultiArgs($multiArr, func_get_args(), 2);
			foreach ($multiArr as $arr3)
				$arr = self::Merge_MultiDimension($arr, $arr3);
		}
		return $arr;
	}

}
