<?php

namespace Admin\models\User;

use \Consts as C;
use \Tools as T;

class Invitation extends \Base\FormModel {

	public function getPostName() {
		return $this->scenario;
	}

	const DescriptionMaxLen = 150;

	private $_RowID = -1;
	public $txtCode;
	public $ddlUserTypeID;
	public $txtUserTypeExpDate = NULL;
	public $txtInvitationExpDate = NULL;
	public $txtDescription = NULL;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		$rules = array(
			array('txtCode, ddlUserTypeID', 'required',
				'on' => 'insert, update'),
			#
			array_merge(array('txtCode', 'length', 'on' => 'insert, update')
					, \ValidationLimits\User::GetInstance()->InvitationCode),
			array('txtCode', 'IsUnique',
				'SQL' => 'SELECT COUNT(*) FROM `_user_invitations` WHERE `Code`=:val AND `ID`!=:pk LIMIT 1',
				'SQLParams' => array(':pk' => $this->_RowID),
				'on' => 'insert, update'),
			array('txtUserTypeExpDate', 'date',
				'format' => C\Regexp::Yii_DateFormat_FullDigit,
				'on' => 'insert, update'),
			array('txtInvitationExpDate', 'date',
				'format' => C\Regexp::Yii_DateFormat_FullDigit,
				'on' => 'insert, update'),
			array('txtDescription', 'length',
				'max' => self::DescriptionMaxLen,
				'on' => 'insert, update'),
		);
		$ActiveUserTypes = Type::GetActiveUserTypes();
		$rules[] = array('ddlUserTypeID', 'in',
			'range' => $ActiveUserTypes ?
					T\DB::GetColumnValues($ActiveUserTypes, 'ID') :
					array(),
			'on' => 'insert, update');

		return $rules;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'txtCode' => \t2::admin_admin('Code'),
			'ddlUserTypeID' => \t2::admin_admin('User type'),
			'txtUserTypeExpDate' => \t2::admin_admin('User Type Expiration'),
			'txtInvitationExpDate' => \t2::admin_admin('Invitation Expiration'),
			'txtDescription' => \t2::admin_admin('Description'),
		);
	}

	/**
	 * @param \Base\DataGridParams $DGP
	 * @return array DataTable
	 */
	function Select(\Base\DataGridParams $DGP) {
		$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_user_invitations`');
		$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
		return T\DB::GetTable(
						"SELECT *"
						. " FROM `_user_invitations`"
						. " WHERE {$DGP->SQLWhereClause}"
						. " ORDER BY {$DGP->Sort}"
						. " LIMIT " . $Limit);
	}

	function Insert(\Base\DataGridParams $DGP) {
		if ($this->validate()) {
			T\DB::Execute(
					"INSERT INTO `_user_invitations`(`ID`, `Code`, `UserTypeID`, `UserTypeExpDate`, `InvitationExpDate`, `Description`)"
					. " VALUES ((" . T\DB::GetNewID('_user_invitations') . "), :code, :typeid, :utexp, :invexp, :description)"
					, array(
				':code' => $this->txtCode,
				':typeid' => $this->ddlUserTypeID,
				':utexp' => $this->txtUserTypeExpDate? : null,
				':invexp' => $this->txtInvitationExpDate? : null,
				':description' => $this->txtDescription? : null,
			));
		} else {
			\html::ErrMsg_Exit(\CHtml::errorSummary($this));
		}
	}

	function Update(\Base\DataGridParams $DGP) {
		$this->_RowID = $DGP->RowID;
		if ($DGP->RowID && $this->validate()) {
			T\DB::Execute(
					"UPDATE `_user_invitations`"
					. " SET `Code`=:code, `UserTypeID`=:typeid, `UserTypeExpDate`=:utexp, `InvitationExpDate`=:invexp, `Description`=:description"
					. " WHERE `ID`=:id"
					, array(
				':id' => $DGP->RowID,
				':code' => $this->txtCode,
				':typeid' => $this->ddlUserTypeID,
				':utexp' => $this->txtUserTypeExpDate? : null,
				':invexp' => $this->txtInvitationExpDate? : null,
				':description' => $this->txtDescription? : null,
			));
		} else {
			\html::ErrMsg_Exit(\CHtml::errorSummary($this));
		}
	}

	function Delete(\Base\DataGridParams $DGP) {
		if ($DGP->RowID) {
			$IDs = explode(',', $DGP->RowID);
			$Queries = array();
			foreach ($IDs as $ID)
				$Queries[] = array("DELETE FROM `_user_invitations` WHERE `ID`=:id", array(':id' => $ID));
			T\DB::Transaction($Queries, NULL, function() {
				\html::ErrMsg_Exit(\t2::admin_admin('Deletion failed'));
			});
		}
	}

}
