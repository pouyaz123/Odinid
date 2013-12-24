<?php
//mytodo 2: admin PlanForm is incomplete
//namespace Admin\models\User;
//
//use \Consts as C;
//use \Tools as T;
//
//class PlanForm extends \Base\FormModel {
//
//	public function getPostName() {
//		return $this->scenario;
//	}
//
//	const PlanCostMax = 16000000;
//	const PerHowMonthMax = 65000;
//	const TitleMaxLen = 50;
//
//	public $txtTitle;
//	public $txtPlanCost;
//	public $txtPerHowMonth;
//	public $chkIsActive;
//
//	/**
//	 * @return array validation rules for model attributes.
//	 */
//	public function rules() {
//		return array(
//			array('txtTitle', 'required',
//				'except' => 'delete'),
//			#
//			array('txtTitle', 'length',
//				'max' => self::TitleMaxLen,
//				'except' => 'delete'),
//			#
//			array('txtPlanCost', 'numerical',
//				'max' => self::PlanCostMax,
//				'except' => 'delete'),
//			array('txtPerHowMonth', 'numerical',
//				'max' => self::PerHowMonthMax,
//				'except' => 'delete'),
//			#
//			array('chkIsActive, chkIsDefault', 'boolean',
//				'except' => 'delete'),
//		);
//	}
//
//	/**
//	 * @return array customized attribute labels (name=>label)
//	 */
//	public function attributeLabels() {
//		return array(
//			'txtTitle' => \Lng::Admin('tr_common', 'Title'),
//			'txtPlanCost' => \Lng::Admin('tr_user', 'Plan cost'),
//			'txtPerHowMonth' => \Lng::Admin('tr_user', 'Per how month'),
//			'chkIsActive' => \Lng::Admin('tr_common', 'Is active'),
//		);
//	}
//
//	function Select(\Base\DataGridParams $DGP) {
//		$AllCount = T\DB::GetField('SELECT COUNT(*) FROM `_user_types`');
//		$Limit = $DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);
//		return T\DB::GetTable('
//			SELECT t.*
//			FROM `_user_types` AS t
//			ORDER BY ' . $DGP->Sort . '
//			LIMIT ' . $Limit);
//	}
//
//	function Insert() {
//		if ($this->validate()) {
//			
//		}
//	}
//
//}
