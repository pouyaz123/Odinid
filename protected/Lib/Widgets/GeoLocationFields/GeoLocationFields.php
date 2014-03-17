<?php

namespace Widgets\GeoLocationFields;

use \Tools as T;

/**
 * Description of GeoLocationFields
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property \Base\FormModel $Model set only
 * @property array|null $ModelPostValue get only
 * @property string $AjaxKW get only
 * @property string $DivisionDropDown_ContainerID get only
 * @property string $CityDropDown_ContainerID get only
 * @property array $ddlarrCountries get only
 * @property array $ddlarrDivisions get only
 * @property array $ddlarrCities get only
 * @property array|boolean $EmptyDDLOption get|set //set to false(default) to have no Empty option
 */
class GeoLocationFields extends \Base\Widget {

	private static $Counter = 0;
	public $ddlCountryAttr;
	public $ddlDivisionAttr;
	public $ddlCityAttr;
	public $txtCountryAttr;
	public $txtDivisionAttr;
	public $txtCityAttr;
	public $txtAddress1Attr;
	public $txtAddress2Attr;
	#
	/** @var \CActiveForm the form widget object to patch the fields to it */
	public $ActiveForm = null;

	/** @var \Base\FormModel */
	private $_Model;

	function setModel($model) {
		$this->_Model = $model;
	}

	private $_AjaxKW = 'GeoLocFields';

	function getAjaxKW() {
		return $this->_AjaxKW . '_' . $this->id;
	}

	function getDivisionDropDown_ContainerID() {
		return $this->ddlDivisionAttr ? "cnt{$this->id}_$this->ddlDivisionAttr" : null;
	}

	function getCityDropDown_ContainerID() {
		return $this->ddlCityAttr ? "cnt{$this->id}_$this->ddlCityAttr" : null;
	}

//-------- Empty and prompt options --------//
	private $_EmptyDDLOption = null;

	public function getEmptyDDLOption() {
		return $this->_EmptyDDLOption;
	}

	public function setEmptyDDLOption($val) {
		if (!$val && !is_string($val) && $val !== false)
			$val = false;
		if (!is_array($val))
			$val = array('' => $val);
		$this->_EmptyDDLOption = $val;
	}

	public $PromptDDLOption = null;

//-------- DATA --------//
	function getModelPostValue() {
		static $Post = NULL;
		if (!$Post)
			$Post = \GPCS::POST(\CHtml::modelName($this->_Model));
		return $Post;
	}

	function getddlarrCountries() {
		static $ddlarr = array();
		if (!count($ddlarr)) {
			$QryRsrc = T\DB::Query("SELECT `ISO2`, `AsciiName` FROM `_geo_countries` ORDER BY `AsciiName`");
//			$QryRsrc = T\DB::Query("SELECT `AsciiName` FROM `_geo_countries` ORDER BY `AsciiName`");
			foreach ($QryRsrc as $dr) {
				$ddlarr[$dr['ISO2']] = $dr['AsciiName'];
//				$ddlarr[$dr['AsciiName']] = $dr['AsciiName'];
			}
			unset($QryRsrc);
		}
		return $ddlarr;
	}

	function getddlarrDivisions() {
		$Post = $this->ModelPostValue;
		if (!$Post)
			return array();
		$Country = isset($Post[$this->ddlCountryAttr]) || isset($Post[$this->txtCountryAttr]) ?
				(isset($Post[$this->ddlCountryAttr]) ? $Post[$this->ddlCountryAttr] : $Post[$this->txtCountryAttr])  :
				null;
		static $ddlarr = array();
		if (!count($ddlarr) && $Country) {
			$QryRsrc = T\DB::Query(
							"SELECT * FROM ("
							. " SELECT gd.`CombinedCode` AS ID, gd.`AsciiName` AS Name, 1 AS Official"
//							. " SELECT gd.`AsciiName` AS Name, 1 AS Official"
							. " FROM `_geo_countries` AS gc"
							. " INNER JOIN (SELECT 1) AS tmp ON gc.`AsciiName`=:country OR gc.`ISO2`=:country"
							. " INNER JOIN `_geo_divisions` AS gd ON gd.`CountryISO2`=gc.`ISO2`"
							. " UNION ALL"
							. " SELECT gud.`ID`, gud.`Division` AS Name, 0 AS Official"
//							. " SELECT gud.`Division` AS Name, 0 AS Official"
							. " FROM `_geo_user_divisions` AS gud"
							. " INNER JOIN (SELECT 1) AS tmp ON gud.`CountryISO2`=:country OR NOT ISNULL(gud.`UserCountryID`)"
							. " INNER JOIN `_geo_user_countries` AS guc ON guc.`Country`=:country AND guc.`ID`=gud.`UserCountryID`"
							. " ) AS tbl"
							. " ORDER BY tbl.Official, tbl.`Name`"
							, array(':country' => $Country));
			if ($QryRsrc)
				foreach ($QryRsrc as $dr) {
					$ddlarr[$dr['ID']] = $dr['Name'];
//					$ddlarr[$dr['Name']] = $dr['Name'];
				}
			unset($QryRsrc);
		}
		return $ddlarr;
	}

	function getddlarrCities() {
		$Post = $this->ModelPostValue;
		if (!$Post)
			return array();
		$Division = isset($Post[$this->ddlDivisionAttr]) || isseT($Post[$this->txtDivisionAttr]) ?
				(isset($Post[$this->ddlDivisionAttr]) ? $Post[$this->ddlDivisionAttr] : $Post[$this->txtDivisionAttr])  :
				null;
		static $ddlarr = array();
		if (!count($ddlarr) && $Division) {
			$QryRsrc = T\DB::Query(
							"SELECT * FROM ("
							. " SELECT gc.`GeonameID` AS ID, gc.`AsciiName` AS Name, 1 AS Official, IF(gc.`DivisionCode`='' OR ISNULL(gc.`DivisionCode`), 0, 1) AS HasDivisionCode"
//							. " SELECT gc.`AsciiName` AS Name, 1 AS Official, IF(gc.`DivisionCode`='' OR ISNULL(gc.`DivisionCode`), 0, 1) AS HasDivisionCode"
							. " FROM `_geo_divisions` AS gd"
							. " INNER JOIN (SELECT 1) AS tmp ON gd.`CombinedCode`=:division OR gd.`AsciiName`=:division"
							. " INNER JOIN `_geo_cities` AS gc ON gc.`CountryISO2`=gd.`CountryISO2` AND (gc.`DivisionCode`=gd.`DivisionCode` OR ISNULL(gc.`DivisionCode`) OR gc.`DivisionCode`='')"
							. " UNION ALL"
							. " SELECT guc.`ID`, guc.`City` AS Name, 0 AS Official, 0 AS HasDivisionCode"
//							. " SELECT guc.`City` AS Name, 0 AS Official, 0 AS HasDivisionCode"
							. " FROM `_geo_user_cities` AS guc"
							. " INNER JOIN (SELECT 1) AS tmp ON guc.`DivisionCombinedCode`=:division OR NOT ISNULL(guc.`UserDivisionID`)"
							. " INNER JOIN `_geo_user_divisions` AS gud ON gud.`Division`=:division AND gud.ID=guc.`UserDivisionID`"
							. " ) AS tbl"
							. " ORDER BY tbl.Official, HasDivisionCode DESC, tbl.`Name`"
							, array(':division' => $Division));
			if ($QryRsrc)
				foreach ($QryRsrc as $dr) {
					$ddlarr[$dr['ID']] = $dr['Name'];
//					$ddlarr[$dr['Name']] = $dr['Name'];
				}
			unset($QryRsrc);
		}
		return $ddlarr;
	}

//-------- Tasks --------//
	public function init() {
		self::$Counter++;
		if (!$this->id)
			$this->id = 'GeoLocationFields' . self::$Counter;

		$Model = &$this->_Model;
		if (!$Model || !is_object($Model) || !is_a($Model, '\CModel'))
			throw new \Err(__METHOD__, 'No valid Model has been passed to ' . __CLASS__ . ' widget');
		if (!$this->ddlCountryAttr)
			throw new \Err(__METHOD__, 'At least the Country attr must be passed into ' . __CLASS__ . ' widget');

		$_this = &$this;
		\Output::AddIn_AjaxOutput(function()use($Model, $_this) {
			/* @var $_this GeoLocationFields */
			$Post = $_this->ModelPostValue;
			if (!$Post)
				return;
			if (isset($Post[$_this->ddlCountryAttr]) || isset($Post[$_this->txtCountryAttr])) {
				echo GeoLocationFields::GenerateDivisionDDL($Model, $_this, $_this->ddlarrDivisions)
				. GeoLocationFields::GenerateCityDDL($Model, $this, array());
			} elseif (isset($Post[$_this->ddlDivisionAttr]) || isseT($Post[$_this->txtDivisionAttr])) {
				echo GeoLocationFields::GenerateCityDDL($Model, $_this, $_this->ddlarrCities);
			}
		}, $this->AjaxKW);
	}

	public function run() {
		if (!\Output::IsThisAsyncPostBack($this->AjaxKW))
			echo $this->render('GeoLocationFields', array(
				'Model' => $this->_Model,
					)
			);
	}

	public static function GenerateDivisionDDL($Model, $_this, $arrData) {
		if (!$_this->ddlDivisionAttr)
			return '';
		/* @var $_this GeoLocationFields */
		return \html::FieldContainer(
						\html::activeComboBox($Model, $_this->ActiveForm, $_this->ddlDivisionAttr, $arrData
								, array(
							'rel' => $_this->CityDropDown_ContainerID ?
									\html::AjaxElement("#$_this->CityDropDown_ContainerID", $_this->AjaxKW) : '',
							'prompt' => ($_this->PromptDDLOption ? : NULL),
							'empty' => ($_this->EmptyDDLOption ? : NULL),
								)
								, null
								, null
								//ddlComno with user input ability
								, $_this->txtDivisionAttr ? array(
									'attribute' => $_this->txtDivisionAttr,
									'htmlOptions' => array(
										'rel' => $_this->CityDropDown_ContainerID ?
												\html::AjaxElement("#$_this->CityDropDown_ContainerID", $_this->AjaxKW) : '',
									)) : null
						)
						, \html::activeLabelEx($Model, $_this->ActiveForm, $_this->ddlDivisionAttr)
						, $_this->txtDivisionAttr ? \html::error($Model, $_this->ActiveForm, $_this->txtDivisionAttr) : null
						, 'style="width:300px"'
		);
	}

	public static function GenerateCityDDL($Model, $_this, $arrData) {
		if (!$_this->ddlDivisionAttr || !$_this->ddlCityAttr)
			return '';
		/* @var $_this GeoLocationFields */
		return \html::FieldContainer(
						\html::activeComboBox($Model, $_this->ActiveForm, $_this->ddlCityAttr, $arrData
								, array(
							'prompt' => ($_this->PromptDDLOption ? : NULL),
							'empty' => ($_this->EmptyDDLOption ? : NULL),
								)
								, null
								, null
								//ddlCombo with user input ability
								, $_this->txtCityAttr ? array(
									'attribute' => $_this->txtCityAttr,
										) : null
						)
						, \html::activeLabelEx($Model, $_this->ActiveForm, $_this->ddlCityAttr)
						, $_this->txtCityAttr ? \html::error($Model, $_this->ActiveForm, $_this->txtCityAttr) : null
						, "id=\"$_this->CityDropDown_ContainerID\""
		);
	}

}
