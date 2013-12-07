<?php

namespace Admin\models\User;

use \Consts as C;
use \Tools as T;

class Type extends \Base\FormModel {

	public function PostName() {
		return $this->scenario;
	}

	const LogicMaxLen = 25;
	const LogicPattern = C\Regexp::LogicName;
	const TitleMaxLen = 50;

	private $_RowID = -1;
	public $txtLogicName;
	public $txtTitle;
	public $chkIsActive = 0;
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
			array('txtLogicName', 'IsUnique',
				'SQL' => 'SELECT COUNT(*) FROM `_user_types` WHERE `LogicName`=:val AND `ID`!=:pk LIMIT 1',
				'SQLParams' => array(':pk' => &$this->_RowID),
				'MsgTModule' => 'Admin',
				'MsgTCat' => 'tr_Common',
				'on' => 'insert, update'),
			array('txtTitle', 'IsUnique',
				'SQL' => 'SELECT COUNT(*) FROM `_user_types` WHERE `Title`=:val AND `ID`!=:pk LIMIT 1',
				'SQLParams' => array(':pk' => &$this->_RowID),
				'MsgTModule' => 'Admin',
				'MsgTCat' => 'tr_Common',
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
			'txtLogicName' => \Lng::Admin('tr_Common', 'Logic name'),
			'txtTitle' => \Lng::Admin('tr_Common', 'Title'),
			'chkIsActive' => \Lng::Admin('tr_Common', 'Is active'),
			'chkIsDefault' => \Lng::Admin('tr_Common', 'Is default'),
		);
	}

	/**
	 * @param \Base\DataGridParams $DGP
	 * @return array DataTable
	 */
	function Select(\Base\DataGridParams $DGP) {
		$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_user_types`');
		$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
		return T\DB::GetTable("
			SELECT t.*, IF(u.UserTypeID<=>NULL, 0, 1) AS IsUsed
			FROM `_user_types` AS t
			INNER JOIN (SELECT 1) AS tmp ON {$DGP->SQLWhereClause}
			LEFT JOIN (SELECT `UserTypeID` FROM `_users` GROUP BY `UserTypeID`) AS u
				ON t.ID=u.UserTypeID
			ORDER BY {$DGP->Sort}
			LIMIT " . $Limit);
	}

	private static $_ActiveUserTypes = array();

	/**
	 * @param string $Fields
	 * @return \CDbDataReader
	 */
	static function GetActiveUserTypes($Fields = '*') {
		if (!isset(self::$_ActiveUserTypes[$Fields]))
			self::$_ActiveUserTypes[$Fields] = T\DB::GetTable("SELECT $Fields FROM `_user_types` WHERE `IsActive`");
		return self::$_ActiveUserTypes[$Fields];
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
		$this->_RowID = $DGP->RowID;
		if ($DGP->RowID && $this->validate()) {
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
			$IDs = explode(',', $DGP->RowID);
			$trans = \Yii::app()->db->beginTransaction();
			try {
				foreach ($IDs as $ID)
					T\DB::Execute("
						DELETE FROM `_user_types`
						WHERE `ID`=:id AND ID NOT IN (SELECT DISTINCT `UserTypeID` FROM `_users`)", array(':id' => $ID));
				$trans->commit();
			} catch (Exception $e) {
				$trans->rollback();
				\Err::ErrMsg(\Lng::Admin('tr_Common', 'Deletion failed'));
			}
		}
	}

	function UniqueDefaultValidate($attr, $params) {
		$ID = &$this->_RowID;
		if ($this->$attr && T\DB::GetField('SELECT COUNT(*) FROM `_user_types` WHERE `IsDefault` AND ID!=:id', array(':id' => $ID)))
			$this->addError('chkIsDefault', \Lng::Admin('tr_UserModule', 'Only one default user type'));
	}

}
