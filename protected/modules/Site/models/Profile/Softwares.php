<?php

namespace Site\models\Profile;

use \Consts as C;
use \Tools as T;

/**
 * Set ::$UserID statically<br>
 * create multiple of this model (like a tabular form). Set attrs and call ->PushTransaction for each one<br>
 * finally by calling Softwares::Commit statically all transactions will be committed if the validation result was valid (validation is automatically)<br>
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-read array $arrRates
 * @property-read array $dtSoftwares
 * @property string $txtSoftwares
 */
class Softwares extends \Base\FormModel {

	public function getPostName() {
		return "UserSoftwares";
	}

	protected function XSSPurify_Exceptions() {
		return "ddlRate";
	}

	public $txtSoftware;
	public $ddlRate;
	private $_txtSoftwares = null;

	public function gettxtSoftwares() {
		$txtSoftwares = &$this->_txtSoftwares;
		foreach ($this->dtSoftwares as $drSoftware) {
			$txtSoftwares.=',' . $drSoftware['Software'];
		}
		return $txtSoftwares;
	}

	public function settxtSoftwares($val) {
		$this->_txtSoftwares = $val;
	}

	//Software Rates
	const Rate_Beginner = 'Beginner';
	const Rate_Intermediate = 'Intermediate';
	const Rate_Advanced = 'Advanced';
	const Rate_Expert = 'Expert';

	function getarrRates() {
		return array(
			self::Rate_Beginner => self::Rate_Beginner,
			self::Rate_Intermediate => self::Rate_Intermediate,
			self::Rate_Advanced => self::Rate_Advanced,
			self::Rate_Expert => self::Rate_Expert,
		);
	}

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('txtSoftware, ddlRate', 'required'),
			array_merge(array('txtSoftware', 'length'), $vl->LongTitle),
			array('ddlRate', 'in'
				, 'range' => array_keys($this->arrRates)),
		);
	}

	public function attributeLabels() {
		return array(
			'txtSoftwares' => \t2::Site_User('Softwares'),
			'ddlRate' => \t2::Site_User('Rate'),
		);
	}

	public function getdtSoftwares() {
		static $dt = null;
		if (!$dt) {
			$dt = T\DB::GetTable(
							"SELECT usft.`SelfRate`, sft.`Software`"
							. " FROM `_user_softwares` usft"
							. " INNER JOIN `_softwares` sft ON sft.`ID`=usft.`SoftwareID`"
							. " WHERE usft.`UID`=:uid"
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
	private static $Softwares = array();
	private static $arrTransactions = array();

	/**
	 * Deletes the unused Softwares(removed Softwares) of the tag list<br/>
	 * This method is called in self::Commit()
	 * @return void
	 */
	private static function DeleteUnusedSoftwares() {
		$arrParams = array();
		foreach (self::$Softwares as $idx => $item) {
			$arrParams[":sft$idx"] = $item;
		}
		$RemovableIDs = T\DB::GetField(
						"SELECT GROUP_CONCAT(sft.`ID` SEPARATOR ',') AS IDs"
						. " FROM `_user_softwares` usft"
						. " INNER JOIN (SELECT 1) tmp ON usft.`UID` = :uid"
						. " INNER JOIN `_softwares` sft ON sft.`ID` = usft.`SoftwareID`"
						. (count($arrParams) ?
								" WHERE sft.`Software` != " . implode(' AND sft.`Software` != ', array_keys($arrParams)) :
								"")
						, array_merge($arrParams, array(':uid' => self::$UserID)));
		if (!$RemovableIDs)
			return;
		$RemovableIDs = explode(',', $RemovableIDs);
		self::$arrTransactions[] = array(
			"DELETE FROM `_user_softwares` WHERE `UID`=:uid AND (`SoftwareID` = " . implode(' OR `SoftwareID` = ', $RemovableIDs) . ")"
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
			throw new \Err(__METHOD__, 'UserID has not been set in Softwares model');
		if (!self::$IsValid)
			return false;
		self::DeleteUnusedSoftwares();
		\Tools\GCC::RogueSoftwares();
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
		self::$Softwares[] = $this->txtSoftware;
		return array(
			array(
				"INSERT INTO `_user_softwares` SET"
				. " `UID`=:uid"
				. ", `SoftwareID`=softwares_getCreatedSoftwareID(:software)"
				. ", `SelfRate`=:selfrate"
				. " ON DUPLICATE KEY UPDATE"
				. " `SelfRate`=:selfrate"
				, array(
					':uid' => self::$UserID,
					':software' => $this->txtSoftware,
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
