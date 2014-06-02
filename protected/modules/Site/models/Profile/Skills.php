<?php

namespace Site\models\Profile;

use \Consts as C;
use \Tools as T;

/**
 * Set ::$UserID statically<br>
 * create multiple of this model (like a tabular form). Set attrs and call ->PushTransaction for each one<br>
 * finally by calling Skills::Commit statically all transactions will be committed if the validation result was valid (validation is automatically)<br>
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-read array $arrRates
 * @property-read array $dtSkills
 * @property-read string $MaxItems
 * @property string $txtSkills
 */
class Skills extends \Base\FormModel {

	public function getPostName() {
		return "UserSkills";
	}

	protected function XSSPurify_Exceptions() {
		return "ddlRate";
	}

	public $txtSkill;
	public $ddlRate;
	private $_txtSkills = null;

	public function gettxtSkills() {
		$txtSkills = &$this->_txtSkills;
		foreach ($this->dtSkills as $drSkill) {
			$txtSkills.=',' . $drSkill['Skill'];
		}
		$txtSkills = trim($txtSkills, ',');
		return $txtSkills;
	}

	public function settxtSkills($val) {
		$this->_txtSkills = $val;
	}

	public function getMaxItems() {
		return T\Settings::GetValue('MaxResumeTagItemsPerCase');
	}

	//Skill Rates
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
			array('txtSkill, ddlRate', 'required'),
			array_merge(array('txtSkill', 'length'), $vl->LongTitle),
			array('ddlRate', 'in'
				, 'range' => array_keys($this->arrRates)),
		);
	}

	protected function afterValidate() {
		if (count(self::$Skills) >= $this->MaxItems)
			$this->addError('', \t2::site_site('You have reached the maximum'));
	}

	public function attributeLabels() {
		return array(
			'txtSkills' => \t2::site_site('Skills') . ' ' . \t2::site_site('MaxItems', array($this->MaxItems)),
			'ddlRate' => \t2::site_site('Rate'),
		);
	}

	public function getdtSkills() {
		static $dt = null;
		if (!$dt) {
			$dt = T\DB::GetTable(
							"SELECT uskl.`SelfRate`, skl.`Skill`"
							. " FROM `_user_skills` uskl"
							. " INNER JOIN (SELECT 1) tmp ON uskl.`UID`=:uid"
							. " INNER JOIN `_skills` skl ON skl.`ID`=uskl.`SkillID`"
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
	private static $Skills = array();
	private static $arrTransactions = array();

	/**
	 * Deletes the unused skills(removed skills) of the tag list<br/>
	 * This method is called in self::Commit()
	 * @return void
	 */
	private static function DeleteUnusedSkills() {
		$arrParams = array();
		foreach (self::$Skills as $idx => $item) {
			$arrParams[":skl$idx"] = $item;
		}
		$RemovableIDs = T\DB::GetField(
						"SELECT GROUP_CONCAT(skl.`ID` SEPARATOR ',') AS IDs"
						. " FROM `_user_skills` uskl"
						. " INNER JOIN (SELECT 1) tmp ON uskl.`UID` = :uid"
						. " INNER JOIN `_skills` skl ON skl.`ID` = uskl.`SkillID`"
						. (count($arrParams) ?
								" WHERE skl.`Skill` != " . implode(' AND skl.`Skill` != ', array_keys($arrParams)) :
								"")
						, array_merge($arrParams, array(':uid' => self::$UserID)));
		if (!$RemovableIDs)
			return;
		$RemovableIDs = explode(',', $RemovableIDs);
		self::$arrTransactions[] = array(
			"DELETE FROM `_user_skills` WHERE `UID`=:uid AND (`SkillID` = " . implode(' OR `SkillID` = ', $RemovableIDs) . ")"
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
			throw new \Err(__METHOD__, 'UserID has not been set in Skill model');
		if (!self::$IsValid)
			return false;
		self::DeleteUnusedSkills();
		\Tools\GCC::RogueSkills();
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
		self::$Skills[] = $this->txtSkill;
		return array(
			array(
				"INSERT INTO `_user_skills` SET"
				. " `UID`=:uid"
				. ", `SkillID`=skills_getCreatedSkillID(:skill)"
				. ", `SelfRate`=:selfrate"
				. " ON DUPLICATE KEY UPDATE"
				. " `SelfRate`=:selfrate"
				, array(
					':uid' => self::$UserID,
					':skill' => $this->txtSkill,
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
