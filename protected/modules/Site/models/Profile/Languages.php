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
 * @property-read array $dtLanguages
 * @property string $txtLanguages
 */
class Languages extends \Base\FormModel {

	public function getPostName() {
		return "UserLanguages";
	}

	protected function XSSPurify_Exceptions() {
		return "ddlRate";
	}

	public $txtLanguage;
	public $ddlRate;
	private $_txtLanguages = null;

	public function gettxtLanguages() {
		$txtLanguages = &$this->_txtLanguages;
		foreach ($this->dtLanguages as $drLanguage) {
			$txtLanguages.=',' . $drLanguage['Language'];
		}
		return $txtLanguages;
	}

	public function settxtLanguages($val) {
		$this->_txtLanguages = $val;
	}

	//Language Rates
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

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('txtLanguage, ddlRate', 'required'),
			array_merge(array('txtLanguage', 'length'), $vl->LongTitle),
			array('ddlRate', 'in'
				, 'range' => array_keys($this->arrRates)),
		);
	}

	public function attributeLabels() {
		return array(
			'txtLanguages' => \t2::Site_User('Languages'),
			'ddlRate' => \t2::Site_User('Rate'),
		);
	}

	public function getdtLanguages() {
		static $dt = null;
		if (!$dt) {
			$dt = T\DB::GetTable(
							"SELECT ul.`SelfRate`, lng.`Language`"
							. " FROM `_user_langs` ul"
							. " INNER JOIN `_languages` lng ON lng.`ID`=ul.`LangID`"
							. " WHERE ul.`UID`=:uid"
							, array(
						':uid' => self::$UserID,
							)
			);
		}
		return $dt? : array();
	}

	/**
	 * @var int user id to be used in next transactions
	 */
	public static $UserID = null;
	private static $IsValid = true;
	private static $Languages = array();
	private static $arrTransactions = array();

	/**
	 * Deletes the unused Languages(removed Languages) of the tag list<br/>
	 * This method is called in self::Commit()
	 * @return void
	 */
	private static function DeleteUnusedLanguages() {
		$arrParams = array();
		foreach (self::$Languages as $idx => $item) {
			$arrParams[":lng$idx"] = $item;
		}
		$RemovableIDs = T\DB::GetField(
						"SELECT GROUP_CONCAT(lng.`ID` SEPARATOR ',') AS IDs"
						. " FROM `_user_langs` ul"
						. " INNER JOIN (SELECT 1) tmp ON ul.`UID` = :uid"
						. " INNER JOIN `_languages` lng ON lng.`ID` = ul.`LangID`"
						. (count($arrParams) ?
								" WHERE lng.`Language` != " . implode(' AND lng.`Language` != ', array_keys($arrParams)) :
								"")
						, array_merge($arrParams, array(':uid' => self::$UserID)));
		if (!$RemovableIDs)
			return;
		$RemovableIDs = explode(',', $RemovableIDs);
		self::$arrTransactions[] = array(
			"DELETE FROM `_user_langs` WHERE `UID`=:uid AND (`LangID` = " . implode(' OR `LangID` = ', $RemovableIDs) . ")"
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
		self::DeleteUnusedLanguages();
		\Tools\GCC::RogueLanguages();
		if (self::$arrTransactions) {
			$Result = T\DB::Transaction(self::$arrTransactions);
			self::$arrTransactions = array();
			return $Result;
		}
		return false;
	}

	/**
	 * @return boolean|array returns the ready array of transactions of this model object to be mereged to other transactions
	 */
	public function getTransactions() {
		if (!$this->validate()) {
			self::$IsValid = false;
			return false;
		}
		self::$Languages[] = $this->txtLanguage;
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
					':lang' => $this->txtLanguage,
					':selfrate' => $this->ddlRate,
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
		if ($arrTransactions)
			self::$arrTransactions = array_merge(self::$arrTransactions, $arrTransactions);
	}

}
