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
 */
class Skills extends \Base\FormModel {

	public function getPostName() {
		return "UserSkills";
	}
	
	protected function XSSPurify_Exceptions() {
		return "rdoRate";
	}

	public $txtSkill;
	public $rdoRate;

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

	public function rules(\CEvent $e) {
		$vl = \ValidationLimits\User::GetInstance();
		return array(
			array('txtSkill', 'required'
				, 'except' => 'Delete'),
			array_merge(array('txtSkill', 'length'
				, 'except' => 'Delete'), $vl->LongTitle),
			array('rdoRate', 'in'
				, 'range' => array_keys($this->arrRates)
				, 'except' => 'Delete'),
		);
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
		$arrSkillParams = array();
		foreach (self::$Skills as $idx => $Skill) {
			$arrSkillParams[":skl$idx"] = $Skill;
		}
		$RemovableSkillIDs = T\DB::GetField(
						"SELECT GROUP_CONCAT(s.`ID` SEPARATOR ',') AS IDs"
						. " FROM `_user_skills` us"
						. " INNER JOIN (SELECT 1) tmp ON us.`UID` = :uid"
						. " INNER JOIN `_skills` s ON s.`ID` = us.`SkillID`"
						. count($arrSkillParams) ?
								" WHERE s.`Skill` != " . implode(' AND s.`Skill` != ', array_keys($arrSkillParams)) :
								""
						, array_merge($arrSkillParams, array(':uid' => self::$UserID)));
		if (!$RemovableSkillIDs)
			return;
		$RemovableSkillIDs = explode(',', $RemovableSkillIDs);
		self::$arrTransactions[] = array(
			"DELETE FROM `_user_skills` WHERE `UID`=:uid AND (`SkillID` = " . implode(' OR `SkillID` = ', $RemovableSkillIDs) . ")"
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
