<?php

use \Tools as T;
use \Consts as C;

/**
 * Tondarweb Output center
 * collects and controls async(ajax) and non-async outputs, thumbnails and and all output things
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 2
 * @copyright (c) Abbas Ali Hashemian
 * @access public
 */
class Output {

	const AjaxKeyword_PostParamName = '__AjaxPostKW';

	/**
	 * @var \Base\Container
	 */
	public static $cntPage = null;

	/**
	 * @var \Base\Container
	 */
	public static $cntAjax = null;
	private static $IsInitialized = false;

	public static function Initialize() {
		if (!self::$IsInitialized) {
			self::$cntPage = \html::Container("_cntPage");
			self::$cntAjax = \html::Container("_cntAjax");
		}
		self::$IsInitialized = true;
	}

	public static $IsRenderPassed = false;

	public static function Render($objController = NULL, $view = NULL, $data = NULL, $ActiveFormID = NULL, $return = false, $processOutput = false) {
		self::$IsRenderPassed = true;
		$IsAjax = T\HTTP::IsAsync();
		/* @var $objController CController */
		if ($objController) {
			if (!$view)
				\Err::ErrMsg_Method(__METHOD__, 'There is a controller but no view passed to the output renderer', func_get_args());
			if ($IsAjax) {
				if (!GPCS::REQUEST(self::AjaxKeyword_PostParamName))
					self::$cntAjax->AddContent(function()use($objController, $ActiveFormID, $view, $data) {
								if (method_exists($objController, 'getPageTitle'))
									\html::AjaxPageTitle($objController->pageTitle);
//							echo $objController->renderPartial($view, $data, true)
//									. ($ActiveFormID?T\HTTP::Ajax_getCActiveFormScript($ActiveFormID):'');
								$output = $objController->renderPartial($view, $data, true);
								$cs = \Yii::app()->getClientScript();
								/* @var $cs \CClientScript */
								$cs->renderBodyEnd($output);
								echo $output;
							});
			} else {
				self::$cntPage->AddContent(function()use($objController, $view, $data) {
							$objController->render($view, $data);
						});
			}
		}
		//IMPORTANT use ->__toString() method because the Yii thrown exceptions in components and ... does not work in to string magic call
		echo $IsAjax ? self::$cntAjax->__toString() : (self::$cntPage->__toString() . '<!-- Developer : Abbas Ali Hashemian<tondarweb@gmail.com> - http://webDesignir.com -->');
	}

	/**
	 * @param string|callable $mixedContent //fnc ...($Params){}
	 * @param mixed $Params //will be passed to $func
	 * @param string $ContentUniqueKW //content can be overriden based on this keyword
	 */
	public static function AddIn_GeneralOutput($mixedContent, $Params = NULL, $ContentUniqueKW = NULL) {
		self::$cntPage->AddContent($mixedContent, $Params, $ContentUniqueKW);
	}

	/**
	 * @param string|callable $mixedContent //fnc ...($Params){}
	 * @param str $AjaxKW
	 * @param mixed $Params //will be passed to $func
	 * @param string $ContentUniqueKW //content can be overriden based on this keyword
	 */
	public static function AddIn_AjaxOutput($mixedContent, $AjaxKW = null, $Params = NULL, $ContentUniqueKW = NULL) {
		if (
				($AjaxKW && GPCS::REQUEST(self::AjaxKeyword_PostParamName) == $AjaxKW) ||
				(!$AjaxKW && !GPCS::REQUEST(self::AjaxKeyword_PostParamName)) ||
				($AjaxKW === '*' && stripos(GPCS::REQUEST(self::AjaxKeyword_PostParamName), 'DataGridAjaxKW_') !== 0 && stripos(GPCS::REQUEST(self::AjaxKeyword_PostParamName), 'AutoComplete_') !== 0)
		) {
			if (self::$IsRenderPassed) {
				if (is_callable($mixedContent))
					$mixedContent($Params);
				else
					echo $mixedContent;
			}
			else
				self::$cntAjax->AddContent($mixedContent, $Params, $ContentUniqueKW);
			return true;
		}
		return false;
	}

}

?>
