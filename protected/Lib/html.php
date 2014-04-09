<?php

use \Tools as T;
use \Consts as C;

//use \Components as Com;
//lower case because it`s easier to use in markup
/**
 * Tondarweb markup center
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 2
 * @copyright (c) Abbas Ali Hashemian
 * @access public
 */
final class html {

	/**
	 * @param str $ID
	 * @param mixed $mixedContent
	 * @return \Base\Container
	 */
	public static function Container($ID = NULL, $mixedContent = NULL) {
		return new \Base\Container($ID, $mixedContent);
	}

	/**
	 * @param str $ID
	 * @param str $Theme
	 * @return \Base\DataGrid 
	 */
	public static function DataGrid($ID, $Theme = \Conf::jQTheme) {
		return new Base\DataGrid($ID, $Theme);
	}

	/**
	 * @param str $ID
	 * @param str $Theme
	 * @return \Base\DataGrid 
	 */
	public static function DataGrid_Ready1($ID, $strTranslationModule = NULL, $strTranslationCat = NULL, $Theme = \Conf::jQTheme) {
		return \html::DataGrid($ID, $Theme)
						->Resources(\Base\DataGrid::$arrGrpahicalButtonsResource, $strTranslationModule, $strTranslationCat)
						->SetFilterBar(array('searchOnEnter' => false))
						->SetNavigator(array(
							'search' => true
							, 'edit' => true
							, 'del' => false
							, 'saveall' => true
							, 'add' => true
						))
						->Resizable()
						->SetDblClickEdit()
						->setTableClasses('MidAlign')
						->Options(
								\html::DataGridConfig()
								->autowidth(true)
								->multiselect(true)
								->cmTemplate(array(
									'align' => 'center'
									, 'title' => false
									, 'search' => true
									, 'editable' => false
									, 'sortable' => true
									, 'editoptions' => array('class' => 'CenterAlign')
								))
		);
	}

	/**
	 * @param arr $arrInitialArray
	 * @return \Base\DataGridColumn 
	 */
	public static function DataGridColumn($arrInitialArray = NULL) {
		return new \Base\DataGridColumn($arrInitialArray);
	}

	/**
	 * @param arr $arrInitialArray
	 * @return \Base\DataGridConfig
	 */
	public static function DataGridConfig($arrInitialArray = NULL) {
		return new \Base\DataGridConfig($arrInitialArray);
	}

	public static function PutInATitleTag($Value, $Title = NULL, $Tag = 'div') {
		return "<$Tag title='" . ($Title ? $Title : $Value) . "'>$Value</$Tag>";
	}

#----------------- Form -----------------#

	const AutoComplete_GetQueryString_ParamName = 'term';

	/**
	 * WARNING! load Assets_Prompt before creating an auto complete because of jquery.ui.draggable of prompts
	 * NOTICE! for DT and fnc if dr be a string value it causes a non-link separator
	 * @param mixed $mixedSource //fnc($term) | strSQL | arrDT
	 * @param mixed $ValueField str/fnc($dr)
	 * @param mixed $LabelField str/fnc($dr)
	 * @param arr $arrDBParams
	 * @param bool $Multi
	 * @param bool $Ajax
	 * @param str $Target_jqSelector
	 * @param int $MinLen
	 * @param str $ValidationRegexp
	 * @param str $KMGFieldName
	 * @param str $Theme
	 * @return string
	 */
//	function jqUI_AutoComplete(
//	$ID_txtField
//	, $mixedSource
//	, $ValueField
//	, $LabelField = NULL
//	, $arrDBParams = NULL
//	, $Multi = false
//	, $Ajax = false
//	, $Target_jqSelector = NULL
//	, $MinLen = 1
//	, $ValidationRegexp = NULL
//	, $KMGFieldName = 'UsageCount'
//	, $Theme = \Conf::jQTheme
//	) {
//		\html::jqUI_AutoComplete_Load($Theme);
//		if (!$mixedSource || (!is_array($mixedSource) && !is_string($mixedSource) && !is_callable($mixedSource)))
//			throw new \Err(__METHOD__, 'Invalid mixedSource has been passed in.', func_get_args());
//		$KW = "AutoComplete_{$ID_txtField}";
//		if (!$arrDBParams || !is_array($arrDBParams))
//			$arrDBParams = array();
//		$fncMakeSource = function($FilterTerm = NULL)use($mixedSource, $arrDBParams, $ValueField, $LabelField, $KMGFieldName) {
//			$SourceIsDataTable = false;
//			if (is_string($mixedSource))
//				$mixedSource = T\DB::GetTable($mixedSource, array_merge($arrDBParams, array('term' => isset($FilterTerm) ? "%$FilterTerm%" : '%')));
//			elseif (is_callable($mixedSource))
//				$mixedSource = $mixedSource($FilterTerm);
//			else
//				$SourceIsDataTable = true;
//			if ($mixedSource) {
//				$mixedSource = array_map(function($dr)use($ValueField, $LabelField, $KMGFieldName) {
//					$arrResult = array();
//					if (!is_array($dr)) {
//						$arrResult['value'] = '';
//						$arrResult['label'] = "<div class='__uiausep'>$dr</div>";
//					} else {
//						if (is_string($ValueField))
//							$arrResult['value'] = T\DB::DRLabelMaker($dr, $ValueField);
//						elseif (is_callable($ValueField))
//							$arrResult['value'] = $ValueField($dr);
//						if ($LabelField) {
//							if (is_string($LabelField))
//								$arrResult['label'] = T\DB::DRLabelMaker($dr, $LabelField, $KMGFieldName);
//							elseif (is_callable($LabelField))
//								$arrResult['label'] = $LabelField($dr);
//						}
//					}
//					return $arrResult;
//				}, $mixedSource);
//			}
//			if ($SourceIsDataTable)
//				$mixedSource = T\DB::Filter($mixedSource, "return stripos(\$dr['value'], :term)!==false", array('term' => $FilterTerm));
//			return $mixedSource;
//		};
//		if ($Ajax) {
//			$QSTPName = self::AutoComplete_GetQueryString_ParamName;
//			\Output::AddIn_AjaxOutput(function()use($fncMakeSource, $ValidationRegexp, $QSTPName) {
//				$FilterTerm = \GPCS::GET($QSTPName);
//				if ($ValidationRegexp && !preg_match($ValidationRegexp, $FilterTerm))
//					return;
//				echo json_encode($fncMakeSource($FilterTerm));
//			}, $KW, NULL, $KW);
//			$URL = str_replace(array('"', "'"), array('\"', "\'"), T\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], \Output::AjaxKeyword_PostParamName . "=$KW"));
//			return "<script>MyAutoCompleteFNCs.push(function(){MyAutoComplete($('#{$ID_txtField}'),{source:'$URL'" . ($Target_jqSelector ? ",appendTo:'$Target_jqSelector'" : "") . "}," . ($Multi ? 1 : 0) . ",1,$MinLen)})</script>";
//		} else {
//			return "<script>MyAutoCompleteFNCs.push(function(){MyAutoComplete($('#{$ID_txtField}'),"
//					. "{source:" . json_encode($fncMakeSource()) . ($Target_jqSelector ? ",appendTo:'$Target_jqSelector'" : "") . "}"
//					. ',' . ($Multi ? 1 : 0) . ",0,$MinLen)})</script>";
//		}
//	}

	public static function FieldContainer($Field, $Label = null, $ErrHolder = null, $ExtAttrs = NULL, $ExtClasses = "") {
		$IsChkRdo = preg_match('/^.*<input[^\n]*[\s\t\n]type=[\"\'](checkbox|radio)[\'\"].+$/is', $Field);
		if (!$IsChkRdo)
			$IsTxt = preg_match('/^.*<input[^\n]*[\s\t\n]type=[\"\'](text|password)[\'\"].+$/is', $Field);

		return "<div class='"
				. ($IsChkRdo ? "ChkRdo" : "Fld" . ($IsTxt ? " Txt" : ""))
				. ($ExtClasses ? " $ExtClasses" : "") . "'"
				. ($ExtAttrs ? " $ExtAttrs" : "") . ">"
				. ($IsChkRdo ? "$Field $Label" : "$Label : $Field") . " $ErrHolder"
				. "</div>";
	}

	public static function CaptchaFieldContainer($Image, $Field, $Label = null, $ErrHolder = null, $ExtAttrs = NULL, $ExtClasses = "Captcha") {
		return self::FieldContainer($Field . ' ' . $Image, $Label, $ErrHolder, $ExtAttrs, $ExtClasses);
	}

	public static function ButtonContainer($Button) {
		return "<a href='javascript:;' class='Btn'>$Button<div></div></a>";
	}

	/**
	 * $form->widget('CCaptcha')
	 * @param CFormModel $form essentially pass the form when you use CActiveForm
	 */
	static function CaptchaImage(CActiveForm $form = null, $className = null, $properties = array(), $captureOutput = true) {
		if (!$className)
			$className = 'CCaptcha';
		$properties = \Tools\Basics::Merge_MultiDimension(
						array(
					'buttonLabel' => '',
					'buttonOptions' => array('title' => \t2::General('Refresh captcha'), 'rel' => self::AjaxExcept),
						)
						, $properties);
		return $form->widget($className, $properties, $captureOutput);
//		$Result = $form->widget($className, $properties, $captureOutput);
//		if ($captureOutput)
//			$Result = "<div>$Result</div>";
//		return $Result;
	}

	/**
	 * same as \CHtml::activeTextField but if you provide the $form with a valid CActiveForm object you will patch the field to the form(client validator and ...)
	 * @param \CModel $model
	 * @param \CActiveForm $ActiveForm
	 * @param string $attribute
	 * @param array $htmlOptions
	 * @return string
	 */
	static function activeTextField($model, \CActiveForm $ActiveForm = null, $attribute, $htmlOptions = array()) {
		return $ActiveForm ?
				$ActiveForm->textField($model, $attribute, $htmlOptions) :
				\CHtml::activeTextField($model, $attribute, $htmlOptions);
	}

	/**
	 * same as \CHtml::activeDropDownList but if you provide the $form with a valid CActiveForm object you will patch the field to the form(client validator and ...)
	 * @param \CModel $model
	 * @param \CActiveForm $ActiveForm
	 * @param string $attribute
	 * @param array $data
	 * @param array $htmlOptions
	 * @return string
	 */
	static function activeDropDownList($model, \CActiveForm $ActiveForm = null, $attribute, $data, $htmlOptions = array()) {
		return $ActiveForm ?
				$ActiveForm->dropDownList($model, $attribute, $data, $htmlOptions) :
				\CHtml::activeDropDownList($model, $attribute, $data, $htmlOptions);
	}

	/**
	 * same as \CHtml::activeLabelEx but if you provide the $form with a valid CActiveForm object you will patch the field to the form(client validator and ...)
	 * @param \CModel $model
	 * @param \CActiveForm $ActiveForm
	 * @param string $attribute
	 * @param array $htmlOptions
	 * @return string
	 */
	static function activeLabelEx($model, \CActiveForm $ActiveForm = null, $attribute, $htmlOptions = array()) {
		return $ActiveForm ?
				$ActiveForm->labelEx($model, $attribute, $htmlOptions) :
				\CHtml::activeLabelEx($model, $attribute, $htmlOptions);
	}

	/**
	 * same as \CHtml::error but if you provide the $form with a valid CActiveForm object you will patch the field to the form(client validator and ...)
	 * @param \CModel $model
	 * @param \CActiveForm $ActiveForm
	 * @param string $attribute
	 * @param array $htmlOptions
	 * @return string
	 */
	static function error($model, \CActiveForm $ActiveForm = null, $attribute, $htmlOptions = array(), $enableAjaxValidation = false, $enableClientValidation = true) {
		return $ActiveForm ?
				$ActiveForm->error($model, $attribute, $htmlOptions, $enableAjaxValidation, $enableClientValidation) :
				\CHtml::error($model, $attribute, $htmlOptions);
	}

	/**
	 * upgraded \CHtml::activeDropDownList to be combobox
	 * Generates a drop down list for a model attribute.
	 * If the attribute has input error, the input field's CSS class will
	 * be appended with {@link errorCss}.
	 * @param CModel $model the data model
	 * @param string $attribute the attribute
	 * @param array $data data for generating the list options (value=>display)
	 * You may use {@link listData} to generate this data.
	 * Please refer to {@link listOptions} on how this data is used to generate the list options.
	 * Note, the values and labels will be automatically HTML-encoded by this method.
	 * @param array $htmlOptions additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are recognized. See {@link clientChange} and {@link tag} for more details.
	 * In addition, the following options are also supported:
	 * <ul>
	 * <li>encode: boolean, specifies whether to encode the values. Defaults to true.</li>
	 * <li>prompt: string, specifies the prompt text shown as the first list option. Its value is empty.  Note, the prompt text will NOT be HTML-encoded.</li>
	 * <li>empty: string, specifies the text corresponding to empty selection. Its value is empty.
	 * The 'empty' option can also be an array of value-label pairs.
	 * Each pair will be used to render a list option at the beginning. Note, the text label will NOT be HTML-encoded.</li>
	 * <li>options: array, specifies additional attributes for each OPTION tag.
	 *     The array keys must be the option values, and the array values are the extra
	 *     OPTION tag attributes in the name-value pairs. For example,
	 * <pre>
	 *     array(
	 *         'value1'=>array('disabled'=>true,'label'=>'value 1'),
	 *         'value2'=>array('label'=>'value 2'),
	 *     );
	 * </pre>
	 * </li>
	 * </ul>
	 * Since 1.1.13, a special option named 'unselectValue' is available. It can be used to set the value
	 * that will be returned when no option is selected in multiple mode. When set, a hidden field is
	 * rendered so that if no option is selected in multiple mode, we can still obtain the posted
	 * unselect value. If 'unselectValue' is not set or set to NULL, the hidden field will not be rendered.
	 * @param string $strUserInputJQSelector	(optional) same as $arrUserInputTextField but only the jQuery selector rerfers to your TextField. You can't use both and the jQSelector has higher priority
	 * @param string $txtUserInputField	(optional) same as $arrUserInputTextField but the ready text field html code
	 * @param array $arrUserInputTextField	(optional) if had been set then the user can put new entries. The combobox will have an "other" item which shows an input text field
	 * the array can contain 2 items : 'attribute'=>$attributeOfTheModel, 'htmlOptions'=>$htmlOptions
	 * @return string the generated drop down list
	 * @see clientChange
	 * @see listData
	 */
	public static function activeComboBox($model, \CActiveForm $ActiveForm = null
	, $attribute, $data, $htmlOptions = array()
	, $strUserInputJQSelector = null
	, $txtUserInputField = null
	, $arrUserInputTextField = null) {
		if (!$strUserInputJQSelector && !$txtUserInputField && $arrUserInputTextField && isset($arrUserInputTextField['attribute']))
			$txtUserInputField = \html::activeTextField(
							$model
							, $ActiveForm
							, $arrUserInputTextField['attribute']
							, isset($arrUserInputTextField['htmlOptions']) ? $arrUserInputTextField['htmlOptions'] : array());
		\CHtml::resolveNameID($model, $attribute, $htmlOptions);
		return \html::activeDropDownList($model, $ActiveForm, $attribute, $data, $htmlOptions)
				. "\n" . self::ComboBoxScript(
						"#{$htmlOptions['id']}"
						, ($strUserInputJQSelector ? : ($txtUserInputField? : NULL))
						, isset($strUserInputJQSelector)
		);
	}

	const Combobox_NoSearchRel = 'NoSearchCombobox';

	public static function ComboBoxScript($jQSelector_selectTag, $strUserInputText = NULL, $UserInputIsAJQuerySelector = false) {
		self::jqUI_Combobox_Load();
		return "<script>"
				. "_t.RunScriptAfterLoad('MyJuiAutoComplete/MyComboBox', function(){"
				. "\$('$jQSelector_selectTag')"
				. ($strUserInputText ? ".data(" . ($UserInputIsAJQuerySelector ? "'UserInputJQSelector'" : "'UserInputTag'") . ", '" . addslashes($strUserInputText) . "')" : "")
				. ".combobox()"
				. "})"
				. "</script>";
	}

	/**
	 * @param str $Text		will get escapced for js
	 * @param str $Title	will get escapced for js
	 * @return string
	 */
	static function PostbackConfirm_OnClick($Text = '', $Title = 'Are you sure?') {
		self::LoadJS('Assets_Prompt/jquery.alerts');
		$Text = addslashes(t2::General($Text));
		$Title = addslashes(t2::General($Text));
		return "return PostBack.jConfirm(this, '$Text', '$Title')";
	}

	const OnceClick = 'OnceClick';

#----------------- Ajax Tools -----------------#

	public static function ErrMsg_Exit($Msg, $HttpHeader = C\Header::BadRequest, $HttpCode = C\Header::BadRequestCode) {
		T\HTTP::Header($HttpHeader, true, $HttpCode);
		echo $Msg;
		\Yii::app()->end();
	}

	private static function AjaxMaker($RelKW, $DefaultButton_jQSelector = NULL, $SpecialKW = NULL, $strAjaxPostParams = NULL, $AjaxURL = null, $AjaxTarget_jQSelector = null) {
		return " $RelKW" #
				. ( $AjaxTarget_jQSelector ? ':' . $AjaxTarget_jQSelector : '' ) #
				. ( $AjaxURL ? self::AsyncURL($AjaxURL) : '' )
				. ($DefaultButton_jQSelector ? self::DefaultButton($DefaultButton_jQSelector) : '')
				. ($SpecialKW ? ' ' . \Output::AjaxKeyword_PostParamName . ":$SpecialKW " : '')
				. ($strAjaxPostParams ? " AjaxPostParams:$strAjaxPostParams " : '')
				. ' ';
	}

	public static function AsyncURL($URL) {
		return " AsyncURL:$URL";
	}

	public static function AsyncParams($Params) {
		return " AjaxPostParams:$Params ";
	}

	/**
	 * Tondarweb AjaxPanel can be a div or any type of html elements to be as a container for links or buttons
	 * it causes the submit button to post the form fields through ajax
	 * also all the links inside this ajax panel will work ajaxically
	 * rel="<?=\html::AjaxPanel(...)?>"
	 * use \html::AjaxExcempt to exclude an element or link from ajax submission
	 * @param str $SpecialKW //we'll use it again for \Output::AddIn_AjaxOutput($func, $strKW = null)
	 * @param string $strAjaxPostParams	//P1=value;P2=value
	 * @return a str for rel attr
	 */
	public static function AjaxPanel($DefaultButton_jQSelector = NULL, $SpecialKW = NULL, $strAjaxPostParams = NULL, $AjaxURL = null, $AjaxTarget_jQSelector = null) {
		return self::AjaxMaker('AjaxPanel', $DefaultButton_jQSelector, $SpecialKW, $strAjaxPostParams, $AjaxURL, $AjaxTarget_jQSelector);
	}

	/**
	 * Same as {@see \html::AjaxPanel} just its arguments(function parameters) are intended for an ajax link panel
	 * @param string $AjaxTarget_jQSelector //#divContent:insert || .divASD:replace
	 * @param string $SpecialKW	//ajax postal special keyword
	 * @param string $strAjaxPostParams	//P1=value;P2=value
	 * @return string //string value to be placed in a "rel" attribute
	 */
	public static function AjaxLinks($AjaxTarget_jQSelector = null, $SpecialKW = NULL, $strAjaxPostParams = NULL) {
		return self::AjaxMaker('AjaxPanel', NULL, $SpecialKW, $strAjaxPostParams, NUlL, $AjaxTarget_jQSelector);
	}

	/**
	 * Tondarweb AjaxElement can be a button or a submit button or a link.
	 * rel="<?=\html::AjaxElement(...)?>"
	 * @param str $SpecialKW //we'll use it again for \Output::AddIn_AjaxOutput($func, $strKW = null)
	 * @param string $strAjaxPostParams	//P1=value;P2=value
	 * @return a str for rel attr
	 */
	public static function AjaxElement($AjaxTarget_jQSelector = null, $SpecialKW = NULL, $strAjaxPostParams = NULL, $AjaxURL = null) {
		return self::AjaxMaker('AjaxElement', NULL, $SpecialKW, $strAjaxPostParams, $AjaxURL, $AjaxTarget_jQSelector);
	}

//	public static function GetAsync_URLHash($AjaxTarget_jQSelector, $strAjaxPostParams) {
//		return "##ASYNCHASH#$strAjaxPostParams->$AjaxTarget_jQSelector";
//	}

	const AjaxExcept = " AjaxExcept ";
	const SimpleAjaxPanel = " SimpleAjaxPanel ";

	/**
	 * Change URL using HTML5 pushState js dom method(for ajax communications)
	 * @param type $URL
	 * @param type $Return
	 * @param type $InlineUniqueKW
	 * @param mixed $AjaxKW	//put NULL instead of false to activate ajax
	 * @param type $AjaxContentUKW
	 * @return type
	 */
	public static function PushStateScript($URL = NULL, $Return = false, $InlineUniqueKW = null, $AjaxKW = '', $AjaxContentUKW = NULL) {
		if (!$InlineUniqueKW)
			$InlineUniqueKW = 'html_PushStateScript';
		if (!$URL)
			$URL = $_SERVER['REQUEST_URI'];
		$URL = addslashes($URL);
		$Script = "_t.PushState('$URL')";
		if ($Return)
			return "<script type='text/javascript'>$Script</script>";
		else
			self::InlineJS($Script, $InlineUniqueKW, $AjaxKW, $AjaxContentUKW);
	}

	static function AjaxPageTitle($Title, $Return = false, $InlineUniqueKW = null, $AjaxKW = '', $AjaxContentUKW = NULL) {
		$Title = addslashes($Title);
		$Script = "_t.DocTitle('$Title')";
		if ($Return)
			return "<script type='text/javascript'>$Script</script>";
		else
			self::InlineJS($Script, $InlineUniqueKW, $AjaxKW, $AjaxContentUKW);
	}

	#----------------- Other -----------------#

	static function AltRow($Reset = false, $JustClassName = false, $JustBool = false) {
		static $IsAltRow = false;
		if ($Reset)
			$IsAltRow = false;
		$Result = $IsAltRow;
		$IsAltRow = $IsAltRow ? false : true;

		$Class = $JustClassName ? ' AltRow ' : ' class="AltRow" ';

		return $JustBool ? $Result : ($Result ? $Class : '');
	}

	static function DefaultButton($DefaultButton_jQSelector) {
		return " DefaultButton:$DefaultButton_jQSelector ";
	}

//
//	const SubmitValidator = ' validSubmit ';
//	const Focus = ' FocusMe ';
//
	public static function PutInContainer(&$strobjContent = "", $Prefix = "<div>", $Postfix = '</div>', $OnlyReturnMsg = true) {
		if (is_string($strobjContent)) {
			$strobjContent = $Prefix . $strobjContent . $Postfix;
		} elseif (is_a($strobjContent, '\Base\Container')) {
			$strobjContent->AddContentAt($Prefix, 0);
			$strobjContent->AddContentAt($Postfix, -1);
		}
		if ($OnlyReturnMsg)
			return $strobjContent;
		else {
			echo $strobjContent;
			return $strobjContent;
		}
	}
//
//	static function GetParentalPath_ByDataTable(
//	$dtPath
//	, $LinkTemplate
//	, $EndNodeTemplate
//	, $HasRoot = true
//	, $Separator = NULL
//	, $JoinIDField = NULL
//	, $arrMixFields = NULL
//	) {
//		$Path = array();
//		if ($HasRoot)
//			$Path[] = $LinkTemplate(NULL);
//		$drLastPathItem = null;
//		if ($dtPath) {
//			if (is_null($Separator))
//				$Separator = '{{@res_PathSeparator}}';
//			if (is_null($JoinIDField))
//				$JoinIDField = 'ID';
//			if (is_null($arrMixFields))
//				$arrMixFields = array('Title' => ' | ');
//			$dtPath = T\DB::ImplodeRows($dtPath, $JoinIDField, $arrMixFields);
//
//			$drLastPathItem = array_pop($dtPath);
//			foreach ($dtPath as $drPath) {
//				$Path[] = $LinkTemplate($drPath);
//			}
//		}
//		$Path[] = $EndNodeTemplate($drLastPathItem);
//		return implode($Separator, $Path);
//	}
//
//	static function GetParentalPath(
//	$DBTableName
//	, $DBFields
//	, $CatID
//	, $LinkTemplate
//	, $EndNodeTemplate
//	, $HasRoot = true
//	, $Separator = NULL
//	, $JoinIDField = NULL
//	, $arrMixFields = NULL
//	) {
//		$dtPath = T\DB::GetPathDataTable($DBTableName, $DBFields, $CatID);
//		return self::GetParentalPath_ByDataTable($dtPath, $LinkTemplate, $EndNodeTemplate, $HasRoot, $Separator, $JoinIDField, $arrMixFields);
//	}
//
	#----------------- Tag making base -----------------#
	private static $UniqueQueue = array();

	private static function CheckUnique($Key) {
		return !isset(self::$UniqueQueue[$Key]);
	}

	private static function AddUnique($Key) {
		if (!self::CheckUnique($Key))
			return false;
		return self::$UniqueQueue[$Key] = true;
	}

	private static function UniqueHandler($KW = null, $Markup, $boolReturnMarkup = true, $boolUnique = true) {
		if ($KW && !self::AddUnique($KW) && $boolUnique)
			return '';
		if ($boolReturnMarkup)
			return $Markup;
		echo $Markup;
		return null;
	}

	#----------------- head tags -----------------#

	private static $CSS_RootDir = '/_css';
	private static $CSS_Ext = '.css';

	private static function GetCSSURL($URL) {
		$AbsoluteURI_Sign = '*';
		if ($URL[0] != $AbsoluteURI_Sign)
			$URL = self::$CSS_RootDir . "/$URL" . self::$CSS_Ext;
		else
			$URL = substr($URL, 1);
		return $URL;
	}

	/**
	 * @param string|array $mixedHREF
	 * @param type $boolReturnMarkup
	 * @param type $boolUnique
	 * @param boolean $Use_ClientLoadCSS
	 * @return string
	 */
	public static function CSS_LinkTag($mixedHREF, $boolReturnMarkup = true, $boolUnique = true, $Use_ClientLoadCSS = false) {
		if (T\HTTP::IsAsync())
			$Use_ClientLoadCSS = true;
		if (!is_array($mixedHREF))
			$mixedHREF = self::GetCSSURL($mixedHREF);
		if ($Use_ClientLoadCSS) {
			if (is_array($mixedHREF)) {
				$markup = array();
				foreach ($mixedHREF as $HREF) {
					$HREF = self::GetCSSURL($HREF);
					$markup[] = self::UniqueHandler($HREF, "_t.LoadCSS('*$HREF')", true, $boolUnique);
				}
				$markup = array_filter($markup);
				$markup = implode("\n", $markup);
				$markup = "\n<script type='text/javascript'>\n$markup</script>";
			} else
				$markup = self::UniqueHandler($mixedHREF, "\n<script type='text/javascript'>_t.LoadCSS('*$mixedHREF'" . ($boolUnique ? '' : ', true') . ")</script>", $boolReturnMarkup, $boolUnique);
		} else
			$markup = self::UniqueHandler($mixedHREF, "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"$mixedHREF\" />", $boolReturnMarkup, $boolUnique);
		return $markup;
	}

	public static $cntIncludedCSS = NULL;

	public static function CSS_Include($mixedMarkup, $Params = NULL, $ContentUniqueKW = NULL) {
		if (!self::$cntIncludedCSS)
			self::$cntIncludedCSS = self::Container("_cntIncludedCSS");
		self::$cntIncludedCSS->AddContent($mixedMarkup, $Params, $ContentUniqueKW);
	}

	private static $CSSQueue = array('INLINE' => array(), 'FILES' => array());

	/**
	 * puts the css to load in main layout and in normal ajax requests
	 * The css will be loaded uniquely if you use $Use_ClientLoadCSS as true and $boolUnique as true
	 * @param string $href	//this will be relative to the css root (/htdocs/_css)
	 * 	"mycssCat/cssFile"	//if you use the relative path you can omit the .css extension
	 * 	but to load absolute css files uniquely or repetitively just use an asterisk at first and use the css extension:<br/>
	 * 	"* /myabsolutepath/to/file.css"
	 * @param bool $boolUnique		//whether to load uniquely or not
	 * @param bool $Use_ClientLoadCSS
	 * 	//if you avoid using client css loader then the unique load can be applied only on the server codes not in the
	 * 	client codes so if you load a css file during an ajax request it may get duplicated in the client
	 * @param string $AjaxKW	//using ajax kw you can explicitely define what ajax request you want to load this file during it.
	 */
	public static function LoadCSS($href, $boolUnique = true, $Use_ClientLoadCSS = true, $AjaxKW = NULL) {
		if (!$Use_ClientLoadCSS || (!T\HTTP::IsAsync() && !\Output::$IsRenderPassed))
			self::CSS_Include(self::CSS_LinkTag($href, true, $boolUnique, false));
		else if (($boolUnique && $Use_ClientLoadCSS) || !$boolUnique) {
			$Queue = &self::$CSSQueue['FILES'];
			$Queue[] = $href;
			$fncLoad = function()use($Queue, $boolUnique, $Use_ClientLoadCSS) {
				echo \html::CSS_LinkTag($Queue, true, $boolUnique, $Use_ClientLoadCSS);
			};
			if (T\HTTP::IsAsync())
				\Output::AddIn_AjaxOutput($fncLoad, $AjaxKW, null, 'HTMLLoadCSS');
			else
				self::CSS_Include($fncLoad, 'HTMLLoadCSS');
		}
	}

	/**
	 * @param type $Code
	 * @param str $OverrideID  //KW causes the new Code overrides the older code and the code is removable totally
	 */
	public static function InlineCSS($Code, $OverrideID = NULL) {
		$Queue = &self::$CSSQueue['INLINE'];
		if ($OverrideID)
			$Queue[$OverrideID] = $Code;
		else
			$Queue[] = $Code;
	}

	public static function InlineCSS_GetRenderedMarkup() {
		return count(self::$CSSQueue['INLINE']) > 0 ? '
<style rel="stylesheet" type="text/css">
	' . implode("\n\n", self::$CSSQueue['INLINE']) . '
</style>' : '';
	}

	public static function InlineCSS_Remove($OverrideID) {
		$Queue = &self::$CSSQueue['INLINE'];
		if (isset($Queue[$OverrideID])) {
			unset($Queue[$OverrideID]);
			return true;
		}
		return false;
	}

	private static $JS_RootDir = '/_js';
	private static $JS_Ext = '.js';

	private static function GetJSURL($URL) {
		$AbsoluteURI_Sign = '*';
		if ($URL[0] != $AbsoluteURI_Sign)
			$URL = self::$JS_RootDir . "/$URL" . self::$JS_Ext;
		else
			$URL = substr($URL, 1);
		return $URL;
	}

	/**
	 * @param string|array $mixedSrc
	 * @param type $boolReturnMarkup
	 * @param type $boolUnique
	 * @param type $Use_ClientLoadJS
	 * @return string
	 */
	public static function JS_SrcTag($mixedSrc, $boolReturnMarkup = true, $boolUnique = true, $Use_ClientLoadJS = true) {
		if (T\HTTP::IsAsync())
			$Use_ClientLoadJS = true;
		if (!is_array($mixedSrc))
			$mixedSrc = self::GetJSURL($mixedSrc);
		if ($Use_ClientLoadJS) {
			if (is_array($mixedSrc)) {
				$markup = array();
				foreach ($mixedSrc as $Src) {
					$Src = self::GetJSURL($Src);
					$markup[] = self::UniqueHandler($Src, "_t.LoadJS('*$Src')", true, $boolUnique);
				}
				$markup = array_filter($markup);
				$markup = implode("\n", $markup);
				$markup = "\n<script type='text/javascript'>\n$markup</script>";
			} else
				$markup = self::UniqueHandler($mixedSrc, "\n<script type='text/javascript'>_t.LoadJS('*$mixedSrc'" . ($boolUnique ? '' : ', true') . ")</script>", $boolReturnMarkup, $boolUnique);
		} else
			$markup = self::UniqueHandler($mixedSrc, "\n<script src='$mixedSrc' type='text/javascript'></script>", $boolReturnMarkup, $boolUnique);
		return $markup;
	}

	public static $cntIncludedJS = NULL;

	public static function JS_Include($mixedMarkup, $Params = NULL, $ContentUniqueKW = NULL) {
		if (!self::$cntIncludedJS)
			self::$cntIncludedJS = self::Container("_cntIncludedJS");

		self::$cntIncludedJS->AddContent($mixedMarkup, $Params, $ContentUniqueKW);
	}

	private static $JSQueue = array('INLINE' => array(), 'FILES' => array());

	/**
	 * puts the js to load in main layout and in normal ajax requests
	 * The js will be loaded uniquely if you use $Use_ClientLoadJS as true and $boolUnique as true
	 * @param string $strSrc	//this will be relative to the js root (/htdocs/_js)
	 * 	"myjsCat/jsFile"	//if you use the relative path you can omit the .js extension
	 * 	but to load absolute js files uniquely or repetitively just use an asterisk at first and use the js extension:<br/>
	 * 	"* /myabsolutepath/to/file.js"
	 * @param bool $boolUnique		//whether to load uniquely or not
	 * @param bool $Use_ClientLoadJS
	 * 	//if you avoid using client js loader then the unique load can be applied only on the server codes not in the
	 * 	client codes so if you load a js file during an ajax request it may get duplicated in the client
	 * @param string $AjaxKW	//using ajax kw you can explicitely define what ajax request you want to load this file during it.
	 */
	public static function LoadJS($strSrc, $boolUnique = true, $Use_ClientLoadJS = true, $AjaxKW = NULL) {
		if (!$Use_ClientLoadJS)
			self::JS_Include(self::JS_SrcTag($strSrc, true, $boolUnique, $Use_ClientLoadJS));
		else if (($Use_ClientLoadJS && $boolUnique) || !$boolUnique) {
			$Queue = &self::$JSQueue['FILES'];
			$Queue[] = $strSrc;
			$fncLoad = function()use($Queue, $boolUnique, $Use_ClientLoadJS) {
				echo \html::JS_SrcTag($Queue, true, $boolUnique, $Use_ClientLoadJS);
			};

			if (T\HTTP::IsAsync())
				\Output::AddIn_AjaxOutput($fncLoad, $AjaxKW, null, 'HTMLLoadJS');
			else
				self::JS_Include($fncLoad, null, 'HTMLLoadJS');
		}
	}

	/**
	 * @param string $Code
	 * @param string $OverrideID  //KW causes the new Code overrides the older code and the code is removable totally
	 * @param string $AjaxKW	//using ajax kw you can explicitely define what ajax request you want to put this script through it.
	 * @param string $AjaxContentUKW if null then $OverrideID will be used
	 */
	public static function InlineJS($Code, $OverrideID = NULL, $AjaxKW = false, $AjaxContentUKW = NULL) {
		$Queue = &self::$JSQueue['INLINE'];
		if ($OverrideID)
			$Queue[$OverrideID] = $Code;
		else
			$Queue[] = $Code;
		if ($AjaxKW !== false)
			\Output::AddIn_AjaxOutput('<script>' . $Code . '</script>', $AjaxKW, NULL, $AjaxContentUKW ? $AjaxContentUKW : $OverrideID);
	}

	public static function InlineJS_GetRenderedMarkup() {
		return count(self::$JSQueue['INLINE']) > 0 ? '
<script type="text/javascript">
	' . implode("\n\n", self::$JSQueue['INLINE']) . '
</script>' : '';
	}

	public static function InlineJS_Remove($OverrideID, $AjaxContentUKW = NULL) {
		$Queue = &self::$JSQueue['INLINE'];
		if (isset($Queue[$OverrideID])) {
			unset($Queue[$OverrideID]);
		}
		\Output::$cntAjax->RemoveContent($AjaxContentUKW ? $AjaxContentUKW : $OverrideID);
	}

	#----------------- Special Codes -----------------#

	public static function LightBox_Load() {
		\html::LoadCSS("*/_js/lightbox/jquery.lightbox-0.5.css");
		\html::LoadJS("lightbox/jquery.lightbox-0.5.pack");
		\html::LoadJS("lightbox/lightbox.myoptimization");
	}

	public static function SuperBox_Load() {
		\html::LoadCSS("*/_js/superbox0.9.1/jquery.superbox.css");
		\html::LoadJS("superbox0.9.1/jquery.superbox-min");
		\html::LoadJS("superbox0.9.1/superbox.myoptimization");
		\html::InlineJS("
$.superbox.settings = {
	loadTxt: '<div class=\"AjaxLoadingRing\"></div>',
	closeTxt: '<span title=\"Esc\">X</span>',
	prevTxt: '{{@res_Previous}}',
	nextTxt: '{{@res_Next}}'
};
			", 'SuperboxConfig');
//	boxId: 'superbox',
//	boxClasses: '',
//	overlayOpacity: .8,
//	boxWidth: '600',
//	boxHeight: '400',
	}

	static function SuperBox_CloseScript($Quotes = "'") {
		return "$($Quotes#superbox .close a$Quotes).click();";
	}

	static function Navs_Load() {
		html::LoadCSS('*/_Components/Navs/Navs.css');
		html::LoadJS('*/_Components/Navs/Navs.js');
	}

	static function Titler_Load() {
		html::LoadCSS('*/_js/Titler/Titler.css');
		html::LoadJS('Titler/Titler');
	}

	/**
	 * PROMPT USAGE GUIDANCE
	 * 
	 * jAlert('error | warning | success | info'<br>
	 * , 'MESSAGE', 'TITLE'<br>
	 * , function(boolResult){}	//deafult callback for normal model<br>
	 * , [{attrs:'id="myID" ', value:'123', focus:1, callback:function(e){}}]	//my buttons<br>
	 * );<br>
	 * <br>
	 * jConfirm(<br>
	 * 'MESSAGE', 'TITLE'<br>
	 * , function(boolResult){}	//deafult callback for normal model<br>
	 * , [{attrs:'id="myID" ', value:'123', focus:1, callback:function(e){}}]	//my buttons<br>
	 * );<br>
	 * <br>
	 * jPrompt(<br>
	 * 'MESSAGE', 'PROMPT_PREDEFINED_VALUE', 'TITLE'<br>
	 * , function(boolResult){}	//deafult callback for normal model<br>
	 * , [{attrs:'id="myID" ', value:'123', focus:1, callback:function(e){}}]	//my buttons<br>
	 * );
	 */
	static function Prompt_Load() {
		html::LoadCSS('*/_js/Assets_Prompt/jquery.alerts.css');
		html::LoadJS('jqUI/jquery.ui.core.min');
		html::LoadJS('jqUI/jquery.ui.widget.min');
		html::LoadJS('jqUI/jquery.ui.mouse.min');
		html::LoadJS('Assets_Prompt/jquery.alerts');
	}

	static function Scrollable_Load() {
		html::LoadCSS('*/_js/Scrollable/scrollable-horizontal.css');
		html::LoadCSS('*/_js/Scrollable/scrollable-buttons.css');
		html::LoadCSS('*/_js/Scrollable/scrollable-navigator.css');
		html::LoadJS('Scrollable/scrollable.min');
		html::LoadJS('Scrollable/scrollable.navigator.min');
//		html::LoadJS('Scrollable/toolbox.mousewheel.min');
	}

	static function DataGrid_Load($Theme = \Conf::jQTheme) {
		$Theme = $Theme ? $Theme : \Conf::jQTheme;
		\html::LoadCSS("*/_js/jqUI/themes/$Theme/jquery-ui.custom.css");
//		\html::LoadJS('jqGrid/jQueryUI/jquery-ui.custom.min');
		\html::LoadJS('jqUI/jquery.ui.core.min');
		\html::LoadJS('jqUI/jquery.ui.widget.min');
		\html::LoadJS('jqUI/jquery.ui.mouse.min');
		\html::LoadJS('jqUI/jquery.ui.position.min');
		\html::LoadJS('jqUI/jquery.ui.resizable.min');
		\html::LoadJS('jqUI/jquery.ui.button.min');
		\html::LoadJS('jqUI/jquery.ui.dialog.min');
		\html::LoadJS('jqUI/jquery.ui.datepicker.min');
		$locale = explode('_', \Yii::app()->language);
		\html::LoadJS('jqGrid/jQueryUI/i18n/grid.locale-' . strtolower($locale[0]));
		//jqgrid
		\html::LoadCSS('*/_js/jqGrid/ui.jqgrid.css');
		\html::LoadJS('jqGrid/jquery.jqGrid.min');
		\html::LoadJS('jqGrid/myOptimize.jqGrid');
	}

	static function TinyMCE_Load($Conf = 'Admin') {
//		$ConfJS = \html::JS_SrcTag('tiny_mce/MyConfigs/Royale_en.conf', 1, 1, 0);
//		\html::JS_Include(\html::JS_SrcTag('tiny_mce/Core/tiny_mce') . $ConfJS);
//		\Output::AddIn_AjaxOutput($ConfJS, $AjaxSpecialKW);
		\html::Prompt_Load();
		\html::LoadJS('tiny_mce/Core/tiny_mce');
		\html::LoadJS("tiny_mce/MyConfigs/$Conf.conf");
	}

	/**
	 * WARNING! load Assets_Prompt before creating an auto complete because of jquery.ui.draggable of prompts
	 * @param type $Theme
	 */
	static function jqUI_AutoComplete_Load($Theme = \Conf::jQTheme) {
		\html::LoadCSS("*/_js/jqUI/themes/$Theme/jquery-ui.custom.css");
		\html::LoadJS('jqUI/jquery.ui.core.min');
		\html::LoadJS('jqUI/jquery.ui.widget.min');
		\html::LoadJS('jqUI/jquery.ui.position.min');
		\html::LoadJS('jqUI/jquery.ui.autocomplete.min');
		\html::LoadJS('MyJuiAutoComplete/MyAutoComplete');
	}

	/**
	 * WARNING! load Assets_Prompt before creating an auto complete because of jquery.ui.draggable of prompts
	 * @param type $Theme
	 */
	static function jqUI_Combobox_Load($Theme = \Conf::jQTheme) {
		\html::LoadCSS("*/_js/jqUI/themes/$Theme/jquery-ui.custom.css");
		\html::LoadCSS("*/_js/MyJuiAutoComplete/Style.css");
		\html::LoadJS('jqUI/jquery.ui.core.min');
		\html::LoadJS('jqUI/jquery.ui.widget.min');
		\html::LoadJS('jqUI/jquery.ui.button.min');
		\html::LoadJS('jqUI/jquery.ui.position.min');
		\html::LoadJS('jqUI/jquery.ui.autocomplete.min');
		\html::LoadJS('MyJuiAutoComplete/MyComboBox');
	}

//	static function MozillaRTL() {
//		$UAgent = T\HTTP::RequestHeaders('User-Agent');
//		return
//				stripos($UAgent, 'WebKit') === false ?
//				\F3::get('res_Direction') :
//				'';
//	}
}
