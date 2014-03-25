<?php

namespace Tools;

use \Tools as T;
use \Consts as C;

/**
 * Tondarweb DB Tools (migrated from F3 to yii)
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb migrated to Odinid Portal
 * @version 2
 * @copyright (c) Odinid
 * @access public
 */
class DB {

	const CharsetLevel1 = 'iso-8859-1';
	const CharsetLevel2 = 'utf-8';
	const CharsetLevel1_DB = 'ascii';
	const CharsetLevel2_DB = 'utf8';
	const UniqueCode_MaxRand = 2147483647;

	private static $arrDBCharsetLevels = array(1 => self::CharsetLevel1_DB, 2 => self::CharsetLevel2_DB);

	static function CharsetLevel($strQueryPart, $intLevel = 2) {
		return "CONVERT($strQueryPart USING " . self::$arrDBCharsetLevels[$intLevel] . ")";
	}

	/**
	 * DB::RealEscape = pdo->quote($string, $parameter_type)
	 * (PHP 5 &gt;= 5.1.0, PECL pdo &gt;= 0.2.1)<br/>
	 * Quotes a string for use in a query.
	 * @link http://php.net/manual/en/pdo.quote.php
	 * @param string $string <p>
	 * The string to be quoted.
	 * </p>
	 * @param int $parameter_type [optional] <p>
	 * Provides a data type hint for drivers that have alternate quoting styles.
	 * </p>
	 * @return string a quoted string that is theoretically safe to pass into an
	 * SQL statement. Returns <b>FALSE</b> if the driver does not support quoting in
	 * this way.
	 */
	public static function RealEscape($string, $parameter_type = \PDO::PARAM_STR) {
		return substr(\Yii::app()->db->pdoInstance->quote($string, $parameter_type), 1, -1);
//		return substr(self::Connect(true)->pdo->quote($Str), 1, -1);
//		return mysql_real_escape_string($Str, self::Connect()->pdo);
	}

	const LikeEscapeChar = '=';

	/**
	 * Escapes the LIKE(not RLIKE or REGEXP) chars.<br/>
	 * Simply use the result as a param and don't forget the "ESCAPE '='" :<br/>
	 *  "col LIKE CONCAT('%', :param, ...) ESCAPE '" . T\DB::LikeEscapeChar . "'"
	 * @param string $val
	 * @param string $escapeChar  = self::LikeEscapeChar "="
	 * @return string escaped value
	 */
	static function EscapeLikeWildCards($val, $escapeChar = self::LikeEscapeChar) {
		return str_replace(
				array($escapeChar, '_', '%')
				, array($escapeChar . $escapeChar, $escapeChar . '_', $escapeChar . '%')
				, $val);
	}

	private static function ClearUnusedParams(&$Query, &$Params) {
		foreach ($Params as $ParamKey => $ParamVal) {
			if (!preg_match('/' . preg_quote($ParamKey, '/') . '([^\w]|$)/', $Query))
				unset($Params[$ParamKey]);
		}
	}

	/**
	 * Executes the SQL statement.
	 * This method is meant only for executing non-query SQL statement.
	 * No result set will be returned.
	 * @param string $strQuery
	 * @param array $arrParams input parameters (name=>value) for the SQL execution. This is an alternative
	 * to {@link bindParam} and {@link bindValue}. If you have multiple input parameters, passing
	 * them in this way can improve the performance. Note that if you pass parameters in this way,
	 * you cannot bind parameters or values using {@link bindParam} or {@link bindValue}, and vice versa.
	 * Please also note that all values are treated as strings in this case, if you need them to be handled as
	 * their real data types, you have to use {@link bindParam} or {@link bindValue} instead.
	 * @return integer number of rows affected by the execution.
	 * @throws \CDbException execution failed
	 */
	public static function Execute($strQuery, $arrParams = NULL, &$objCDbCommand = NULL) {
		if (!$arrParams)
			$arrParams = array();
		self::ClearUnusedParams($strQuery, $arrParams);
		$objCDbCommand = \Yii::app()->db->createCommand($strQuery);
		return $objCDbCommand->execute($arrParams);
	}

	/**
	 * Handles(Execute and Commit and Rollback) the whole transaction
	 * @param array $arrQueries<br/>
	 * array(<br/>
	 * 	array('Query', array(params)),<br/>
	 * 	'Query without particular params'<br/>
	 * )
	 * @param array $arrCommonParams	//common params to merge to the params of each query. The higher priority is by the individual params of each query
	 * @param boolean|function $mixedFailureMsg	//to generate customized transaction failure msg
	 * 	function (\Exception $ex, func_get_args(), \CDbTransaction $Transaction){}
	 *  boolean : true to use the default failure msg and false for no failure msg
	 * @return \CDbTransaction|boolean //\Yii::app()->db->beginTransaction() [->commit ->rollback] or false on failure
	 */
	public static function Transaction($arrQueries, $arrCommonParams = NULL, $mixedFailureMsg = true) {
		$DB = \Yii::app()->db;
		$Transaction = $DB->beginTransaction();
		try {
			foreach ($arrQueries as $Query) {
				if (!$Query)
					continue;
				if (!is_array($Query))
					$Query = array($Query);
				$arrParams = isset($Query[1]) ? $Query[1] : array();
				if ($arrCommonParams && is_array($arrCommonParams))
					$arrParams = array_merge($arrCommonParams, $arrParams);
				self::ClearUnusedParams($Query[0], $arrParams);
				$DB->createCommand($Query[0])->execute($arrParams);
			}
			$Transaction->commit();
		} catch (\Exception $ex) {
			$Transaction->rollback();
			if ($mixedFailureMsg) {
				$arrDetails = array(
					'func_get_args' => func_get_args(),
					'\Exception $ex' => $ex
				);
				if (is_bool($mixedFailureMsg))
					throw new \Err(__METHOD__, 'Query transaction failed', $arrDetails);
				elseif (is_callable($mixedFailureMsg)) {
					\Err::TraceMsg_Method(__METHOD__, 'Query transaction failed', $arrDetails);
					$mixedFailureMsg($ex, func_get_args(), $Transaction);
				}
			}
			return false;
		}
		return $Transaction;
	}

	/**
	 * Executes the SQL statement and returns query result.
	 * This method is for executing an SQL query that returns result set.
	 * @param string $strQuery
	 * @param array $arrParams input parameters (name=>value) for the SQL execution. This is an alternative
	 * to {@link bindParam} and {@link bindValue}. If you have multiple input parameters, passing
	 * them in this way can improve the performance. Note that if you pass parameters in this way,
	 * you cannot bind parameters or values using {@link bindParam} or {@link bindValue}, and vice versa.
	 * Please also note that all values are treated as strings in this case, if you need them to be handled as
	 * their real data types, you have to use {@link bindParam} or {@link bindValue} instead.
	 * @return \CDbDataReader the reader object for fetching the query result
	 * @throws CException execution failed
	 */
	public static function Query($strQuery, $arrParams = NULL, &$objCDbCommand = NULL) {
		if (!$arrParams)
			$arrParams = array();
		self::ClearUnusedParams($strQuery, $arrParams);
		$objCDbCommand = \Yii::app()->db->createCommand($strQuery);
		$objCDbDataReader = $objCDbCommand->query($arrParams);
		return $objCDbDataReader->count() ? $objCDbDataReader : NULL;
	}

	/**
	 * Don't use for large amount of data because the data array is returning.
	 * Using the PDO ::Query method we are returning and working with the reference of the data object
	 * @param string $strQuery
	 * @param array $arrParams
	 * @return array|NULL
	 */
	public static function GetTable($strQuery, $arrParams = NULL, &$objCDbDataReader = NULL, &$objCDbCommand = NULL) {
		$objCDbDataReader = self::Query($strQuery, $arrParams, $objCDbCommand);
		return $objCDbDataReader ? $objCDbDataReader->readAll() : $objCDbDataReader;
	}

	/**
	 * @param string $strQuery
	 * @param array $arrParams
	 * @return array|NULL
	 */
	public static function GetRow($strQuery, $arrParams = NULL, &$objCDbDataReader = NULL, &$objCDbCommand = NULL) {
		$objCDbDataReader = self::Query($strQuery, $arrParams, $objCDbCommand);
		return $objCDbDataReader ? $objCDbDataReader->read() : $objCDbDataReader;
	}

	/**
	 * @param string $strQuery
	 * @param array $arrParams
	 * @return mixed|NULL
	 */
	public static function GetField($strQuery, $arrParams = NULL, $columnIndex = 0, &$objCDbDataReader = NULL, &$objCDbCommand = NULL) {
		$objCDbDataReader = self::Query($strQuery, $arrParams, $objCDbCommand);
		return $objCDbDataReader ? $objCDbDataReader->readColumn($columnIndex) : $objCDbDataReader;
	}

	/**
	 * DONT FORGET !!!RETURN!!! IN YOUR FILTER
	 * @param dtArr $dt
	 * @param str $drArgName '$dr'
	 * @param str $PHPCode_BoolReturner 'return $dr[...]==...;'
	 * @return dtArr Result is a filtered datatable
	 */
	public static function Filter($dt, $PHPCode_BoolReturner = 'return true', $arrDBParams = array(), $drArgName = '$dr') {
		if (!$dt)
			return null;
		if ($arrDBParams && is_array($arrDBParams)) {
			$PHPCode_BoolReturner = Basics::InjectParams(
							$PHPCode_BoolReturner
							, $arrDBParams
							, function($PIdx, $Param) {
						if (is_bool($Param))
							$Param = $Param ? 'true' : 'false';
						elseif (is_null($Param))
							$Param = 'NULL';
						elseif (is_string($Param))
							$Param = "'" . T\DB::RealEscape($Param . '') . "'";
						return $Param;
					}
			);
		}
		$PHPCode_BoolReturner = trim($PHPCode_BoolReturner, ';');
		if (stripos($PHPCode_BoolReturner, 'return') === false)
			$PHPCode_BoolReturner = "return $PHPCode_BoolReturner";
		$Result = array_values(array_filter($dt, create_function($drArgName, $PHPCode_BoolReturner . ';')));
		return $Result && count($Result) ? $Result : null;
	}

	/**
	 * explodes $LabelFieldName by (.) and uses pieces as field name of datarow to make a string
	 * @param dr $dr
	 * @param str $LabelFieldName
	 * @return str
	 */
	public static function DRLabelMaker($dr, $LabelFieldName, $KMGFieldName = NULL) {
		$Result = '';
		foreach (explode('.', $LabelFieldName) as $Field) {
			$Result .= isset($dr[$Field]) ?
					($KMGFieldName && $Field === $KMGFieldName ?
							T\Basics::KMGMaker($dr[$Field]) :
							$dr[$Field]) :
					$Field;
		}
		return $Result;
	}

	/**
	 * This method contains expensive SQL queries. Don't use it for long data tables. Short tables will be OK
	 * @param type $TableName
	 * @param boolean $ReturnTheQuery
	 * @param boolean $Recovery
	 * @return integer
	 */
	static function GetNewID($TableName, $IDColumn = 'ID', $ReturnTheQuery = true, $Recovery = true) {
		//ID recovery
		$Query = "SELECT `$IDColumn` FROM (SELECT IFNULL("
				. ($Recovery ? "(SELECT t1.`$IDColumn`-1 AS `$IDColumn`"
						. " FROM `$TableName` AS t1"
						. " WHERE t1.`$IDColumn`>1"
						. " AND 0=(SELECT COUNT(t2.`$IDColumn`) FROM `$TableName` AS t2 WHERE t2.`$IDColumn`=t1.`$IDColumn`-1) LIMIT 1"
						. ")" : "NULL")
				. ", (IFNULL(MAX(`$IDColumn`), 0)+1)) AS `$IDColumn` FROM `$TableName` LIMIT 1) AS tbl";
		//CMD TEST
//mysql> insert into test set id=(select ID FROM (SELECT IFNULL(
//(SELECT t1.`ID`-1 AS `ID`
// FROM `test` AS t1
// WHERE t1.`ID`>1
// AND 0=(SELECT COUNT(t2.`ID`) FROM `test` AS t2 WHERE t2.`ID`=t1.`ID`-1) LIMIT 1
//)
//, (IFNULL(MAX(`ID`), 0)+1)) AS `ID` FROM `test` limit 1) AS tbl);
//mysql> insert into test(id) values((select ID FROM (SELECT IFNULL(
//(SELECT t1.`ID`-1 AS `ID`
// FROM `test` AS t1
// WHERE t1.`ID`>1
// AND 0=(SELECT COUNT(t2.`ID`) FROM `test` AS t2 WHERE t2.`ID`=t1.`ID`-1) LIMIT 1
//)
//, (IFNULL(MAX(`ID`), 0)+1)) AS `ID` FROM `test` limit 1) AS tbl));
		return $ReturnTheQuery ? $Query : self::GetField($Query);
	}

	/**
	 * Versus the ::GetNewID , the query of this method is not so expensive if we use the right where clause
	 * @param type $TableName
	 * @param type $WHEREClause
	 * @param type $arrDBParams
	 * @param array $arrOptions array(<br/>
	 *  'ReturnTheQuery' => true,<br/>
	 *  'Delim' => '_',<br/>
	 *  'Count' => -1,<br/>
	 *  'PrefixQuery' => "''"<br/>
	 * )<br/>
	 * 	*Delim : Delimiter<br/>
	 *  *PrefixQuery : this should be a query which will be prepnded to the result using a CONCAT(..., ...). If you pass a string remember to escape special chars and add quotes around it
	 * @return integer|string
	 */
	static function GetNewID_Combined(
	$TableName, $IDColumn = 'ID', $WHEREClause = '1=1', $arrDBParams = NULL, $arrOptions = array()) {
		$arrOptions = array_merge(array('ReturnTheQuery' => true, 'Delim' => '_', 'Count' => -1, 'PrefixQuery' => "''"), $arrOptions);
		$Query = "SELECT `$IDColumn` FROM ("
				. "SELECT CONCAT({$arrOptions['PrefixQuery']}, IFNULL(MAX(SUBSTRING_INDEX(`$IDColumn`, '{$arrOptions['Delim']}', {$arrOptions['Count']})),0)+1) AS `$IDColumn` FROM `$TableName` WHERE $WHEREClause LIMIT 1"
				. ") AS tbl";
		return $arrOptions['ReturnTheQuery'] ? $Query : self::GetField($Query, $arrDBParams);
//		mysql> insert into test(id) values(concat('1_', (
//		select ID from (SELECT IFNULL(MAX(SUBSTRING_INDEX(`ID`, '_', -1)), 0)+1 AS `ID` FROM `test` WHERE 1 = 1 LIMIT 1) tbl
//		)));
	}

	/**
	 * @param array $arrDataTable
	 * @param string $ColumnName
	 * @return array
	 */
	static function GetColumnValues($arrDataTable, $ColumnName) {
		$ColumnValues = array();
		if ($arrDataTable)
			foreach ($arrDataTable as $row) {
				$ColumnValues[] = $row[$ColumnName];
			}
		return $ColumnValues;
	}

	/**
	 * Unique random hashcode for a db column
	 * @param string $DBTableName
	 * @param string $DBColumnName
	 * @return string
	 */
	public static function GetUniqueCode($DBTableName = NULL, $DBColumnName = NULL) {
		if ($DBTableName) {
			$IsUniqueCode = false;
			while (!$IsUniqueCode) {
				$Code = md5(uniqid(mt_rand(0, self::UniqueCode_MaxRand), true));
				if (T\DB::GetField("SELECT COUNT(*) FROM $DBTableName WHERE `$DBColumnName`=:code"
								, array(':code' => $Code)) == 0)
					$IsUniqueCode = true;
			}
		} else
			$Code = uniqid(mt_rand(0, self::UniqueCode_MaxRand), true);
		return $Code;
	}

}
