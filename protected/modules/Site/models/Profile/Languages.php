<?php

namespace Site\models\Profile;

use \Consts as C;
use \Tools as T;

/**
 * Set ::$UserID statically<br>
 * create multiple of this model (like a tabular form). Set attrs and call ->PushTransaction for each one<br>
 * finally by calling Languages::Commit statically all transactions will be committed if the validation result was valid (validation is automatically)<br>
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-read array $arrRates
 */
class Languages extends \Base\FormModel {

	public function getPostName() {
		return "UserLangs";
	}
	
	protected function XSSPurify_Exceptions() {
		return "rdoRate";
	}

	public $txtLang;
	public $rdoRate;

	//Lang Rates
	const Rate_Beginner = 'Beginner';
	const Rate_Intermediate = 'Intermediate';
	const Rate_Advanced = 'Advanced';
	const Rate_Mother = 'Mother';

	function getarrRates() {
		return array(
			self::Rate_Beginner => self::Rate_Beginner,
			self::Rate_Intermediate => self::Rate_Intermediate,
			self::Rate_Advanced => self::Rate_Advanced,
			self::Rate_Mother => self::Rate_Mother,
		);
	}

	public function rules(\CEvent $e) {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('txtLang', 'required'
				, 'except' => 'Delete'),
			array_merge(array('txtLang', 'length'
				, 'except' => 'Delete'), $vl->Title),
			array('rdoRate', 'in'
				, 'range' => array_keys($this->arrRates)
				, 'except' => 'Delete'),
		);
	}

	/**
	 * @var int user id to be used in next transactions
	 */
	public static $UserID = null;
	protected static $IsValid = true;
	protected static $Langs = array();
	protected static $arrTransactions = array();

	/**
	 * Deletes the unused langs(removed langs) of the tag list<br/>
	 * This method is called in self::Commit()
	 * @return void
	 */
	protected static function DeleteUnusedLangs() {
		$arrLangParams = array();
		foreach (self::$Langs as $idx => $Lang) {
			$arrLangParams[":skl$idx"] = $Lang;
		}
		$RemovableLangIDs = T\DB::GetField(
						"SELECT GROUP_CONCAT(l.`ID` SEPARATOR ',') AS IDs"
						. " FROM `_user_langs` ul"
						. " INNER JOIN (SELECT 1) tmp ON ul.`UID` = :uid"
						. " INNER JOIN `_languages` l ON l.`ID` = ul.`LangID`"
						. count($arrLangParams) ?
								" WHERE l.`Language` != " . implode(' AND l.`Language` != ', array_keys($arrLangParams)) :
								""
						, array_merge($arrLangParams, array(':uid' => self::$UserID)));
		if (!$RemovableLangIDs)
			return;
		$RemovableLangIDs = explode(',', $RemovableLangIDs);
		self::$arrTransactions[] = array(
			"DELETE FROM `_user_langs` WHERE `UID`=:uid AND (`LangID` = " . implode(' OR `LangID` = ', $RemovableLangIDs) . ")"
			, array(
				':uid' => self::$UserID
			)
		);
	}

	/**
	 * Commit the transactions has been added through the recent model objects<br>
	 * The transaction list will get removed after the commision
	 * @param int $UserID
	 * @return boolean|\CDbTransaction boolean on failure or invalid inputs
	 * @throws \Err
	 */
	public static function Commit($UserID = null) {
		if ($UserID)
			self::$UserID = $UserID;
		if (!self::$UserID)
			throw new \Err(__METHOD__, 'UserID has not been set in Language model');
		if (!self::$IsValid)
			return false;
		self::DeleteUnusedLangs();
		$Result = T\DB::Transaction(self::$arrTransactions);
		self::$arrTransactions = array();
		return $Result;
	}

	/**
	 * @return boolean|array returns the ready array of transactions of this model object to be mereged to other transactions
	 */
	public function getTransactions() {
		if (!$this->validate()) {
			self::$IsValid = false;
			return false;
		}
		self::$Langs[] = $this->txtLang;
		return array(
			array(
				"INSERT INTO `_user_langs` SET"
				. " `UID`=:uid"
				. ", `LangID`=langs_getCreatedLangID(:lang)"
				. ", `SelfRate`=:selfrate"
				. " ON DUPLICATE KEY UPDATE"
				. " `SelfRate`=:selfrate"
				, array(
					':uid' => self::$UserID,
					':lang' => $this->txtLang,
					':selfrate' => $this->rdoRate,
				)
			)
		);
	}

	/**
	 * pushes the transactions to the statically stored transactions in this class
	 * @param array $arrTransactions if was omitted the transactions of this model object will be used instead
	 */
	public function PushTransactions($arrTransactions = null) {
		if (!$arrTransactions)
			$arrTransactions = $this->getTransactions();
		self::$arrTransactions = array_merge(self::$arrTransactions, $arrTransactions);
	}

}
