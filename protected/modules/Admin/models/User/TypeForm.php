<?php

namespace Admin\models\User;

use \Consts as C;
use \Tools as T;

class TypeForm extends \Base\FormModel {

	public function PostName() {
		return $this->scenario;
	}

	const LogicMaxLen = 25;
	const LogicPattern = C\Regexp::LogicName;
	const TitleMaxLen = 50;

	private $_RowID = NULL;
	public $txtLogicName;
	public $txtTitle;
	public $chkIsActive;
	public $chkIsDefault;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('txtLogicName, txtTitle', 'required',
				'on' => 'insert, update'),
			#
			array('txtLogicName', 'length',
				'max' => self::LogicMaxLen,
				'on' => 'insert, update'),
			array('txtLogicName', 'match',
				'pattern' => self::LogicPattern,
				'on' => 'insert, update'),
			array('txtTitle', 'length',
				'max' => self::TitleMaxLen,
				'on' => 'insert, update'),
			#
			array('chkIsActive, chkIsDefault', 'boolean',
				'on' => 'insert, update'),
			array('chkIsDefault', 'UniqueDefaultValidate',
				'on' => 'insert, update'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'txtLogicName' => \Lng::Admin('Common', 'Logic name'),
			'txtTitle' => \Lng::Admin('Common', 'Title'),
			'chkIsActive' => \Lng::Admin('Common', 'Is active'),
			'chkIsDefault' => \Lng::Admin('Common', 'Is default'),
		);
	}

	function Select(\Base\DataGridParams $DGP) {
		$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_user_types`');
		$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
		return T\DB::GetTable('
			SELECT t.*, u.UserTypeID AS IsUsed
			FROM `_user_types` AS t
			LEFT JOIN (SELECT `UserTypeID` FROM `_users` GROUP BY `UserTypeID`) AS u
				ON t.ID=u.UserTypeID
			ORDER BY ' . $DGP->Sort . '
			LIMIT ' . $Limit);
	}

	function Insert(\Base\DataGridParams $DGP) {
		if ($this->validate()) {
			$ID = T\DB::GetNewID('_user_types');
			T\DB::Execute("
				INSERT INTO `_user_types`(`ID`, `LogicName`, `Title`, `IsDefault`, `IsActive`)
				VALUES (:id, :lgc, :ttl, :dflt, :act)"
					, array(
				':id' => $ID,
				':lgc' => $this->txtLogicName,
				':ttl' => $this->txtTitle,
				':dflt' => $this->chkIsDefault ? : NULL,
				':act' => $this->chkIsActive,
			));
		} else {
			\html::AjaxMsg_Exit(\CHtml::errorSummary($this));
		}
	}

	function Update(\Base\DataGridParams $DGP) {
		if ($DGP->RowID && $this->validate()) {
			$this->_RowID = $DGP->RowID;
			T\DB::Execute("
				UPDATE `_user_types`
				SET `LogicName`=:lgc, `Title`=:ttl, `IsDefault`=:dflt, `IsActive`=:act
				WHERE `ID`=:id"
					, array(
				':id' => $DGP->RowID,
				':lgc' => $this->txtLogicName,
				':ttl' => $this->txtTitle,
				':dflt' => $this->chkIsDefault ? : NULL,
				':act' => $this->chkIsActive,
			));
		} else {
			\html::AjaxMsg_Exit(\CHtml::errorSummary($this));
		}
	}

	function Delete(\Base\DataGridParams $DGP) {
		if ($DGP->RowID) {
			T\DB::Execute("DELETE FROM `_user_types` WHERE `ID`=:id", array(':id' => $DGP->RowID));
		}
	}

	function UniqueDefaultValidate($attr, $params) {
		$ID = &$this->_RowID;
//		\Err::DebugBreakPoint('SELECT COUNT(*) FROM `_user_types` WHERE `IsDefault`'
//						. ($ID ? " AND ID!=:id" : ""));
		if ($this->$attr && T\DB::GetField('SELECT COUNT(*) FROM `_user_types` WHERE `IsDefault`'
						. ($ID ? " AND ID!=:id" : ""), array(':id' => $ID)))
			$this->addError('chkIsDefault', \Lng::Admin('User', 'Only one default user type'));
	}

}
