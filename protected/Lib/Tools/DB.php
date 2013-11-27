<?php

namespace Tools;

use \Tools as T;
use \Consts as C;

/**
 * Tondarweb DB Tools (migrated from F3 to yii)
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb+Odinid Portal
 * @version 2
 * @copyright (c) Abbas Ali Hashemian + (c) Odinid
 * @access public
 */
class DB {

	const CharsetLevel1 = 'iso-8859-1';
	const CharsetLevel2 = 'utf-8';
	const CharsetLevel1_DB = 'ascii';
	const CharsetLevel2_DB = 'utf8';

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
		$objCDbCommand = \Yii::app()->db->createCommand($strQuery);
		return $objCDbCommand->execute($arrParams);
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
		$objCDbCommand = \Yii::app()->db->createCommand($strQuery);
		return $objCDbCommand->query($arrParams);
	}

	/**
	 * Don't use for large amount of data because the data array is returning.
	 * Using the PDO ::Query method we are returning and working with the reference of the data object
	 * @param string $strQuery
	 * @param array $arrParams
	 * @return array | empty array
	 */
	public static function GetTable($strQuery, $arrParams = NULL, &$objCDbDataReader = NULL, &$objCDbCommand = NULL) {
		$objCDbDataReader = self::Query($strQuery, $arrParams, $objCDbCommand);
		return $objCDbDataReader->readAll();
	}

	/**
	 * @param string $strQuery
	 * @param array $arrParams
	 * @return array | false
	 */
	public static function GetRow($strQuery, $arrParams = NULL, &$objCDbDataReader = NULL, &$objCDbCommand = NULL) {
		$objCDbDataReader = self::Query($strQuery, $arrParams, $objCDbCommand);
		return $objCDbDataReader->read();
	}

	/**
	 * @param string $strQuery
	 * @param array $arrParams
	 * @return mixed | false
	 */
	public static function GetField($strQuery, $arrParams = NULL, $columnIndex = 0, &$objCDbDataReader = NULL, &$objCDbCommand = NULL) {
		$objCDbDataReader = self::Query($strQuery, $arrParams, $objCDbCommand);
		return $objCDbDataReader->readColumn($columnIndex);
	}

	/**
	 * This method contains expensive SQL queries. Don't use it for long data tables. Short tables will be OK
	 * @param type $TableName
	 * @param type $Recovery
	 * @return integer|string
	 */
	static function GetNewID($TableName, $Recovery = true) {
		//ID recovery
		if ($Recovery)
			$ID = self::GetField("SELECT t1.ID-1 AS ID FROM $TableName AS t1 WHERE t1.ID>1 AND 0=(SELECT COUNT(t2.ID) FROM $TableName AS t2 WHERE t2.ID=t1.ID-1) LIMIT 1
				UNION ALL SELECT (IFNULL(MAX(ID), 0)+1) AS ID FROM $TableName");
		else
			$ID = self::GetField("SELECT (IFNULL(MAX(ID), 0)+1) AS ID FROM $TableName");
		return $ID ? $ID : 1;
	}

	/**
	 * Versus the ::GetNewID , the query of this method is not so expensive. Be relax
	 * @param type $TableName
	 * @param type $WHEREClause
	 * @param type $arrDBParams
	 * @param type $Delim
	 * @param type $Count
	 * @return type
	 */
	static function GetNewID_VarCharCol($TableName, $WHEREClause = '1=1', $arrDBParams = NULL, $Delim = '_', $Count = -1) {
		$ID = self::GetField("SELECT MAX(SUBSTRING_INDEX(ID, '$Delim', $Count)+1) AS ID FROM $TableName WHERE $WHEREClause", $arrDBParams);
		$ID = @$ID[0]['ID'];
		return $ID ? $ID : 1;
	}

}

?>
