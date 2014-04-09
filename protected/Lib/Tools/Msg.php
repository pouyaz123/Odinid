<?php

namespace Tools;

class Msg {
#----------------- Msg Boxes -----------------#

	public static function Success($strMsg = "", $OnlyReturnMsg = true) {
		if (is_string($strMsg) && strlen(trim($strMsg)) == 0)
			return null;
		return \html::PutInContainer($strMsg, '<div class="SuccessMsg">', '</div>', $OnlyReturnMsg);
	}

	public static function Err($strMsg = "", $OnlyReturnMsg = true) {
		if (is_string($strMsg) && strlen(trim($strMsg)) == 0)
			return null;
		return \html::PutInContainer($strMsg, '<div class="ErrMsg">', '</div>', $OnlyReturnMsg);
	}

	public static function Warning($strMsg = "", $OnlyReturnMsg = true) {
		if (is_string($strMsg) && strlen(trim($strMsg)) == 0)
			return null;
		return \html::PutInContainer($strMsg, '<div class="WarningMsg">', '</div>', $OnlyReturnMsg);
	}

#----------------- Different -----------------#
#----------------- HTTP ERROR PAGES -----------------#
//are moved to class "\Err"
#----------------- GENERAL MSG BOX -----------------#

	private static $GeneralMsg = '';
	private static $GeneralMsg_Btns = array();

	const ErrorCSS = 'ErrMsg';
	const SuccessCSS = 'SuccessMsg';

	/**
	 * @param str $strMsg
	 * @param mixed $arrBtns
	 * array(array('value'=>'123', focus:1, callback:function(e){}))
	 */
	static function GMsg_Add($strMsg = null, $CSS = self::SuccessCSS, $arrBtns = null, $boolAddToFirst = false) {
		if ($strMsg) {
			$strMsg = "<div class='$CSS'>$strMsg</div>";
			if ($boolAddToFirst)
				self::$GeneralMsg = $strMsg . self::$GeneralMsg;
			else
				self::$GeneralMsg .= $strMsg;
		}
		if ($arrBtns) {
			if (!is_array($arrBtns))
				$arrBtns = array($arrBtns);
			self::$GeneralMsg_Btns = array_merge(self::$GeneralMsg_Btns, $arrBtns);
		}
	}

	static function GMsg_Show($Type = self::Prompt_Info, $Title = NULL, $AjaxSpecialKW = NULL) {
		self::PromptAlert($Title, self::$GeneralMsg, $Type, self::$GeneralMsg_Btns, NULL, $AjaxSpecialKW);
	}

	const Prompt_Info = 'info';
	const Prompt_Error = 'error';
	const Prompt_Warning = 'warning';
	const Prompt_Success = 'success';

	static function PromptAlert($Title, $Msg, $Type = self::Prompt_Info, $arrBtns = NULL, $PromptID = NULL, $AjaxSpecialKW = NULL) {
		\html::Prompt_Load();

		$CustomButtons = '';
		if ($arrBtns) {
			$arrBtns = array_map(function ($Elm) {
				$Elm = $Elm . '';
				return (strpos($Elm, '<') !== false) ? '"' . addslashes($Elm) . '"' : $Elm;
			}, $arrBtns);
			$CustomButtons.=implode(', ', $arrBtns);
		}
		$CustomButtons = (strlen($CustomButtons)) ? "[$CustomButtons]" : '[{value:"OK", focus:true}]';

		$Msg = addslashes($Msg);

		if (!$Title) {
			switch ($Type) {
				case self::Prompt_Info:
					$Title = \t2::Site_Common('Note');
					break;
				case self::Prompt_Success:
					$Title = \t2::Site_Common('Success');
					break;
				case self::Prompt_Warning:
					$Title = \t2::Site_Common('Warning');
					break;
				case self::Prompt_Error:
					$Title = \t2::Site_Common('Error');
					break;
				default :
					$Title = \t2::Site_Common('Note');
			}
		}
		$Title = addslashes($Title);

		$JSContent = "$(function(){jAlert( '$Type', '$Msg', '$Title', null, $CustomButtons)});";
		\html::InlineJS($JSContent, $PromptID, $AjaxSpecialKW);
//		\Output::AddIn_AjaxOutput(function ()use($JSContent) {
//					echo "<script>$JSContent</script>";
//				}, $AjaxSpecialKW, null, $PromptID);
	}

	static function PromptAlert_Remove($PromptID) {
		\html::InlineJS_Remove($PromptID);
//		\Output::$cntAjax->RemoveContent($PromptID);
	}

}

?>
