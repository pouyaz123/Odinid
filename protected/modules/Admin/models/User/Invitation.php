<?php

namespace Admin\models\User;

use \Consts as C;
use \Tools as T;

class Invitation extends \Base\FormModel {

	public function PostName() {
		return $this->scenario;
	}

	const CodeMaxLen = 50;
	const DescriptionMaxLen = 150;

	private $_RowID = -1;
	public $txtCode;
	public $ddlUserTypeID;
	public $txtDescription = NULL;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		$rules = array(
			array('txtCode, ddlUserTypeID', 'required',
				'on' => 'insert, update'),
			#
			array('txtCode', 'length',
				'max' => self::CodeMaxLen,
				'on' => 'insert, update'),
			array('txtCode', 'Unique',
				'SQL' => 'SELECT COUNT(*) FROM `_user_invitations` WHERE `Code`=:val AND `ID`!=:pk LIMIT 1',
				'SQLParams' => array(':pk' => &$this->_RowID),
				'MsgTModule' => 'Admin',
				'MsgTCat' => 'tr_Common',
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
			'txtCode' => \Lng::Admin('tr_Common', 'Code'),
			'ddlUserTypeID' => \Lng::Admin('tr_Common', 'User type'),
			'txtDescription' => \Lng::Admin('tr_Common', 'Description'),
		);
	}

	/**
	 * @param \Base\DataGridParams $DGP
	 * @return array DataTable
	 */
	function Select(\Base\DataGridParams $DGP) {
		$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_user_invitations`');
		$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
		return T\DB::GetTable("
			SELECT *
			FROM `_user_invitations`
			WHERE {$DGP->SQLWhereClause}
			ORDER BY {$DGP->Sort}
			LIMIT " . $Limit);
	}

	function Insert(\Base\DataGridParams $DGP) {
		if ($this->validate()) {
			$ID = T\DB::GetNewID('_user_invitations');
			T\DB::Execute("
				INSERT INTO `_user_invitations`(`ID`, `Code`, `UserTypeID`, `Description`)
				VALUES (:id, :code, :typeid, :description)"
					, array(
				':id' => $ID,
				':code' => $this->txtCode,
				':typeid' => $this->ddlUserTypeID,
				':description' => $this->txtDescription,
			));
		} else {
			\html::AjaxMsg_Exit(\CHtml::errorSummary($this));
		}
	}

	function Update(\Base\DataGridParams $DGP) {
		$this->_RowID = $DGP->RowID;
		if ($DGP->RowID && $this->validate()) {
			T\DB::Execute("
				UPDATE `_user_invitations`
				SET `Code`=:code, `UserTypeID`=:typeid, `Description`=:description
				WHERE `ID`=:id"
					, array(
				':id' => $DGP->RowID,
				':code' => $this->txtCode,
				':typeid' => $this->ddlUserTypeID,
				':description' => $this->txtDescription,
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
					T\DB::Execute("DELETE FROM `_user_invitations` WHERE `ID`=:id", array(':id' => $ID));
				$trans->commit();
			} catch (Exception $e) {
				$trans->rollback();
				\Err::ErrMsg(\Lng::Admin('tr_Common', 'Deletion failed'));
			}
		}
	}

}
