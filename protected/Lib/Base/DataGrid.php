<?php

namespace Base;

use \Tools as T;
use \Consts as C;
use \Components as Com;

/**
 * DataGrid - Converted from F3 to Yii
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 2
 * @copyright (c) Abbas Ali Hashemian
 * @access public
 */
class DataGrid extends Container {

	private $_Columns = NULL
			, $_Queries = array()
			, $_PrivateCallbacks = array();
	private $_AjaxKW = ''
			, $_PagerID = '';

	/**
	 * @var DataGridConfig $Options
	 */
	public $Options = NULL;
	public $Configs = NULL; //&$this->Options
	public $Methods = array();
	public $HasFilterBar = false;
	public $HasSearch = false;
	public $HasMultiSearch = false;
	public static $arrGrpahicalButtonsResource = array(
		'Edit' => '<img src="/_img/icons/edit_inline16.png"/>'
		, 'Delete' => '<img src="/_img/icons/bin16.png"/>'
		, 'Cancel' => '<img src="/_img/icons/cancel16.png"/>'
		, 'Save' => '<img src="/_img/icons/save16.png"/>'
	);
	private $_Resources = array(
		'Action' => 'Actions'
		#
		, 'Edit' => 'Inline edit'
		, 'EditTitle' => 'Inline edit'
		#
		, 'Delete' => 'Delete'
		, 'DeleteTitle' => 'Delete'
		#
		, 'Save' => 'Save'
		, 'SaveTitle' => 'Save'
		#
		, 'Cancel' => 'Cancel'
		, 'CancelTitle' => 'Cancel'
		#
		, 'ActionColIndexName' => 'jQGrid_ACTIONCOL'
		, 'SaveAll' => 'Save all editing items'
	);
	private $_TableClasses = NULL;

	/**
	 * @param str $strClasses
	 * @return \Base\DataGrid
	 */
	public function setTableClasses($strClasses) {
		$this->_TableClasses = $strClasses;
		return $this;
	}

	public function getTableClasses() {
		return $this->_TableClasses;
	}

	private $_TranslationModule = NULL;
	private $_TranslationCat = NULL;

	public function getTranslationModule() {
		return $this->_TranslationModule;
	}

	public function getTranslationCat() {
		return $this->_TranslationCat;
	}

	/**
	 * @return \Base\DataGrid 
	 */
	public function SetTranslation($strTranslationModule, $strTranslationCat) {
		$this->_TranslationModule = $strTranslationModule;
		$this->_TranslationCat = $strTranslationCat;
		\t2::tarray($this->_Resources, $strTranslationModule, $strTranslationCat);
		return $this;
	}

	/**
	 * @return \Base\DataGrid 
	 */
	public function Resources($arrToMerge, $strTranslationModule = NULL, $strTranslationCat = NULL) {
		$this->_Resources = array_merge($this->_Resources, $arrToMerge);
		if ($strTranslationModule && $strTranslationCat)
			$this->SetTranslation($strTranslationModule, $strTranslationCat);
		return $this;
	}

	public function PagerID() {
		return $this->_PagerID;
	}

	public static function LoadFiles($Theme = \Conf::jQTheme) {
		\html::DataGrid_Load($Theme);
	}

	/**
	 * creates and returns the string of dropdown list(select) elements to use in the grid column
	 * ->type('select')
	 * ->searchoptions(array('value' => $ddl))
	 * ->editoptions(array('value' => $ddl));
	 * @param array $arrDataTable
	 * @param str|null $ValueField	if set to null the index will be used as value
	 * @param str|null $LabelField	if set to null the value will be used as label
	 * @param str|false|null $FirstEmptyOption	if set to a value rather than false or set as null we will have an empty option at first
	 * @return str elements string
	 */
	public static function CreateDDLElements($arrDataTable, $ValueField = NULL, $LabelField = NULL, $FirstEmptyOption = false) {
		$strDDL = array();
		if ($FirstEmptyOption !== false)
			$strDDL[] = ':' . ($FirstEmptyOption ? $FirstEmptyOption : '---');
		if ($arrDataTable)
			foreach ($arrDataTable as $idx => $dr) {
				$val = (isset($ValueField) ? $dr[$ValueField] : $idx);
				$label = (isset($LabelField) ? $dr[$LabelField] : $val);
				$strDDL[] = "{$val}:{$label}";
			}
		return implode(';', $strDDL);
	}

	/**
	 * @param str $ID
	 * @param fnc $fncQuery //function($SortColumn, $SortOrder, $LimitStartIdx, $LimitLength){return str $Query}
	 */
	function __construct($ID, $Theme = \Conf::jQTheme) {
		Container::__construct($ID);
		$this->_PagerID = "{$this->ID}_Pager";
		$this->_AjaxKW = "DataGridAjaxKW_{$this->ID}";

		//jqui
//core
//Widget
//Mouse
//Position
//Resizable
//Button
//Dialog
//Datepicker
		self::LoadFiles($Theme);

		//options - Configs
		$this->Configs = &$this->Options;

		$this->Options = \html::DataGridConfig(array(
					#SERVER SIDE OPTIONS
					'DataKey' => NULL
					#HTTP
					, 'url' => T\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], \Output::AjaxKeyword_PostParamName . '=' . $this->_AjaxKW)
					, 'editurl' => T\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], \Output::AjaxKeyword_PostParamName . '=' . $this->_AjaxKW)
					, 'cellurl' => T\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], \Output::AjaxKeyword_PostParamName . '=' . $this->_AjaxKW)
					, 'mtype' => 'POST'
//					, 'postData' => array(\Output::AjaxKeyword_PostParamName => $this->AjaxKW)
//					, 'ajaxGridOptions' => array('data' => &$this->AjaxPostParams)
					#LIST
					, 'datatype' => 'json'
//			, 'jsonReader' => array(
//				'repeatitems' => false
//				, 'id' => '0'
//			)
					, 'searchoptions' => array(
//				'in','ni',
						'sopt' => array('cn', 'eq', 'bw', 'ew', 'nc', 'ne', 'bn', 'en', 'lt', 'le', 'gt', 'ge')
					)
					#EDIT
					, 'editoptions' => array()
					#HTTP Parameters
					, 'prmNames' => array(
						#list+search
						'page' => "Grd_PageNo"
						, 'rows' => "Grd_HowManyRows"
						, 'sort' => "Grd_SortColumn"
						, 'order' => "Grd_SortOrder"
						, 'search' => "Grd_Search"
						, 'filters' => 'filters'
						#operations
						, 'oper' => "Grd_Operation"
						, 'addoper' => "Add"
						, 'editoper' => "Edit"
						, 'deloper' => "Del"
						, 'id' => "Grd_RowID"
						#other
						, 'subgridid' => "Grd_SubGrdID"
						, 'npage' => null
						, 'totalrows' => "Grd_TotalRows"
						, 'nd' => "Grd_nd" //the time passed to the request (for IE browsers not to cache the request) (default value nd)
					)
					#COLUMNS
					, 'colNames' => array()
					, 'cmTemplate' => array('align' => 'auto', 'title' => false, 'sortable' => TRUE)
					, 'colModel' => &$this->_Columns
					, 'sortname' => null
					, 'sortorder' => 'ASC'
					#PAGE + NAV
					, 'pager' => "#{$this->_PagerID}"
					, 'rowNum' => 10
					, 'viewrecords' => true //total num view
					#OTHER
					, 'caption' => 'Data Grid'
					, 'gridview' => true //speed reason
					, 'sortable' => true
//			, 'export' => array(
//				'paper' => 'a4'
//				, 'orientation' => 'landscape'
//			)
					, 'multiselect' => false
					#LAYER
					, 'width' => 640
					, 'height' => 'auto'
					, 'scrollrows' => true
					, 'scroll' => 0
					, 'jqGridInlineEditRow' => '##JSFUN##function(){alert(0)}##JSFUN##'
		));
	}

	#----------------- COLUMNS -----------------#

	/**
	 * @param DataGridColumn $multi_jQGridColumn can be either arrays or DataGridColumn objects<br/>
	 * array(<br/>
	 * 	'title'=>'title'<br/>
	 * 	, 'name'=>'colName'<br/>
	 * 	, 'index'=>'colName'<br/>
	 * 	, 'width'=>55<br/>
	 * 	, 'editable':true<br/>
	 * 	, 'type':'date' //to have a calendar date picker with filter and edit text boxes<br/>
	 * 	, 'editoptions':{'size':20}<br/>
	 * )<br/>
	 * to Reset a specific property of a column we have not to reenter all other properties
	 * @return \Base\DataGrid 
	 */
	function SetColumns($multi_jQGridColumn) {
		T\Basics::MultiArgs($multi_jQGridColumn, func_get_args());
		foreach ($multi_jQGridColumn as $NewColumn) {
			/* @var $NewColumn DataGridColumn */
			if (!is_object($NewColumn) || !is_a($NewColumn, '\Base\DataGridColumn') || !$NewColumn->index()) {
				throw new \Err(__METHOD__, 'Invalid column passed! A column shoud be an instance of "\Base\DataGridColumn" and "index" specified at least'
						, array('func_get_args' => func_get_args(), '$this' => $this));
			}

			if (!$this->Options->sortname())
				$this->Options->sortname($NewColumn->index());
			$idx = $NewColumn->index();
			$name = $NewColumn->name();
			if (!$name && $idx) {
				$idx = explode('.', $idx);
				$NewColumn->name(array_pop($idx));
			} else if ($name && !$idx)
				$NewColumn->index($name);
			//checkbox improvement : 0/1 instead of yes/no
			$eOpt = $NewColumn->editoptions();
			if ($NewColumn->type() == 'checkbox' && !isset($eOpt['value']))
				$NewColumn->editoptions(array('value' => '1:0'));
			$this->Options->colModel(array($NewColumn->index() => $NewColumn->_getArray()));
		}
		return $this;
	}

	/**
	 * @return \Base\DataGrid 
	 */
	function UnsetColumn($IndexName) {
		if (isset($this->_Columns[$IndexName]))
			unset($this->_Columns[$IndexName]);
		return $this;
	}

	/**
	 * @return \Base\DataGridColumn
	 */
	function GetColumn($IndexName) {
		if (isset($this->_Columns[$IndexName]))
			return new DataGridColumn($this->_Columns[$IndexName]);
		return null;
	}

	#----------------- methods -----------------#

	/**
	 * @return \Base\DataGrid 
	 */
	function __call($MName, $Args) {
		$this->Methods[$MName] = $Args;
		return $this;
	}

	/**
	 * @return \Base\DataGrid 
	 */
	function UnsetMethod($MName) {
		if (isset($this->Methods[$MName]))
			unset($this->Methods[$MName]);
		return $this;
	}

	#----------------- options(configs) -----------------#

	/**
	 * @return \Base\DataGrid
	 */
	function Options(\Base\DataGridConfig $Configs) {
		if (is_null($Configs))
			return $this;
		$this->Options->MergeWith($Configs->_getArray());
		return $this;
	}

	/**
	 * @return \Base\DataGridConfig
	 */
	function GetOptions() {
		return $this->Options;
	}

	/**
	 * @return \Base\DataGrid|\Base\DataGridConfig
	 */
	function Config(\Base\DataGridConfig $Configs) {
		return $this->Options($Configs);
	}

	/**
	 * @param str $strKeyColNames //col1, col2,col3
	 * @return \Base\DataGrid 
	 */
	function DataKey($strKeyColNames) {
		$this->Options->DataKey($strKeyColNames);
		return $this;
	}

	function GetActionColButtons($RowIndex, $strBtnClassName = null, $JustEditButton = false, $JustDeleteButton = false, $EditModeExtraCodes = '') {
		$Resources = &$this->_Resources;
		return "
<span id=\"edit_row_$RowIndex\">
	" . (!$JustDeleteButton ? "<a id='DGEDITROW_{$this->ID}_$RowIndex' class=\"$strBtnClassName\" title=\"{$Resources['EditTitle']}\" href=\"javascript:void(0);\" onclick=\"DGEDITROW('{$this->ID}', '$RowIndex', this)\" >{$Resources['Edit']}</a>" : "") . "
	" . (!$JustEditButton ? "<a class=\"$strBtnClassName\" title=\"{$Resources['DeleteTitle']}\" href=\"javascript:void(0);\" onclick=\"DGDELETEROW('{$this->ID}', '$RowIndex')\">{$Resources['Delete']}</a>" : "") . "
</span>
<span style=\"display:none\" id=\"save_row_$RowIndex\">
	<a class=\"$strBtnClassName\" title=\"{$Resources['SaveTitle']}\" href=\"javascript:void(0);\" onclick=\"DGSAVEROW('{$this->ID}', '$RowIndex', this)\" rel=\"GridInlistSaveBtn\">{$Resources['Save']}</a>
	<a class=\"$strBtnClassName\" title=\"{$Resources['CancelTitle']}\" href=\"javascript:void(0);\" onclick=\"DGCANCELEDIT('{$this->ID}', '$RowIndex', this)\" rel=\"GridInlistCancelBtn\">{$Resources['Cancel']}</a>
		$EditModeExtraCodes
</span>
";
	}

	/**
	 * Action Column : Inlist edit/delete
	 * @return \Base\DataGrid 
	 */
	function SetActionColumn($arrActionColOptions = array(), $strBtnClassName = null) {
		if (!$arrActionColOptions || !is_array($arrActionColOptions))
			$arrActionColOptions = array();
		$Resources = &$this->_Resources;
		$this->SetColumns(
				\html::DataGridColumn()
						->index($Resources['ActionColIndexName'])
						->header($Resources['Action'])
						->align('center')
						->sortable(false)
						->editable(false)
						->search(false)
						->title(false)
						->MergeWith($arrActionColOptions)
		);

		$this->Options->gridComplete("##JSFUN##
function(){
	var ids = $('#{$this->ID}').jqGrid('getDataIDs');
	for(var i=0;i < ids.length;i++){
		var cl = ids[i]
			,be = ' <a class=\"$strBtnClassName\" title=\"{$Resources['EditTitle']}\" href=\"javascript:void(0);\" onclick=\"DGEDITROW(\\'{$this->ID}\\', \\''+cl+'\\', this)\" >{$Resources['Edit']}</a>'
			,de = ' <a class=\"$strBtnClassName\" title=\"{$Resources['DeleteTitle']}\" href=\"javascript:void(0);\" onclick=\"DGDELETEROW(\\'{$this->ID}\\', \\''+cl+'\\')\">{$Resources['Delete']}</a>'
			,se = ' <a class=\"$strBtnClassName\" title=\"{$Resources['SaveTitle']}\" href=\"javascript:void(0);\" onclick=\"DGSAVEROW(\\'{$this->ID}\\', \\''+cl+'\\', this)\" rel=\"GridInlistSaveBtn\">{$Resources['Save']}</a>'
			,ce = ' <a class=\"$strBtnClassName\" title=\"{$Resources['CancelTitle']}\" href=\"javascript:void(0);\" onclick=\"DGCANCELEDIT(\\'{$this->ID}\\', \\''+cl+'\\', this)\" rel=\"GridInlistCancelBtn\">{$Resources['Cancel']}</a>';
		$('#{$this->ID}').jqGrid(
			'setRowData'
			, cl
			, {{$Resources['ActionColIndexName']}:'<span id=\"edit_row_'+cl+'\">'+be+de+'</span>'+'<span style=\"display:none\" id=\"save_row_'+cl+'\">'+se+ce+'</span>'}
		);
	}		
}##JSFUN##"
//saveparameters = {
//	"successfunc" : null,
//	"url" : null,
//	"extraparam" : {},
//	"aftersavefunc" : null,
//	"errorfunc": null,
//	"afterrestorefunc" : null,
//	"restoreAfterError" : true,
//	"mtype" : "POST"
//}
//
//jQuery("#grid_id").saveRow(rowid,  saveparameters);
//jQuery("#grid_id").jqGrid('saveRow',rowid,  saveparameters);
		);
		return $this;
	}

	/**
	 * Action Column : Inlist edit/delete
	 * @return \Base\DataGrid 
	 */
	function UnsetActionColumn() {
		$this->UnsetColumn($this->_Resources['ActionColIndexName']);
		$this->Options->_unset('gridComplete');
		return $this;
	}

	/**
	 * Double Click Edit ability : dbl click causes row to turn to edit mode
	 * @return \Base\DataGrid 
	 */
	function SetDblClickEdit() {
		$this->Options->ondblClickRow("##JSFUN##
function(id){
	DGEDITROW('{$this->ID}', id, $('#DGEDITROW_{$this->ID}_'+id).get(0))
}##JSFUN##");
		return $this;
	}

	/**
	 * Double Click Edit ability : dbl click causes row to turn to edit mode
	 * @return \Base\DataGrid 
	 */
	function UnsetDblClickEdit() {
		$this->Options->_unset('ondblClickRow');
		return $this;
	}

	private $_NavOptions = array('add' => false, 'edit' => false, 'del' => false, 'search' => false, 'multisearch' => false, 'saveall' => true);

	/**
	 * @param arr $arrOptions
	 * <br/>array('add' => false, 'edit' => false, 'del' => false, 'search' => false, 'multisearch' => false, 'saveall'=>true)
	 * @return \Base\DataGrid 
	 */
	function SetNavigator($arrOptions = array()) {
		if (!is_array($arrOptions))
			$arrOptions = array();
		$this->_NavOptions = $arrOptions = array_merge(
				$this->_NavOptions
				, $arrOptions);
		$this->HasSearch = $arrOptions['search'];
		$this->HasMultiSearch = $arrOptions['multisearch'];
		$this->_PrivateCallbacks['navGrid'] = "
\$('#{$this->ID}').jqGrid(
	'navGrid'
	,'#{$this->_PagerID}'
	," . json_encode($arrOptions) . "
	,{},{},{}
	,{multipleSearch:" . ($arrOptions['multisearch'] ? 'true' : 'false') . "}
)"
				. (!empty($arrOptions['saveall']) ? ".navButtonAdd('#{$this->_PagerID}',
					{ caption:'', buttonicon:'ui-icon-saveall', onClickButton:function(){
						$('#{$this->ID} [rel=\"GridInlistSaveBtn\"]:visible').click()
					}, position: 'first', title:'" . $this->_Resources['SaveAll'] . "', cursor: 'pointer'})" : "");
//				. ".navButtonAdd('#{$this->_PagerID}',
//					{ caption:'', buttonicon:'ui-icon-info', onClickButton:function(){
//						jAlert('info', 'Webdesignir php datagrid on top of jQGrid.<br/>Server side by : Abbas Ali Hashemian<br/>tondarweb@gmail.com - webdesignir.com', 'About datagrid' )
//					}, position: 'last', title:'About', cursor: 'pointer'})";
		return $this;
	}

	/**
	 * @return \Base\DataGrid 
	 */
	function UnsetNavigator() {
		$this->HasSearch = false;
		$this->HasMultiSearch = false;
		if (isset($this->_PrivateCallbacks['navGrid']))
			unset($this->_PrivateCallbacks['navGrid']);
		return $this;
	}

	/**
	 * @param arr $arrOptions //array('stringResult' => true, 'searchOnEnter' => true)
	 * @return \Base\DataGrid 
	 */
	function SetFilterBar($arrOptions = array()) {
		$this->HasFilterBar = true;
		if (!is_array($arrOptions))
			$arrOptions = array();
		$arrOptions = array_merge(array('stringResult' => true, 'searchOnEnter' => true), $arrOptions);
		$arrOptions = json_encode($arrOptions);
		$this->_PrivateCallbacks['filterToolbar'] = "
\$('#{$this->ID}').jqGrid(
	'filterToolbar'
	,$arrOptions
);";
		return $this;
	}

	/**
	 * @return \Base\DataGrid 
	 */
	function UnsetFilterBar() {
		$this->HasFilterBar = false;
		if (isset($this->_PrivateCallbacks['filterToolbar']))
			unset($this->_PrivateCallbacks['filterToolbar']);
		return $this;
	}

	/**
	 * @return \Base\DataGrid 
	 */
	function Resizable($TurnOn = true) {
		if ($TurnOn)
			$this->_PrivateCallbacks['gridResize'] = "\$('#{$this->ID}').jqGrid('gridResize',{});";
		else if (isset($this->_PrivateCallbacks['gridResize']))
			unset($this->_PrivateCallbacks['gridResize']);
		return $this;
	}

	#----------------- Event Handlers -----------------#
	/**
	 * @param function $fncEventHandler<br>
	 * Params:&$arrRule, $arrParams<br>
	 * $arrRule : {"field":"i.FieldName","op":"cn","data":"..."}<br>
	 * returns NOTHING or FALSE to omit the filter<br>
	 * SET NULL TO REMOVE
	 * @param array $arrParams
	 * @param str $KW	//event handler kw
	 * @return \Base\DataGrid
	 */

	public function OnPreColFilter($fncEventHandler = NULL, $arrParams = NULL, $KW = NULL) {
		$PreColFilter = &$this->EventHandlers['PreColFilter'];
		if (!$PreColFilter)
			$PreColFilter = array();
		if (!func_num_args())
			return $PreColFilter;
		else {
			if ($fncEventHandler === NULL)
				$PreColFilter = array();
			elseif ($KW)
				$PreColFilter[$KW] = array('FNC' => $fncEventHandler, 'PARAMS' => $arrParams);
			else
				$PreColFilter[] = array('FNC' => $fncEventHandler, 'PARAMS' => $arrParams);
		}
		return $this;
	}

	#----------------- data -----------------#

	/**
	 * @return \Base\DataGrid 
	 */
	private function AjaxData() {
		$dg = &$this;
		$Queries = &$this->_Queries;
//		array($this, $this->Queries)
		\Output::AddIn_AjaxOutput(
				function()use($dg, $Queries) {
			$PNames = $dg->Options->prmNames;

			$dgp = new DataGridParams();
			$dgp->DataGrid = $dg;

			#--------- identifying
			$dgp->RowID = T\DB::RealEscape(\GPCS::POST($PNames['id']));
//					$dgp->SubGridID = \GPCS::POST($HTTPParamNames['subgridid']);
//					$dgp->NPage = \GPCS::POST($HTTPParamNames['npage']);
//					$dgp->TotalRows = \GPCS::POST($HTTPParamNames['totalrows']);
			#--------- others
			$dgp->nd = \GPCS::POST($PNames['nd']); //the time passed to the request (for IE browsers not to cache the request) (default value nd)

			switch (\GPCS::POST($PNames['oper'])) {
				case $PNames['addoper']:
					if (!empty($Queries['INSERT'])) {
//								$fncValidator();
						$Queries['INSERT']['FNC']($dgp, $Queries['INSERT']['PARAMS']);
					}
					break;
				case $PNames['editoper']:
					if (!empty($Queries['UPDATE'])) {
//								$fncValidator();
						$Queries['UPDATE']['FNC']($dgp, $Queries['UPDATE']['PARAMS']);
					}
					break;
				case $PNames['deloper']:
					if (!empty($Queries['DELETE'])) {
						$Queries['DELETE']['FNC']($dgp, $Queries['DELETE']['PARAMS']);
					}
					break;
				default: //list+search
					if ($Queries['SELECT']) {
						#--------- sorting
						$SortColumn = \GPCS::POST($PNames['sort']);
						$SortOrder = strtolower(\GPCS::POST($PNames['order'])) == 'desc' ? 'DESC' : 'ASC';
						$SortColumn = $dg->GetColumn($SortColumn);
						if ($SortColumn && ($SortColumn->sortable() || ($SortColumn->sortable() !== false && (!empty($dg->Options->cmTemplate['sortable']) || $dg->sortable()))))
							$SortColumn = $SortColumn->index();
						else {
							$SortColumn = '1';
							$SortOrder = 'ASC';
						}
						$dgp->Sort = " $SortColumn $SortOrder ";
						$dgp->SortColumn = $SortColumn;
						$dgp->SortOrder = $SortOrder;
						#
						#
								#--------- paging
						$dgp->PageNo = intval(\GPCS::POST($PNames['page']));
						$dgp->PageNo = $dgp->PageNo <= 0 ? 1 : $dgp->PageNo;
						$HowManyRows = intval(\GPCS::POST($PNames['rows']));
						if ($HowManyRows && array_search($HowManyRows, $dg->Options->rowList(), true) !== false)
							$dg->Options->rowNum($HowManyRows);
						$dgp->RowsPerPage = $dg->Options->rowNum();
						#
						#
								#--------- FILTERING
						$Filter = \GPCS::POST($PNames['filters']);
						$Filter = $Filter ? json_decode($Filter, true, 4) : array();
						$dgp->HasFilter = (strtolower(\GPCS::POST($PNames['search'])) == 'true') && ($dg->HasFilterBar || $dg->HasSearch);
						if (empty($Filter['groupOp']) || empty($Filter['rules']))
							$dgp->HasFilter = false;
						if ($dgp->HasFilter) {
							$dgp->SQLWhereClause = array();
							//Loop Rules
							foreach ($Filter['rules'] as $Rule) {
								if (!isset($Rule['data']) || empty($Rule['op']) || $Rule['data'] === '')
									continue;
								$Column = $dg->GetColumn($Rule['field']);
								$Column = $Column ? $Column->_getArray() : $Column;
								if (!$Column ||
										(isset($Column['search']) && $Column['search'] === false) ||
										(empty($Column['search']) && empty($dg->Options->cmTemplate['search']))
								) {//Validating and FASTENING Rule
									$dgp->HasFilter = false;
									throw new \Err(__METHOD__, 'This field is not specified for filtering(search)! Hacking attempt?!', "rule:" . print_r($Rule));
									exit;
								}
								$ColName = isset($Column['whereclause_leftside']) ? $Column['whereclause_leftside'] : null;
								if (!$ColName)
									$ColName = $Column['index'];

								$SkipRule = false;
								$PreColFilter = $dg->OnPreColFilter();
								if ($PreColFilter) {
									foreach ($PreColFilter as $Handler) {
										$HandlerResult = NULL;
										if (is_callable($Handler['FNC']))
											$HandlerResult = $Handler['FNC']($Rule, $Handler['PARAMS']);
										if ($HandlerResult === FALSE) {
											$SkipRule = true;
											break;
										}
									}
								}
								if ($SkipRule)
									continue;
								//SQL INJECTION IS BLOCKED HERE
								$CharsetLevel = (isset($Column['CharsetLevel'])) ? $Column['CharsetLevel'] : 2;
								$Conversion = ($CharsetLevel !== false);
								$Rule['data'] = T\DB::RealEscape($Rule['data']);
								$Rule['data'] = T\DB::EscapeLikeWildCards($Rule['data']);
								$arrConditionList = array(//:DATA is sql parameter
									"eq" => " $ColName =		" . ($Conversion ? T\DB::CharsetLevel("'{$Rule['data']}' ", $CharsetLevel) : "'{$Rule['data']}' ")  //equal
									, "ne" => " $ColName !=		" . ($Conversion ? T\DB::CharsetLevel("'{$Rule['data']}' ", $CharsetLevel) : "'{$Rule['data']}' ")  //not equal
									, "lt" => " $ColName <		" . ($Conversion ? T\DB::CharsetLevel("'{$Rule['data']}' ", $CharsetLevel) : "'{$Rule['data']}' ")  //less than
									, "le" => " $ColName <=		" . ($Conversion ? T\DB::CharsetLevel("'{$Rule['data']}' ", $CharsetLevel) : "'{$Rule['data']}' ")  //less or equal
									, "gt" => " $ColName >		" . ($Conversion ? T\DB::CharsetLevel("'{$Rule['data']}' ", $CharsetLevel) : "'{$Rule['data']}' ")  //greater than
									, "ge" => " $ColName >=		" . ($Conversion ? T\DB::CharsetLevel("'{$Rule['data']}' ", $CharsetLevel) : "'{$Rule['data']}' ")  //greater or equal
									, "bw" => " $ColName LIKE		" . ($Conversion ? T\DB::CharsetLevel("'{$Rule['data']}%' ", $CharsetLevel) : "'{$Rule['data']}%' ") . " ESCAPE '" . T\DB::LikeEscapeChar . "'" //begin with
									, "bn" => " $ColName NOT LIKE	" . ($Conversion ? T\DB::CharsetLevel("'{$Rule['data']}%' ", $CharsetLevel) : "'{$Rule['data']}%' ") . " ESCAPE '" . T\DB::LikeEscapeChar . "'" //not begin with
									, "in" => " $ColName LIKE		" . ($Conversion ? T\DB::CharsetLevel("'%{$Rule['data']}%' ", $CharsetLevel) : "'%{$Rule['data']}%' ") . " ESCAPE '" . T\DB::LikeEscapeChar . "'" //is in
									, "ni" => " $ColName NOT LIKE	" . ($Conversion ? T\DB::CharsetLevel("'%{$Rule['data']}%' ", $CharsetLevel) : "'%{$Rule['data']}%' ") . " ESCAPE '" . T\DB::LikeEscapeChar . "'" //is not in
									, "ew" => " $ColName LIKE		" . ($Conversion ? T\DB::CharsetLevel("'%{$Rule['data']}' ", $CharsetLevel) : "'%{$Rule['data']}' ") . " ESCAPE '" . T\DB::LikeEscapeChar . "'" //end with
									, "en" => " $ColName NOT LIKE	" . ($Conversion ? T\DB::CharsetLevel("'%{$Rule['data']}' ", $CharsetLevel) : "'%{$Rule['data']}' ") . " ESCAPE '" . T\DB::LikeEscapeChar . "'" //not end with
									, "cn" => " $ColName LIKE		" . ($Conversion ? T\DB::CharsetLevel("'%{$Rule['data']}%' ", $CharsetLevel) : "'%{$Rule['data']}%' ") . " ESCAPE '" . T\DB::LikeEscapeChar . "'" //contain
									, "nc" => " $ColName NOT LIKE	" . ($Conversion ? T\DB::CharsetLevel("'%{$Rule['data']}%' ", $CharsetLevel) : "'%{$Rule['data']}%' ") . " ESCAPE '" . T\DB::LikeEscapeChar . "'" //not contain
									, "nu" => " ISNULL($ColName) " //is null
									, "nn" => " NOT ISNULL($ColName) " //not null
								);
								$arrConditionList = array_intersect_key($arrConditionList, array_flip($dg->Options->searchoptions['sopt']));
								if (isset($arrConditionList[$Rule['op']]))
									$dgp->SQLWhereClause[] = $arrConditionList[$Rule['op']];
								if (!$dg->HasMultiSearch)
									break;
							}
							$Filter['groupOp'] = strtolower($Filter['groupOp']) === 'and' ? ' AND ' : ' OR ';
							if (count($dgp->SQLWhereClause))
								$dgp->SQLWhereClause = implode($Filter['groupOp'], $dgp->SQLWhereClause);
							else
								$dgp->SQLWhereClause = ' 1=1 ';
						}
						#
						#
								#-----------SELECT OPERATION
						$dt = $Queries['SELECT']['FNC']($dgp, $Queries['SELECT']['PARAMS']);
						if (!$dt)
							$dt = array();
						if (is_object($dt) && is_a($dt, '\CDbDataReader')) //yii framework
							$dt = $dt->readAll();
						if ($DataKeys = $dg->Options->DataKey)
							$DataKeys = array_flip(explode(',', str_replace(' ', '', $DataKeys)));
						foreach ($dt as $drIdx => $dr) {
							//data key
							$DatarowID = $drIdx;
							if ($DataKeys) {
								$DatarowID = array_intersect_key($dr, $DataKeys);
								if (count($DatarowID) != count($DataKeys))
									throw new \Err(__METHOD__, 'Specified Data Keys doesn`t match on SQL Datarow (Probably Query doesn`t contain the DataKey)', array($DataKeys, $dr));
								$DatarowID = implode('_', $DatarowID);
							}
							//filter and sort data row fields based on grid columns
							$ArrangedDR = array();
							foreach ($dg->Options->colModel() as $col) {
								$IsRightCol = isset($col['name']) && array_key_exists($col['name'], $dr);
								if ($IsRightCol && isset($col['type']) && $col['type'] == 'date' && is_null($dr[$col['name']]))
									$dr[$col['name']] = '';
								$ArrangedDR[] = $IsRightCol ? $dr[$col['name']] : null;
							}
							$dr = $ArrangedDR;
							unset($ArrangedDR);
							$dt[$drIdx] = array('id' => $DatarowID, 'cell' => array_values($dr));
						}
						T\HTTP::Header(\Consts\Header::ContentType . 'text/json');
//								echo json_encode(array(
//											'total' => $dgp->TotalPages
//											, 'page' => $dgp->PageNo
//											, 'records' => $dgp->AllRowsCount
//											, 'rows' => $dt
//										));
//								
//								echo T\Basics::str_ReplaceRecursively('\\t\\t', '\\t'
//										, T\Basics::JSON_Advanced(array(
//											'total' => $dgp->TotalPages
//											, 'page' => $dgp->PageNo
//											, 'records' => $dgp->AllRowsCount
//											, 'rows' => $dt
//										))
//								);
						echo T\Basics::JSON_Advanced(array(
							'total' => $dgp->TotalPages
							, 'page' => $dgp->PageNo
							, 'records' => $dgp->AllRowsCount
							, 'rows' => $dt
						));
					}
					break;
			}
		}
				, $this->_AjaxKW, NULL, 'DataGrid_AjaxPostBack_UniqueContentKW');
		return $this;
	}

	/**
	 * @param fnc $fncQuery
	 * function(DataGridParams $DGP, $ExtraParams){<br/>
	 * 		$AllCount=Tools\DB::GetField('SELECT COUNT(*) ...');
	 * 		$DGP->QueryLimitParams($AllCount, $ref_LimitIdx, $ref_LimitLen);<br/>
	 * 		return Tools\DB::GetTable('SELECT ... LIMIT :0,:1', $ref_LimitIdx, $ref_LimitLen, $DGP->SortColumn);<br/>
	 * }
	 * @return \Base\DataGrid 
	 */
	function SelectQuery($fncQuery, $ExtraParams = NULL) {
		if (!$this->GetOptions()->rowList())
			$this->GetOptions()->rowList(array(10, 20, 30));
		$this->_Queries['SELECT'] = array('FNC' => $fncQuery, 'PARAMS' => $ExtraParams);
		return $this->AjaxData(); //will add in ajax output so repeated calls will not mean repeated runs
	}

	/**
	 * @param fnc $fncQuery
	 * function(DataGridParams $DGP, $ExtraParams){<br/>
	 * }
	 * @return \Base\DataGrid 
	 */
	function InsertQuery($fncQuery, $ExtraParams = NULL) {
		$this->_Queries['INSERT'] = array('FNC' => $fncQuery, 'PARAMS' => $ExtraParams);
		return $this->AjaxData();
	}

	/**
	 * @param fnc $fncQuery
	 * function(DataGridParams $DGP, $ExtraParams){<br/>
	 * }
	 * @return \Base\DataGrid 
	 */
	function UpdateQuery($fncQuery, $ExtraParams = NULL) {
		$this->_Queries['UPDATE'] = array('FNC' => $fncQuery, 'PARAMS' => $ExtraParams);
		return $this->AjaxData();
	}

	/**
	 * @param fnc $fncQuery
	 * function(DataGridParams $DGP, $ExtraParams){<br/>
	 * }
	 * @return \Base\DataGrid 
	 */
	function DeleteQuery($fncQuery, $ExtraParams = NULL) {
		$this->_Queries['DELETE'] = array('FNC' => $fncQuery, 'PARAMS' => $ExtraParams);
		return $this->AjaxData();
	}

	#----------------- render -----------------#

	protected function Render() {
		if (!count($this->_Columns))
			return Container::Render();

		//Columns
		$IndexedColModel = array();
		foreach ($this->Options->colModel() as $Key => $Model) {
			if (isset($Model['whereclause_leftside']))
				unset($Model['whereclause_leftside']);
			if (!isset($Model['header']))
				$Model['header'] = $Model['name'];
			$this->Options->colNames(array($Model['header']));
			//calendar datepicker
			if (isset($Model['type'])) {
				if (strtolower($Model['type']) == 'date') {
					$Model['searchoptions']['dataInit'] = "##JSFUN##function(elm){link_dtpicker(elm)}##JSFUN##";
					$Model['editoptions']['dataInit'] = "##JSFUN##function(elm){link_dtpicker(elm)}##JSFUN##";
					if (!isset($Model['edittype']))
						$Model['edittype'] = 'text';
					if (!isset($Model['stype']))
						$Model['stype'] = 'text';
					if (!isset($Model['formatter']))
						$Model['formatter'] = 'text';
				}else {
					if (!isset($Model['edittype']))
						$Model['edittype'] = $Model['type'];
					if (!isset($Model['stype']))
						$Model['stype'] = $Model['type'];
					if (!isset($Model['formatter']))
						$Model['formatter'] = $Model['type'];
				}
			}
			$IndexedColModel[] = $Model;
		}
		$this->Options->_unset('colModel')->colModel($IndexedColModel); //place $IndexedColModel
		$this->Config(\html::DataGridConfig()
						->cmTemplate(array('searchoptions' => array('sopt' => $this->Options->searchoptions['sopt'])
		)));

		//Options
		$this->Options->mtype('post');
		$Options = T\Basics::JSON_Advanced($this->Options->_getArray(), '##JSFUN##');

		//Methods
		$Methods = '';
		foreach ($this->Methods as $MName => $MArgs) {
			if ($MArgs)
				$MArgs = substr(T\Basics::JSON_Advanced($MArgs, '##JSFUN##'), 1, -1);
			$Methods.=".$MName($MArgs)";
		}

		$PrivateCallbacks = implode('; ', $this->_PrivateCallbacks);

		$this->AddContent("
<table id='{$this->ID}'" . ($this->_TableClasses ? " class='{$this->_TableClasses}'" : "") . "></table> 
<div id='{$this->_PagerID}'></div>
<script type='text/javascript'>
	function DGConstruct_{$this->ID}(){
		var lastSel;
		\$('#{$this->ID}').jqGrid($Options)$Methods
		{$PrivateCallbacks}
	}
	if(typeof(PostBack)!='undefined')
		DGConstruct_{$this->ID}()
	else
		PBDocComplete.push(DGConstruct_{$this->ID})
</script>
"
				, NULL, "DataGrid_{$this->ID}");
		return Container::Render();
	}

}

/**
 * @method \Base\DataGridColumn type($str_date_text_textarea_select_checkbox_password_button_image_file)
 * @method \Base\DataGridColumn header($str_columnHeader)	//column header caption
 * 
 * 
 * @method \Base\DataGridColumn align($string)
 * 	//	Defines the alignment of the cell in the Body layer, not in header cell. Possible values: left, center, right.	left<br>
 * cellattr	function	This function add attributes to the cell during the creation of the data - i.e dynamically. By example all valid attributes for the table cell can be used or a style attribute with different properties. The function should return string. Parameters passed to this function are:<br>
 * rowId - the id of the row<br>
 * val - the value which will be added in the cell<br>
 * rawObject - the raw object of the data row - i.e if datatype is json - array, if datatype is xml xml node.<br>
 * cm - all the properties of this column listed in the colModel<br>
 * rdata - the data row which will be inserted in the row. This parameter is array of type name:value, where name is the name in colModel 	null<br>
 * @method \Base\DataGridColumn classes($string)
 * 	//	This option allow to add classes to the column. If more than one class will be used a space should be set. By example classes:'class1 class2' will set a class1 and class2 to every cell on that column. In the grid css there is a predefined class ui-ellipsis which allow to attach ellipsis to a particular row. Also this will work in FireFox too.	empty string
 * @method \Base\DataGridColumn datefmt($string)
 * 	//	Governs format of sorttype:date (when datetype is set to local) and editrules {date:true} fields. Determines the expected date format for that column. Uses a PHP-like date formatting. Currently ”/”, ”-”, and ”.” are supported as date separators. Valid formats are:<br>
 * y,Y,yyyy for four digits year<br>
 * YY, yy for two digits year<br>
 * m,mm for months<br>
 * d,dd for days.<br>
 * See Array Data 	ISO Date (Y-m-d)<br>
 * @method \Base\DataGridColumn defval($string)
 * 	//	The default value for the search field. This option is used only in Custom Searching and will be set as initial search. 	empty
 * @method \Base\DataGridColumn editable($boolean)
 * 	//	Defines if the field is editable. This option is used in cell, inline and form modules. See editing 	false
 * @method \Base\DataGridColumn editoptions($array)
 * 	//	Array of allowed options (attributes) for edittype option editing	empty array<br>
 * editoptions: {<br>
 * 	size:10<br>
 * 	, maxlength: 15<br>
 * 	, value:"yes:no"	//"val:title;val2:title2"(select-ddl) "yes:no"(checkbox)<br>
 *  , rows:"2"<br>
 * 	, cols:"10"<br>
 *  , multiple:'multiple'	//for select elements<br>
 * }
 * @method \Base\DataGridColumn editrules($array)
 * 	//	sets additional rules for the editable field editing	empty array<br>
 * >>edithidden:	boolean	This option is valid only in form editing module. By default the hidden fields are not editable. If the field is hidden in the grid and edithidden is set to true, the field can be edited when add or edit methods are called.<br><br>
 * >>required:	boolean	(true or false) if set to true, the value will be checked and if empty, an error message will be displayed.<br><br>
 * >>number:	boolean	(true or false) if set to true, the value will be checked and if this is not a number, an error message will be displayed.<br><br>
 * >>integer:	boolean	(true or false) if set to true, the value will be checked and if this is not a integer, an error message will be displayed.<br><br>
 * >>minValue:	number(integer)	if set, the value will be checked and if the value is less than this, an error message will be displayed.<br><br>
 * >>maxValue:	number(integer)	if set, the value will be checked and if the value is more than this, an error message will be displayed.<br><br>
 * >>email:	boolean	if set to true, the value will be checked and if this is not valid e-mail, an error message will be displayed<br><br>
 * >>url:	boolean	if set to true, the value will be checked and if this is not valid url, an error message will be displayed<br><br>
 * >>date:	boolean	if set to true a value from datefmt option is get (if not set ISO date is used) and the value will be checked and if this is not valid date, an error message will be displayed<br><br>
 * >>time:	boolean	if set to true, the value will be checked and if this is not valid time, an error message will be displayed. Currently we support only hh:mm format and optional am/pm at the end<br><br>
 * >>custom:	boolean	if set to true allow definition of the custom checking rules via a custom function. See below<br><br>
 * >>custom_func:	function	this function should be used when a custom option is set to true. Parameters passed to this function are the value, which should be checked and the name - the property from colModel. The function should return array with the following parameters: first parameter - true or false. The value of true mean that the checking is successful false otherwise; the second parameter have sense only if the first value is false and represent the error message which will be displayed to the user. Typically this can look like this [false,”Please enter valid value”]<br>
 * @method \Base\DataGridColumn edittype($str_text_textarea_select_checkbox_password_button_image_file)
 * 	//	Defines the edit type for inline and form editing Possible values: text, textarea, select, checkbox, password, button, image and file. See also editing	text
 * @method \Base\DataGridColumn firstsortorder($string)
 * 	//	If set to asc or desc, the column will be sorted in that direction on first sort.Subsequent sorts of the column will toggle as usual	null
 * @method \Base\DataGridColumn fixed($boolean)
 * 	//	If set to true this option does not allow recalculation of the width of the column if shrinkToFit option is set to true. Also the width does not change if a setGridWidth method is used to change the grid width.	false
 * @method \Base\DataGridColumn formoptions($array)
 * 	//	Defines various options for form editing. See Form options 	empty
 * formoptions(array('rowpos'=>1, 'colpos'=>2))
 * @method \Base\DataGridColumn formatoptions($array)
 * 	//	Format options can be defined for particular columns, overwriting the defaults from the language file. See Formatter for more details.	none
 * @method \Base\DataGridColumn formatter($mixed)
 * 	//	The predefined types (string) or custom function name that controls the format of this field. See Formatter for more details.	none
 * @method \Base\DataGridColumn frozen($boolean)
 * 	//	If set to true determines that this column will be frozen after calling the setFrozenColumns method	false
 * @method \Base\DataGridColumn hidedlg($boolean)
 * 	//	If set to true this column will not appear in the modal dialog where users can choose which columns to show or hide. See Show/Hide Columns.	false
 * @method \Base\DataGridColumn hidden($boolean)
 * 	//	Defines if this column is hidden at initialization.	false
 * @method \Base\DataGridColumn index($string)
 *  //!!!!!(just right side of dot "prj.ID"->"ID") Will be used as default value of the "name" if was not set and for where clause if "whereclause" was not set and for sort(ORDER)<br/>
 *  //BE CAREFUL to not use '`' (tbl.`Col` is wrong)
 * 	//	Set the index name when sorting. Passed as sidx parameter.	empty string
 * @method \Base\DataGridColumn jsonmap($string)
 * 	//	Defines the json mapping for the column in the incoming json string. See Retrieving Data 	none
 * @method \Base\DataGridColumn key($boolean)
 * 	//	In case if there is no id from server, this can be set as as id for the unique row id. Only one column can have this property. If there are more than one key the grid finds the first one and the second is ignored.	false
 * @method \Base\DataGridColumn label($string)
 * 	//	When colNames array is empty, defines the heading for this column. If both the colNames array and this setting are empty, the heading for this column comes from the name property.	none
 * @method \Base\DataGridColumn name($string)
 *  //!!!!!Will be used to get value from DataTable in a select query, no more<br/>
 * 	//	Set the unique name in the grid for the column. This property is required. As well as other words used as property/event names, the reserved words (which cannot be used for names) include subgrid, cb and rn.	Required
 * @method \Base\DataGridColumn whereclause_leftside($string)
 *  //!!!!!The left side of this col where clause in the Query
 * @method \Base\DataGridColumn CharsetLevel($intLevel_TDBCharsetLevelx_DB)
 *  //!!!!!String Charset convertion for comparisons
 * @method \Base\DataGridColumn resizable($boolean)
 * 	//	Defines if the column can be re sized 	true
 * @method \Base\DataGridColumn search($boolean)
 * 	//	When used in search modules, disables or enables searching on that column. Search Configuration	true
 * @method \Base\DataGridColumn searchoptions($array)
 * 	//	Defines the search options used searching Search Configuration	empty
 * @method \Base\DataGridColumn sortable($boolean)
 * 	//	Defines is this can be sorted.	true
 * @method \Base\DataGridColumn sorttype($mixed)
 * 	//	Used when datatype is local. Defines the type of the column for appropriate sorting.Possible values:<br>
 * int/integer - for sorting integer<br>
 * float/number/currency - for sorting decimal numbers<br>
 * date - for sorting date<br>
 * text - for text sorting<br>
 * function - defines a custom function for sorting. To this function we pass the value to be sorted and it should return a value too.<br>
 * See Array Data 	text
 * @method \Base\DataGridColumn stype($str_text_textarea_select_checkbox_password_button_image_file)
 * 	//	Determines the type of the element when searching. See Search Configuration	text
 * @method \Base\DataGridColumn surl($string)
 * 	//	Valid only in Custom Searching and edittype : 'select' and describes the url from where we can get already-constructed select element	empty string
 * @method \Base\DataGridColumn template($object)
 * 	//	Set of valid properties for the colModel. This option can be used if you want to overwrite a lot of default values in the column model with easy. See cmTemplate in grid options 	null
 * @method \Base\DataGridColumn title($boolean)
 * 	//	If this option is false the title is not displayed in that column when we hover a cell with the mouse	true
 * @method \Base\DataGridColumn width($number)
 * 	//	Set the initial width of the column, in pixels. This value currently can not be set as percentage	150
 * @method \Base\DataGridColumn xmlmap($string)
 * 	//	Defines the xml mapping for the column in the incomming xml file. Use a CSS specification for this See Retrieving Data	none<br>
 * unformat	function	Custom function to “unformat” a value of the cell when used in editing See Custom Formatter. (Unformat is also called during sort operations. The value returned by unformat is the value compared during the sort.) 	null
 * @method \Base\DataGridColumn viewable($boolean)
 * 	//	This option is valid only when viewGridRow method is activated. When the option is set to false the column does not appear in view Form
 */
class DataGridColumn extends ConfigArray {
//	public function editoptions(DataGridEditOptions $EditOptions) {
//		$this->__call('editoptions', $EditOptions->_getArray());
//	}
}

/**
 * @method \Base\DataGridConfig DataKey($str)
 * 
 * 
 * 
 * @method \Base\DataGridConfig ajaxGridOptions($object)
 * 	//	This option allows to set global ajax settings for the grid when requesting data. Note that with this option it is possible to overwrite all current ajax settings in the grid including the error, complete and beforeSend events.	empty object	Yes
 * @method \Base\DataGridConfig ajaxSelectOptions($object)
 * 	//	This option allows to set global ajax settings for the select element when the select is obtained via dataUrl option in editoptions or searchoptions objects	empty object	Yes
 * @method \Base\DataGridConfig altclass($string)
 * 	//	The class that is used for applying different styles to alternate (zebra) rows in the grid. You can construct your own class and replace this value. This option is valid only if the altRows option is set to true	ui-priority-secondary	Yes. Requires reload
 * @method \Base\DataGridConfig altRows($boolean)
 * 	//	Set a zebra-striped grid (alternate rows have different styles)	false	Yes. After reload
 * @method \Base\DataGridConfig autoencode($boolean)
 * 	//	When set to true encodes (html encode) the incoming (from server) and posted data (from editing modules). For example < will be converted to &lt;.	false	Yes
 * @method \Base\DataGridConfig autowidth($boolean)
 * 	//	When set to true, the grid width is recalculated automatically to the width of the parent element. This is done only initially when the grid is created. In order to resize the grid when the parent element changes width you should apply custom code and use the setGridWidth method for this purpose	false	No
 * @method \Base\DataGridConfig caption($string)
 * 	//	Defines the caption for the grid. This caption appears in the caption layer, which is above the header layer (see How It Works). If the string is empty the caption does not appear.	empty string	No.Method avail.
 * @method \Base\DataGridConfig cellLayout($integer)
 * 	//	This option determines the padding + border width of the cell. Usually this should not be changed, but if custom changes to the td element are made in the grid css file, this will need to be changed. The initial value of 5 means paddingLef(2) + paddingRight (2) + borderLeft (1) = 5	5	No
 * @method \Base\DataGridConfig cellEdit($boolean)
 * 	//	Enables (disables) cell editing. See Cell Editing for more details	false	Yes
 * @method \Base\DataGridConfig cellsubmit($string)
 * 	//	Determines where the contents of the cell are saved. Possible values are remote and clientArray. See Cell Editing for more details.	'remote'	Yes
 * @method \Base\DataGridConfig cellurl($string)
 * 	//	the url where the cell is to be saved. See Cell Editing for more details	null	Yes
 * @method \Base\DataGridConfig cmTemplate(array|object $array)
 * 	// 	Defines a set of properties which override the default values in colModel. For example if you want to make all columns not sortable, then only one propery here can be specified instead of specifying it in all columns in colModel 	null 	No
 * @method \Base\DataGridConfig colModel($array)
 * 	//	Array which describes the parameters of the columns.This is the most important part of the grid. For a full description of all valid values see colModel API. 	empty array	No
 * @method \Base\DataGridConfig colNames($array)
 * 	//	An array in which we place the names of the columns. This is the text that appears in the head of the grid (header layer). The names are separated with commas. Note that the number of elements in this array should be equal of the number elements in the colModel array.	empty array[]	No
 * @method \Base\DataGridConfig data($array)
 * 	//	An array that stores the local data passed to the grid. You can directly point to this variable in case you want to load an array data. It can replace the addRowData method which is slow on relative big data	empty array	Yes
 * @method \Base\DataGridConfig datastr($string)
 * 	//	The string of data when datatype parameter is set to xmlstring or jsonstring	null	Yes
 * @method \Base\DataGridConfig datatype($string)
 * 	//	Defines in what format to expect the data that fills the grid. Valid options are xml (we expect data in xml format), xmlstring (we expect xml data as string), json (we expect data in JSON format), jsonstring (we expect JSON data as a string), local (we expect data defined at client side (array data)), javascript (we expect javascript as data), function (custom defined function for retrieving data), or clientSide to manually load data via the data array. See colModel API and Retrieving Data	xml	Yes
 * @method \Base\DataGridConfig deepempty($boolean)
 * 	//	This option should be set to true if an event or a plugin is attached to the table cell. The option uses jQuery empty for the the row and all its children elements. This of course has speed overhead, but prevents memory leaks	false	Yes
 * @method \Base\DataGridConfig deselectAfterSort($boolean)
 * 	//	Applicable only when we use datatype : local. Deselects currently selected row(s) when a sort is applied.	true	Yes
 * @method \Base\DataGridConfig direction($string)
 * 	//	Determines the direction of text in the grid. The default is ltr (Left To Right). When set to rtl (Right To Left) the grid automatically changes the direction of the text. It is important to note that in one page we can have two (or more) grids where the one can have direction ltr while the other can have direction rtl. This option works only in Firefox 3.x versions and Internet Explorer versions >=6. Currently Safari, Google Chrome and Opera do not completely support changing the direction to rtl. The most common problem in Firefox is that the default settings of the browser do not support rtl. In order change this see this HOW TO section in this chapter .	ltr	No
 * @method \Base\DataGridConfig editurl($string)
 * 	//	Defines the url for inline and form editing. May be set to clientArray to manually post data to server, see Inline Editing. 	null	Yes
 * @method \Base\DataGridConfig emptyrecords($string)
 * 	//	The string to display when the returned (or the current) number of records in the grid is zero. This option is valid only if viewrecords option is set to true.	see lang file	Yes
 * @method \Base\DataGridConfig ExpandColClick($boolean)
 * 	//	When true, the tree grid (see treeGrid) is expanded and/or collapsed when we click anywhere on the text in the expanded column. In this case it is not necessary to click exactly on the icon. 	true	No
 * @method \Base\DataGridConfig ExpandColumn($string)
 * 	//	Indicates which column (name from colModel) should be used to expand the tree grid. If not set the first one is used. Valid only when the treeGrid option is set to true.	null	No
 * @method \Base\DataGridConfig footerrow($boolean)
 * 	//	If set to true this will place a footer table with one row below the gird records and above the pager. The number of columns equal those specified in colModel	false	No
 * @method \Base\DataGridConfig forceFit($boolean)
 * 	//	If set to true, and a column's width is changed, the adjacent column (to the right) will resize so that the overall grid width is maintained (e.g., reducing the width of column 2 by 30px will increase the size of column 3 by 30px). In this case there is no horizontal scrollbar. Note: This option is not compatible with shrinkToFit option - i.e if shrinkToFit is set to false, forceFit is ignored.	false	No
 * @method \Base\DataGridConfig gridstate($string)
 * 	//	Determines the current state of the grid (i.e. when used with hiddengrid, hidegrid and caption options). Can have either of two states: visible or hidden. 	visible	No
 * @method \Base\DataGridConfig gridview($boolean)
 * 	//	In the previous versions of jqGrid including 3.4.X, reading a relatively large data set (number of rows >= 100 ) caused speed problems. The reason for this was that as every cell was inserted into the grid we applied about 5 to 6 jQuery calls to it. Now this problem is resolved; we now insert the entry row at once with a jQuery append. The result is impressive - about 3 to 5 times faster. What will be the result if we insert all the data at once? Yes, this can be done with a help of gridview option (set it to true). The result is a grid that is 5 to 10 times faster. Of course, when this option is set to true we have some limitations. If set to true we can not use treeGrid, subGrid, or the afterInsertRow event. If you do not use these three options in the grid you can set this option to true and enjoy the speed. 	false	Yes
 * @method \Base\DataGridConfig grouping($boolean)
 * 	//	Enables grouping in grid. Please refer to the Grouping page. 	false	Yes
 * @method \Base\DataGridConfig headertitles($boolean)
 * 	//	If the option is set to true the title attribute is added to the column headers. 	false	No
 * @method \Base\DataGridConfig height($mixed)
 * 	//	The height of the grid. Can be set as number (in this case we mean pixels) or as percentage (only 100% is acceped) or value of auto is acceptable. 	150	No.Method avail.
 * @method \Base\DataGridConfig hiddengrid($boolean)
 * 	//	If set to true the grid is initially is hidden. The data is not loaded (no request is sent) and only the caption layer is shown. When the show/hide button is clicked for the first time to show grid, the request is sent to the server, the data is loaded, and grid is shown. From this point we have a regular grid. This option has effect only if the caption property is not empty and the hidegrid property (see below) is set to true. 	false	No
 * @method \Base\DataGridConfig hidegrid($boolean)
 * 	//	Enables or disables the show/hide grid button, which appears on the right side of the caption layer (see How It Works). Takes effect only if the caption property is not an empty string. 	true	No
 * @method \Base\DataGridConfig hoverrows($boolean)
 * 	//	When set to false the effect of mouse hovering over the grid data rows is disabled.	true	Yes
 * @method \Base\DataGridConfig idPrefix($string)
 * 	//	When set, this string is added as prefix to the id of the row when it is built. 	empty	Yes
 * @method \Base\DataGridConfig ignoreCase($boolean)
 * 	//	By default the local searching is case-sensitive. To make the local search and sorting not case-insensitive set this options to true 	false	Yes
 * @method \Base\DataGridConfig inlineData($empty object)
 * 	//	an array used to add content to the data posted to the server when we are in inline editing. 	{}	Yes
 * @method \Base\DataGridConfig jsonReader($array)
 * 	//	An array which describes the structure of the expected json data. For a full description and default setting, see Retrieving Data JSON Data		No
 * @method \Base\DataGridConfig lastpage($integer)
 * 	//	Readonly property. Gives the total number of pages returned from the request. 	0	No
 * @method \Base\DataGridConfig lastsort($integer)
 * 	//	Readonly property. Gives the index of last sorted column beginning from 0. 	0	No
 * @method \Base\DataGridConfig loadonce($boolean)
 * 	//	If this flag is set to true, the grid loads the data from the server only once (using the appropriate datatype). After the first request, the datatype parameter is automatically changed to local and all further manipulations are done on the client side. The functions of the pager (if present) are disabled.	false	No
 * @method \Base\DataGridConfig loadtext($string)
 * 	//	The text which appears when requesting and sorting data. This parameter is located in language file. 	Loading…	No
 * @method \Base\DataGridConfig loadui($string)
 * 	//	This option controls what to do when an ajax operation is in progress.<br>
 * disable - disables the jqGrid progress indicator. This way you can use your own indicator.<br>
 * enable (default) - shows the text set in the loadtext property (default value is Loading…) in the center of the grid.<br>
 * block - displays the text set in the loadtext property and blocks all actions in the grid until the ajax request completes. Note that this disables paging, sorting and all actions on toolbar, if any. 	enable	Yes
 * @method \Base\DataGridConfig mtype($string)
 * 	//	Defines the type of request to make (“POST” or “GET”) 	GET	Yes
 * @method \Base\DataGridConfig multikey($string)
 * 	//	This parameter makes sense only when the multiselect option is set to true. Defines the key which should be pressed when we make multiselection. The possible values are: shiftKey - the user should press Shift Key, altKey - the user should press Alt Key, and ctrlKey - the user should press Ctrl Key. 	empty string	Yes
 * @method \Base\DataGridConfig multiboxonly($boolean)
 * 	//	This option works only when the multiselect option is set to true. When multiselect is set to true, clicking anywhere on a row selects that row; when multiboxonly is also set to true, the multiselection is done only when the checkbox is clicked (Yahoo style). Clicking in any other row (suppose the checkbox is not clicked) deselects all rows and selects the current row. 	false	Yes
 * @method \Base\DataGridConfig multiselect($boolean)
 * 	//	If this flag is set to true a multi selection of rows is enabled. A new column at left side containing checkboxes is added. Can be used with any datatype option. 	false	No. see HOWTO
 * @method \Base\DataGridConfig multiselectWidth($integer)
 * 	//	Determines the width of the checkbox column created when the multiselect option is enabled. 	20	No
 * @method \Base\DataGridConfig page($integer)
 * 	//	Set the initial page number when we make the request.This parameter is passed to the url for use by the server routine retrieving the data. 	1	Yes
 * @method \Base\DataGridConfig pager($mixed)
 * 	//	Defines that we want to use a pager bar to navigate through the records. This must be a valid HTML element; in our example we gave the div the id of “pager”, but any name is acceptable. Note that the navigation layer (the “pager” div) can be positioned anywhere you want, determined by your HTML; in our example we specified that the pager will appear after the body layer. The valid settings can be (in the context of our example) pager, #pager, jQuery('#pager'). I recommend to use the second one - #pager. See Pager	empty string. Currently only one pagebar is possible.	No
 * @method \Base\DataGridConfig pagerpos($string)
 * 	//	Determines the position of the pager in the grid. By default the pager element when created is divided in 3 parts (one part for pager, one part for navigator buttons and one part for record information). 	center	No
 * @method \Base\DataGridConfig pgbuttons($boolean)
 * 	//	Determines if the Pager buttons should be shown if pager is available. Also valid only if pager is set correctly. The buttons are placed in the pager bar. 	true	No
 * @method \Base\DataGridConfig pginput($boolean)
 * 	//	Determines if the input box, where the user can change the number of the requested page, should be available. The input box appears in the pager bar. 	true	No
 * @method \Base\DataGridConfig pgtext($string)
 * 	//	Show information about current page status. The first value is the current loaded page. The second value is the total number of pages. 	See lang file	Yes
 * @method \Base\DataGridConfig prmNames($array)
 * 	//	The default value of this property is:<br>
 * {page:“page”,rows:“rows”, sort:“sidx”, order:“sord”, search:“_search”, nd:“nd”, id:“id”, oper:“oper”, editoper:“edit”, addoper:“add”, deloper:“del”, subgridid:“id”, npage:null, totalrows:“totalrows”}<br>
 * This customizes names of the fields sent to the server on a POST request. For example, with this setting, you can change the sort order element from sidx to mysort by setting prmNames: {sort: “mysort”}. The string that will be POST-ed to the server will then be myurl.php?page=1&rows=10&mysort=myindex&sord=asc rather than myurl.php?page=1&rows=10&sidx=myindex&sord=asc<br>
 * So the value of the column on which to sort upon can be obtained by looking at $POST['mysort'] in PHP. When some parameter is set to null, it will be not sent to the server. For example if we set prmNames: {nd:null} the nd parameter will not be sent to the server. For npage option see the scroll option.<br>
 * These options have the following meaning and default values:<br>
 * page: the requested page (default value page)<br>
 * rows: the number of rows requested (default value rows)<br>
 * sort: the sorting column (default value sidx)<br>
 * order: the sort order (default value sord)<br>
 * search: the search indicator (default value _search)<br>
 * nd: the time passed to the request (for IE browsers not to cache the request) (default value nd)<br>
 * id: the name of the id when POST-ing data in editing modules (default value id)<br>
 * oper: the operation parameter (default value oper)<br>
 * editoper: the name of operation when the data is POST-ed in edit mode (default value edit)<br>
 * addoper: the name of operation when the data is posted in add mode (default value add)<br>
 * deloper: the name of operation when the data is posted in delete mode (default value del)<br>
 * totalrows: the number of the total rows to be obtained from server - see rowTotal (default value totalrows)<br>
 * subgridid: the name passed when we click to load data in the subgrid (default value id) 	none 	Yes
 * @method \Base\DataGridConfig postData($array)
 * 	//	This array is appended directly to the url. This is an associative array and can be used this way: {name1:value1…}. See API methods for manipulation. 	empty array	Yes
 * @method \Base\DataGridConfig reccount($integer)
 * 	//	Readonly property. Determines the exact number of rows in the grid. Do not confuse this with records parameter. Although in many cases they may be equal, there are cases where they are not. For example, if you define rowNum to be 15, but the request to the server returns 20 records, the records parameter will be 20, but the reccount parameter will be 15 (the grid you will have 15 records and not 20). 	0	No
 * @method \Base\DataGridConfig recordpos($string)
 * 	//	Determines the position of the record information in the pager. Can be left, center, right. 	right	No
 * @method \Base\DataGridConfig records($integer)
 * 	//	Readonly property. Gives the number of records returned as a result of a query to the server. 	none	No
 * @method \Base\DataGridConfig recordtext($string)
 * 	//	Text that can be shown in the pager. Also this option is valid if viewrecords option is set to true. This text appears only if the total number of records is greater then zero. In order to show or hide some information the items in {} mean the following:<br>
 * {0} - the start position of the records depending on page number and number of requested records<br>
 * {1} - the end position<br>
 * {2} - total records returned from the server. 	see lang file	Yes
 * @method \Base\DataGridConfig resizeclass($string)
 * 	//	Assigns a class to columns that are resizable so that we can show a resize handle only for ones that are resizable. 	empty string	No
 * @method \Base\DataGridConfig rowList($array)
 * 	//[]	An array to construct a select box element in the pager in which we can change the number of the visible rows. When changed during the execution, this parameter replaces the rowNum parameter that is passed to the url. If the array is empty, this element does not appear in the pager. Typically you can set this like [10,20,30]. If the rowNum parameter is set to 30 then the selected value in the select box is 30. 	empty arrray	No
 * @method \Base\DataGridConfig rownumbers($boolean)
 * 	//	If this option is set to true, a new column at left of the grid is added. The purpose of this column is to count the number of available rows, beginning from 1. In this case colModel is extended automatically with new element with the name rn. Note: Do not to use the name rn in the colModel. 	false	No
 * @method \Base\DataGridConfig rowNum($integer)
 * 	//	Sets how many records we want to view in the grid. This parameter is passed to the url for use by the server routine retrieving the data. Note that if you set this parameter to 10 (i.e. retrieve 10 records) and your server return 15 then only 10 records will be loaded. Set this parameter to -1 (unlimited) to disable this checking. 	20	Yes
 * @method \Base\DataGridConfig rowTotal($integer)
 * 	//	When set this parameter can instruct the server to load the total number of rows needed to work on. Note that rowNum determines the total records displayed in the grid, while rowTotal determines the total number of rows on which we can operate. When this parameter is set, we send an additional parameter to the server named totalrows. You can check for this parameter, and if it is available you can replace the rows parameter with this one. Mostly this parameter can be combined with loadonce parameter set to true.	null	Yes
 * @method \Base\DataGridConfig rownumWidth($integer)
 * 	//	Determines the width of the row number column if rownumbers option is set to true. 	25	No
 * @method \Base\DataGridConfig savedRow($array)
 * 	//	This is a readonly property and is used in inline and cell editing modules to store the data, before editing the row or cell. See Cell Editing and Inline Editing. 	empty array	No
 * @method \Base\DataGridConfig searchdata($array)
 * 	// {}	This property contain the searched data in pair name:value.	empty array{}	Yes
 * @method \Base\DataGridConfig scroll($boolean)
 * 	// or<br>
 * integer	Creates dynamic scrolling grids. When enabled, the pager elements are disabled and we can use the vertical scrollbar to load data. When set to true the grid will always hold all the items from the start through to the latest point ever visited.<br>
 * When scroll is set to an integer value (example 1), the grid will just hold the visible lines. This allow us to load the data in portions whitout caring about memory leaks. In addition to this we have an optional extension to the server protocol: npage (see prmNames array). If you set the npage option in prmNames, then the grid will sometimes request more than one page at a time; if not, it will just perform multiple GET requests. 	false	No
 * @method \Base\DataGridConfig scrollOffset($integer)
 * 	//	Determines the width of the vertical scrollbar. Since different browsers interpret this width differently (and it is difficult to calculate it in all browsers) this can be changed. 	18	No.Method avail.
 * @method \Base\DataGridConfig scrollTimeout($integer)
 * 	//(milliseconds)	This controls the timeout handler when scroll is set to 1. 	200	Yes
 * @method \Base\DataGridConfig scrollrows($boolean)
 * 	//	When enabled, selecting a row with setSelection scrolls the grid so that the selected row is visible. This is especially useful when we have a verticall scrolling grid and we use form editing with navigation buttons (next or previous row). On navigating to a hidden row, the grid scrolls so that the selected row becomes visible. 	false	Yes
 * @method \Base\DataGridConfig selarrrow($array)
 * 	//	This options is readonly. Gives the currently selected rows when multiselect is set to true. This is a one-dimensional array and the values in the array correspond to the selected id's in the grid. 	empty array []	No
 * @method \Base\DataGridConfig selrow($string)
 * 	//	This option is readonly. It contains the id of the last selected row. If you sort or use paging, this options is set to null. 	null	No
 * @method \Base\DataGridConfig shrinkToFit($boolean)
 * 	// or<br>
 * integer	This option, if set, defines how the the width of the columns of the grid should be re-calculated, taking into consideration the width of the grid. If this value is true, and the width of the columns is also set, then every column is scaled in proportion to its width. For example, if we define two columns with widths 80 and 120 pixels, but want the grid to have a width of 300 pixels, then the columns will stretch to fit the entire grid, and the extra width assigned to them will depend on the width of the columns themselves and the extra width available. The re-calculation is done as follows: the first column gets the width (300(new width)/200(sum of all widths))*80(first column width) = 120 pixels, and the second column gets the width (300(new width)/200(sum of all widths))*120(second column width) = 180 pixels. Now the widths of the columns sum up to 300 pixels, which is the width of the grid. If the value is false and the value in width option is set, then no re-sizing happens whatsoever. So in this example, if shrinkToFit is set to false, column one will have a width of 80 pixels, column two will have a width of 120 pixels and the grid will retain the width of 300 pixels. If the value of shrinkToFit is an integer, the width is calculated according to it. FIX ME - The effect of using an integer can be elaborated. 	true	No
 * @method \Base\DataGridConfig sortable($boolean)
 * 	//	When set to true, this option allows reordering columns by dragging and dropping them with the mouse. Since this option uses the jQuery UI sortable widget, be sure to load this widget and its related files in the HTML head tag of the page. Also, be sure to select the jQuery UI Addons option under the jQuery UI Addon Methods section while downloading jqGrid if you want to use this facility. Note: The colModel object also has a property called sortable, which specifies if the grid data can be sorted on a particular column or not. 	false	No
 * @method \Base\DataGridConfig sortname($string)
 * 	//	The column according to which the data is to be sorted when it is initially loaded from the server (note that you will have to use datatypes xml or json to load remote data). This parameter is appended to the url. If this value is set and the index (name) matches the name from colModel, then an icon indicating that the grid is sorted according to this column is added to the column header. This icon also indicates the sorting order - descending or ascending (see the parameter sortorder). Also see prmNames. 	empty string	Yes
 * @method \Base\DataGridConfig sortorder($string)
 * 	//	The initial sorting order (ascending or descending) when we fetch data from the server using datatypes xml or json. This parameter is appended to the url - see prnNames. The two allowed values are - asc or desc.	asc	Yes
 * @method \Base\DataGridConfig subGrid($boolean)
 * 	//	If set to true this enables using a sub-grid. If the subGrid option is enabled, an additional column at left side is added to the basic grid. This column contains a 'plus' image which indicates that the user can click on it to expand the row. By default all rows are collapsed. See Subgrid	false	No
 * @method \Base\DataGridConfig subGridOptions($object)
 * 	// 	A set of additional options for the subgrid. For more information and default values see Subgrid. 	see Subgrid 	Yes
 * @method \Base\DataGridConfig subGridModel($array)
 * 	//-[]	This property, which describes the model of the subgrid, has an effect only if the subGrid property is set to true. It is an array in which we describe the column model for the subgrid data. For more information see Subgrid.	empty array	No
 * @method \Base\DataGridConfig subGridType($mixed)
 * 	//	This option allows loading a subgrid as a service. If not set, the datatype parameter of the parent grid is used.	null	Yes
 * @method \Base\DataGridConfig subGridUrl($string)
 * 	//	This option has effect only if the subGrid option is set to true. This option points to the url from which we get the data for the subgrid. jqGrid adds the id of the row to this url as parameter. If there is a need to pass additional parameters, use the params option in subGridModel. See Subgrid	empty string	Yes
 * @method \Base\DataGridConfig subGridWidth($integer)
 * 	//	Defines the width of the sub-grid column if subgrid is enabled.	20	No
 * @method \Base\DataGridConfig toolbar($array)
 * 	//	This option defines the toolbar of the grid. This is an array with two elements in which the first element's value enables the toolbar and the second defines the position relative to the body layer (table data). Possible values are top, bottom, and both. When we set it like toolbar: [true,”both”] two toolbars are created – one on the top of table data and the other at the bottom of the table data. When we have two toolbars, then we create two elements (div). The id of the top bar is constructed by concatenating the string “t_” and the id of the grid, like “t_” + id_of_the_grid and the id of the bottom toolbar is constructed by concatenating the string “tb_” and the id of the grid, like “tb_” + id_of_the grid. In the case where only one toolbar is created, we have the id as “t_” + id_of_the_grid, independent of where this toolbar is located (top or bottom)	[false, ''] 	No
 * @method \Base\DataGridConfig toppager($boolean)
 * 	//	When enabled this option places a pager element at top of the grid, below the caption (if available). If another pager is defined, both can coexist and are kept in sync. The id of the newly created pager is the combination grid_id + “_toppager”. 	false	No
 * @method \Base\DataGridConfig totaltime($integer)
 * 	//	Readonly parameter. It gives the loading time of the records - currently available only when we load xml or json data. The measurement begins when the request is complete and ends when the last row is added. 	0	No
 * @method \Base\DataGridConfig treedatatype($mixed)
 * 	//	Gives the initial datatype (see datatype option). Usually this should not be changed. During the reading process this option is equal to the datatype option. 	null	No
 * @method \Base\DataGridConfig treeGrid($boolean)
 * 	//	Enables (disables) the tree grid format. For more details see Tree Grid	false	No
 * @method \Base\DataGridConfig treeGridModel($string)
 * 	//	Deteremines the method used for the treeGrid. The value can be either nested or adjacency. See Tree Grid	nested	No
 * @method \Base\DataGridConfig treeIcons($array)
 * 	//	This array sets the icons used in the tree grid. The icons should be a valid names from UI theme roller images. The default values are: {plus:'ui-icon-triangle-1-e',minus:'ui-icon-triangle-1-s',leaf:'ui-icon-radio-off'} 		No
 * @method \Base\DataGridConfig treeReader($array)
 * 	//	Extends the colModel defined in the basic grid. The fields described here are appended to end of the colModel array and are hidden. This means that the data returned from the server should have values for these fields. For a full description of all valid values see Tree Grid.		No<br>
 * tree_root_level	numeric	Defines the level where the root element begins when treeGrid is enabled. 	0	No
 * @method \Base\DataGridConfig url($string)
 * 	//	The url of the file that returns the data needed to populate the grid. May be set to clientArray to manualy post data to server; see Inline Editing. 	null	Yes
 * @method \Base\DataGridConfig userData($array)
 * 	//	This array contains custom information from the request. Can be used at any time. 	empty array	No
 * @method \Base\DataGridConfig userDataOnFooter($boolean)
 * 	//	When set to true we directly place the user data array userData in the footer. The rules are as follows: If the userData array contains a name which matches any name defined in colModel, then the value is placed in that column. If there are no such values nothing is placed. Note that if this option is used we use the current formatter options (if available) for that column.	false	Yes
 * @method \Base\DataGridConfig viewrecords($boolean)
 * 	//	If true, jqGrid displays the beginning and ending record number in the grid, out of the total number of records in the query. This information is shown in the pager bar (bottom right by default)in this format: “View X to Y out of Z”. If this value is true, there are other parameters that can be adjusted, including emptyrecords and recordtext. 	false	No
 * @method \Base\DataGridConfig viewsortcols($array)
 * 	//	The purpose of this parameter is to define a different look and behavior for the sorting icons (up/down arrows) that appear in the column headers. This parameter is an array with the following default options viewsortcols : [false,'vertical',true]. The first parameter determines if sorting icons should be visible on all the columns that have the sortable property set to true. Setting this value to true could be useful if you want to indicate to the user that (s)he can sort on that particular column. The default of false sets the icon to be visible only on the column on which that data has been last sorted. Setting this parameter to true causes all icons in all sortable columns to be visible.<br>
 * The second parameter determines how icons should be placed - vertical means that the sorting arrows are one under the other. 'horizontal' means that the arrows should be next to one another.<br>
 * The third parameter determines the click functionality. If set to true the data is sorted if the user clicks anywhere in the column's header, not only the icons. If set to false the data is sorted only when the sorting icons in the headers are clicked.<br>
 * Important: If you are setting the third element to false, make sure that you set the first element to true; if you don't, the icons will not be visible and the user will not know where to click to be able to sort since clicking just anywhere in the header will not guarantee a sort. 	[false,'vertical',true]	No
 * @method \Base\DataGridConfig width($number)
 * 	//	If this option is not set, the width of the grid is the sum of the widths of the columns defined in the colModel (in pixels). If this option is set, the initial width of each column is set according to the value of the shrinkToFit option. 	none	No. Method avail.
 * @method \Base\DataGridConfig xmlReader($array)
 * 	//	An array which describes the structure of the expected xml data. For a full description refer to Retrieving Data in XML Format. 		No
 *
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * ------ EVENTS
 * @method \Base\DataGridConfig afterInsertRow($jsfun)
 * //$jsfun:"##JSFUN##function(rowid, rowdata, rowelem ){}##JSFUN##"<br>
 * This event fires after every inserted row.<br>
 * rowid is the id of the inserted row<br>
 * rowdata is an array of the data to be inserted into the row. This is array of type name: value, where the name is a name from colModel<br>
 * rowelem is the element from the response. If the data is xml this is the xml element of the row; if the data is json this is array containing all the data for the row<br>
 * Note: this event does not fire if gridview option is set to true
 * @method \Base\DataGridConfig beforeProcessing($jsfun)
 * //$jsfun:"##JSFUN##function(data, status, xhr){}##JSFUN##"<br>
 * This event fire before proccesing the data from the server. Note that the data is formatted depended on the value of the datatype parameter - i.e if the datatype is 'json' for example the data is JavaScript object
 * @method \Base\DataGridConfig beforeRequest($jsfun)
 * //$jsfun:"##JSFUN##function(){}##JSFUN##"<br>
 * This event fire before requesting any data. Also does not fire if datatype is function. If the event return false the request is not made to the server
 * @method \Base\DataGridConfig beforeSelectRow($jsfun)
 * //$jsfun:"##JSFUN##function(rowid, e){}##JSFUN##"<br>
 * This event fire when the user click on the row, but before select them.<br>
 * rowid is the id of the row.<br>
 * e is the event object<br>
 * This event should return boolean true or false. If the event return true the selection is done. If the event return false the row is not selected and any other action if defined does not occur.
 * @method \Base\DataGridConfig gridComplete($jsfun)
 * //$jsfun:"##JSFUN##function(){}##JSFUN##"<br>
 * This fires after all the data is loaded into the grid and all other processes are complete. Also the event fires independent from the datatype parameter and after sorting paging and etc.
 * @method \Base\DataGridConfig loadBeforeSend($jsfun)
 * //$jsfun:"##JSFUN##function(xhr, settings){}##JSFUN##"<br>
 * A pre-callback to modify the XMLHttpRequest object (xhr) before it is sent. Use this to set custom headers etc. Returning false will cancel the request.
 * @method \Base\DataGridConfig loadComplete($jsfun)
 * //$jsfun:"##JSFUN##function(data){}##JSFUN##"<br>
 * This event is executed immediately after every server request.<br>
 * data Data from the response depending on datatype grid parameter
 * @method \Base\DataGridConfig loadError($jsfun)
 * //$jsfun:"##JSFUN##function(xhr, status, error){}##JSFUN##"<br>
 * A function to be called if the request fails. The function gets passed three arguments: The XMLHttpRequest object (xhr), a string describing the type of error (status) that occurred and an optional exception object (error), if one occurred.
 * @method \Base\DataGridConfig onCellSelect($jsfun)
 * //$jsfun:"##JSFUN##function(rowid, iCol, cellcontent, e){}##JSFUN##"<br>
 * Fires when we click on particular cell in the grid.<br>
 * rowid is the id of the row<br>
 * iCol is the index of the cell,<br>
 * cellcontent is the content of the cell,<br>
 * e is the event object element where we click.<br>
 * (Note that this available when we not use cell editing module and is disabled when using cell editing).
 * @method \Base\DataGridConfig ondblClickRow($jsfun)
 * //$jsfun:"##JSFUN##function(rowid, iRow, iCol, e){}##JSFUN##"<br>
 * Raised immediately after row was double clicked.<br>
 * rowid is the id of the row,<br>
 * iRow is the index of the row (do not mix this with the rowid),<br>
 * iCol is the index of the cell.<br>
 * e is the event object
 * @method \Base\DataGridConfig onHeaderClick($jsfun)
 * //$jsfun:"##JSFUN##function(gridstate){}##JSFUN##"<br>
 * Fire after clicking to hide or show grid (hidegrid:true);<br>
 * gridstate is the state of the grid - can have two values - visible or hidden
 * @method \Base\DataGridConfig onPaging($jsfun)
 * //$jsfun:"##JSFUN##function(pgButton){}##JSFUN##"<br>
 * This event fires after click on [page button] and before populating the data. Also works when the user enters a new page number in the page input box (and presses [Enter]) and when the number of requested records is changed via the select box. To this event we pass only one parameter pgButton See pager.<br>
 * If this event return 'stop' the processing is stopped and you can define your own custom paging
 * @method \Base\DataGridConfig onRightClickRow($jsfun)
 * //$jsfun:"##JSFUN##function(rowid, iRow, iCol, e){}##JSFUN##"<br>
 * Raised immediately after row was right clicked.<br>
 * rowid is the id of the row,<br>
 * iRow is the index of the row (do not mix this with the rowid),<br>
 * iCol is the index of the cell.<br>
 * e is the event object.<br>
 * Note - this event does not work in Opera browsers, since Opera does not support oncontextmenu event
 * @method \Base\DataGridConfig onSelectAll($jsfun)
 * //$jsfun:"##JSFUN##function(aRowids, status ){}##JSFUN##"<br>
 * This event fires when multiselect option is true and you click on the header checkbox.<br>
 * aRowids array of the selected rows (rowid's).<br>
 * status - boolean variable determining the status of the header check box - true if checked, false if not checked.<br>
 * Note that the aRowids alway contain the ids when header checkbox is checked or unchecked.
 * @method \Base\DataGridConfig onSelectRow($jsfun)
 * //$jsfun:"##JSFUN##function(rowid, status, e){}##JSFUN##"<br>
 * Raised immediately after row was clicked.<br>
 * rowid is the id of the row,<br>
 * status is the status of the selection,<br>
 * e is the event object. Can be used when multiselect is set to true. true if the row is selected, false if the row is deselected.
 * @method \Base\DataGridConfig onSortCol($jsfun)
 * //$jsfun:"##JSFUN##function(index, iCol, sortorder){}##JSFUN##"<br>
 * Raised immediately after sortable column was clicked and before sorting the data.<br>
 * index is the index name from colModel,<br>
 * iCol is the index of column,<br>
 * sortorder is the new sorting order - can be 'asc' or 'desc'.<br>
 * If this event return 'stop' the sort processing is stopped and you can define your own custom sorting
 * @method \Base\DataGridConfig resizeStart($jsfun)
 * //$jsfun:"##JSFUN##function(event, index){}##JSFUN##"<br>
 * Event which is called when we start resize a column. event is the event object, index is the index of the column in colModel.
 * @method \Base\DataGridConfig resizeStop($jsfun)
 * //$jsfun:"##JSFUN##function(newwidth, index){}##JSFUN##"<br>
 * Event which is called after the column is resized. newwidth is the is the new width of the column , index is the index of the column in colModel.
 * @method \Base\DataGridConfig serializeGridData($jsfun)
 * //$jsfun:"##JSFUN##function(postData){}##JSFUN##"<br>
 * If set this event can serialize the data passed to the ajax request. The function should return the serialized data. This event can be used when a custom data should be passed to the server - e.g - JSON string, XML string and etc.<br>
 * To this event we pass the postData array.
 */
class DataGridConfig extends ConfigArray {
	
}

///**
// * @method \Base\DataGridEditOptions 
// */
//class DataGridEditOptions extends ConfigArray {
//public function value($arr) {
//	
//}
//}
