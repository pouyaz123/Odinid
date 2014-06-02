<?php

namespace Site\models\Profile;

use \Consts as C;
use \Tools as T;

/**
 * Set ::$UserID statically<br>
 * create multiple of this model (like a tabular form). Set attrs and call ->PushTransaction for each one<br>
 * finally by calling Tools::Commit statically all transactions will be committed if the validation result was valid (validation is automatically)<br>
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-read array $arrRates
 * @property-read array $dtTools
 * @property-read string $MaxItems
 * @property string $txtTools
 */
class Tools extends \Base\FormModel {

	public function getPostName() {
		return "UserTools";
	}

	protected function XSSPurify_Exceptions() {
		return "ddlRate";
	}

	public $txtTool;
	public $ddlRate;
	private $_txtTools = null;

	public function gettxtTools() {
		$txtTools = &$this->_txtTools;
		foreach ($this->dtTools as $drTool) {
			$txtTools.=',' . $drTool['Tool'];
		}
		$txtTools = trim($txtTools, ',');
		return $txtTools;
	}

	public function settxtTools($val) {
		$this->_txtTools = $val;
	}
	
	public function getMaxItems() {
		return T\Settings::GetValue('MaxResumeTagItemsPerCase');
	}

	//Tool Rates
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
			array('txtTool, ddlRate', 'required'),
			array_merge(array('txtTool', 'length'), $vl->LongTitle),
			array('ddlRate', 'in'
				, 'range' => array_keys($this->arrRates)),
		);
	}

	protected function afterValidate() {
		if (count(self::$Tools) >= $this->MaxItems)
			$this->addError('', \t2::site_site('You have reached the maximum'));
	}

	public function attributeLabels() {
		return array(
			'txtTools' => \t2::site_site('Tools') . ' ' . \t2::site_site('MaxItems', array($this->MaxItems)),
			'ddlRate' => \t2::site_site('Rate'),
		);
	}

	public function getdtTools() {
		static $dt = null;
		if (!$dt) {
			$dt = T\DB::GetTable(
							"SELECT utl.`SelfRate`, tl.`Tool`"
							. " FROM `_user_tools` utl"
							. " INNER JOIN (SELECT 1) tmp ON utl.`UID`=:uid"
							. " INNER JOIN `_tools` tl ON tl.`ID`=utl.`ToolID`"
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
	private static $Tools = array();
	private static $arrTransactions = array();

	/**
	 * Deletes the unused Tools(removed Tools) of the tag list<br/>
	 * This method is called in self::Commit()
	 * @return void
	 */
	private static function DeleteUnusedTools() {
		$arrParams = array();
		foreach (self::$Tools as $idx => $item) {
			$arrParams[":tl$idx"] = $item;
		}
		$RemovableIDs = T\DB::GetField(
						"SELECT GROUP_CONCAT(tl.`ID` SEPARATOR ',') AS IDs"
						. " FROM `_user_tools` utl"
						. " INNER JOIN (SELECT 1) tmp ON utl.`UID` = :uid"
						. " INNER JOIN `_tools` tl ON tl.`ID` = utl.`ToolID`"
						. (count($arrParams) ?
								" WHERE tl.`Tool` != " . implode(' AND tl.`Tool` != ', array_keys($arrParams)) :
								"")
						, array_merge($arrParams, array(':uid' => self::$UserID)));
		if (!$RemovableIDs)
			return;
		$RemovableIDs = explode(',', $RemovableIDs);
		self::$arrTransactions[] = array(
			"DELETE FROM `_user_tools` WHERE `UID`=:uid AND (`ToolID` = " . implode(' OR `ToolID` = ', $RemovableIDs) . ")"
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
			throw new \Err(__METHOD__, 'UserID has not been set in Tools model');
		if (!self::$IsValid)
			return false;
		self::DeleteUnusedTools();
		\Tools\GCC::RogueTools();
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
		self::$Tools[] = $this->txtTool;
		return array(
			array(
				"INSERT INTO `_user_tools` SET"
				. " `UID`=:uid"
				. ", `ToolID`=tools_getCreatedToolID(:tool)"
				. ", `SelfRate`=:selfrate"
				. " ON DUPLICATE KEY UPDATE"
				. " `SelfRate`=:selfrate"
				, array(
					':uid' => self::$UserID,
					':tool' => $this->txtTool,
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
