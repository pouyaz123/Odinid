<?

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

	public static function AjaxMsg_Exit($Msg, $HttpHeader = C\Header::BadRequest, $HttpCode = C\Header::BadRequestCode) {
		T\HTTP::Header($HttpHeader, true, $HttpCode);
		echo $Msg;
		exit;
	}

#----------------- Ajax Tools -----------------#

	private static function AjaxMaker($RelKW, $DefaultButton_jQSelector = NULL, $SpecialKW = NULL, $AjaxPostParams = NULL, $AjaxURL = null, $AjaxTarget_jQSelector = null) {
		return " $RelKW" #
				. ( $AjaxTarget_jQSelector ? ':' . $AjaxTarget_jQSelector : '' ) #
				. ( $AjaxURL ? self::AsyncURL($AjaxURL) : '' )
				. ($DefaultButton_jQSelector ? self::DefaultButton($DefaultButton_jQSelector) : '')
				. ($SpecialKW ? ' ' . \Output::AjaxKeyword_PostParamName . ":$SpecialKW " : '')
				. ($AjaxPostParams ? " AjaxPostParams:$AjaxPostParams " : '')
				. ' ';
	}

	public static function AsyncURL($URL) {
		return " AsyncURL:$URL";
	}

	public static function AsyncParams($Params) {
		return " AjaxPostParams:$Params ";
	}

	/**
	 * @param str $SpecialKW //we'll use it again for \Output::AddIn_AjaxOutput($func, $strKW = null)
	 * @return a str for rel attr
	 */
	public static function AjaxPanel($DefaultButton_jQSelector = NULL, $SpecialKW = NULL, $AjaxPostParams = NULL, $AjaxURL = null, $AjaxTarget_jQSelector = null) {
		return self::AjaxMaker('AjaxPanel', $DefaultButton_jQSelector, $SpecialKW, $AjaxPostParams, $AjaxURL, $AjaxTarget_jQSelector);
	}

	/**
	 * 
	 * @param string $AjaxTarget_jQSelector //#divContent:insert || .divASD:replace
	 * @param string $SpecialKW	//ajax postal special keyword
	 * @param string $AjaxPostParams	//P1=value;P2=value
	 * @return string //string value to be placed in a "rel" attribute
	 */
	public static function AjaxLinks($AjaxTarget_jQSelector = null, $SpecialKW = NULL, $AjaxPostParams = NULL) {
		return self::AjaxMaker('AjaxPanel', NULL, $SpecialKW, $AjaxPostParams, NUlL, $AjaxTarget_jQSelector);
	}

	/**
	 * @param str $SpecialKW //we'll use it again for \Output::AddIn_AjaxOutput($func, $strKW = null)
	 * @return a str for rel attr
	 */
	public static function AjaxElement($AjaxTarget_jQSelector = null, $SpecialKW = NULL, $AjaxPostParams = NULL, $AjaxURL = null) {
		return self::AjaxMaker('AjaxElement', NULL, $SpecialKW, $AjaxPostParams, $AjaxURL, $AjaxTarget_jQSelector);
	}

//	public static function GetAsync_URLHash($AjaxTarget_jQSelector, $AjaxPostParams) {
//		return "##ASYNCHASH#$AjaxPostParams->$AjaxTarget_jQSelector";
//	}

	const AjaxExcept = " AjaxExcept ";

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

	private static $IsAltRow = false;

	static function AltRow($Reset = false, $JustClassName = false, $JustBool = false) {
		if ($Reset)
			self::$IsAltRow = false;
		$Result = self::$IsAltRow;
		self::$IsAltRow = self::$IsAltRow ? false : true;

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
//	public static function PutInContainer(&$strobjContent = "", $Prefix = "<div>", $Postfix = '</div>', $OnlyReturnMsg = true) {
//		if (is_string($strobjContent)) {
//			$strobjContent = $Prefix . $strobjContent . $Postfix;
//			$strobjContent = \F3::resolve($strobjContent);
//		} elseif (is_a($strobjContent, '\Base\Container')) {
//			$strobjContent->AddContentAt($Prefix, 0);
//			$strobjContent->AddContentAt($Postfix, -1);
//		}
//		if ($OnlyReturnMsg)
//			return $strobjContent;
//		else {
//			echo $strobjContent;
//			return $strobjContent;
//		}
//	}
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
			}
			else
				$markup = self::UniqueHandler($mixedHREF, "\n<script type='text/javascript'>_t.LoadCSS('*$mixedHREF'" . ($boolUnique ? '' : ', true') . ")</script>", $boolReturnMarkup, $boolUnique);
		}
		else
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
			}
			else
				$markup = self::UniqueHandler($mixedSrc, "\n<script type='text/javascript'>_t.LoadJS('*$mixedSrc'" . ($boolUnique ? '' : ', true') . ")</script>", $boolReturnMarkup, $boolUnique);
		}
		else
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
	 * @param string $AjaxKW
	 * @param string $AjaxContentUKW
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

	static function PostbackConfirm_OnClick($Text = '', $Title = '{{@res_Confirmation}}') {
		$Text = addslashes($Text);
		$Title = addslashes($Title);
		return " onclick=\"return PostBack.jConfirm(this, '$Text', '$Title')\" ";
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
		\html::LoadJS('MyAutoComplete');
	}

//	static function MozillaRTL() {
//		$UAgent = T\HTTP::RequestHeaders('User-Agent');
//		return
//				stripos($UAgent, 'WebKit') === false ?
//				\F3::get('res_Direction') :
//				'';
//	}
}

?>