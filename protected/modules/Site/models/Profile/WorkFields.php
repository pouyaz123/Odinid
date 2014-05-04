<?php

namespace Site\models\Profile;

use \Consts as C;
use \Tools as T;

/**
 * Set ::$UserID statically<br>
 * create multiple of this model (like a tabular form). Set attrs and call ->PushTransaction for each one<br>
 * finally by calling WorkFields::Commit statically all transactions will be committed if the validation result was valid (validation is automatically)<br>
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-read array $dtWorkFields
 * @property-read string $MaxItems
 * @property string $txtWorkFields
 */
class WorkFields extends \Base\FormModel {

	public function getPostName() {
		return "UserWorkFields";
	}

	public $txtWorkField;
	private $_txtWorkFields = null;

	public function gettxtWorkFields() {
		$txtWorkFields = &$this->_txtWorkFields;
		foreach ($this->dtWorkFields as $drWorkField) {
			$txtWorkFields.=',' . $drWorkField['WorkField'];
		}
		return $txtWorkFields;
	}

	public function settxtWorkFields($val) {
		$this->_txtWorkFields = $val;
	}

	public function getMaxItems() {
		return T\Settings::GetValue('MaxResumeTagItemsPerCase');
	}

	public function rules() {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('txtWorkField', 'required'),
			array_merge(array('txtWorkField', 'length'), $vl->LongTitle),
		);
	}

	protected function afterValidate() {
		if (count(self::$WorkFields) >= $this->MaxItems)
			$this->addError('', \t2::site_site('You have reached the maximum'));
	}

	public function attributeLabels() {
		return array(
			'txtWorkFields' => \t2::site_site('Work fields') . ' ' . \t2::site_site('MaxItems', array($this->MaxItems)),
		);
	}

	public function getdtWorkFields() {
		static $dt = null;
		if (!$dt) {
			$dt = T\DB::GetTable(
							"SELECT wfld.`WorkField`"
							. " FROM `_user_workfields` uwfld"
							. " INNER JOIN (SELECT 1) tmp ON uwfld.`UID`=:uid"
							. " INNER JOIN `_workfields` wfld ON wfld.`ID`=uwfld.`WorkFieldID`"
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
	private static $WorkFields = array();
	private static $arrTransactions = array();

	/**
	 * Deletes the unused WorkFields(removed WorkFields) of the tag list<br/>
	 * This method is called in self::Commit()
	 * @return void
	 */
	private static function DeleteUnusedWorkFields() {
		$arrParams = array();
		foreach (self::$WorkFields as $idx => $item) {
			$arrParams[":wfld$idx"] = $item;
		}
		$RemovableIDs = T\DB::GetField(
						"SELECT GROUP_CONCAT(wfld.`ID` SEPARATOR ',') AS IDs"
						. " FROM `_user_workfields` uwfld"
						. " INNER JOIN (SELECT 1) tmp ON uwfld.`UID` = :uid"
						. " INNER JOIN `_workfields` wfld ON wfld.`ID` = uwfld.`WorkFieldID`"
						. (count($arrParams) ?
								" WHERE wfld.`WorkField` != " . implode(' AND wfld.`WorkField` != ', array_keys($arrParams)) :
								"")
						, array_merge($arrParams, array(':uid' => self::$UserID)));
		if (!$RemovableIDs)
			return;
		$RemovableIDs = explode(',', $RemovableIDs);
		self::$arrTransactions[] = array(
			"DELETE FROM `_user_workfields` WHERE `UID`=:uid AND (`WorkFieldID` = " . implode(' OR `WorkFieldID` = ', $RemovableIDs) . ")"
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
			throw new \Err(__METHOD__, 'UserID has not been set in WorkField model');
		if (!self::$IsValid)
			return false;
		self::DeleteUnusedWorkFields();
		\Tools\GCC::RogueWorkFields();
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
		self::$WorkFields[] = $this->txtWorkField;
		return array(
			array(
				"INSERT IGNORE INTO `_user_workfields` SET"
				. " `UID`=:uid"
				. ", `WorkFieldID`=workFields_getCreatedWorkFieldID(:wf)"
				, array(
					':uid' => self::$UserID,
					':wf' => $this->txtWorkField,
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
