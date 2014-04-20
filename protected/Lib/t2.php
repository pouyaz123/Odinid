<?php

/**
 * Odinid language center (translation 2 previously was Lng from Language)
 * t2 is more simple and similar to Yii standard
 * translations and ... 
 * WARNING : when you add new translation file add it to the Lng_StaticMethodPHPDoc.php class to have right intelli sense auto complete
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 2
 * @copyright (c) Odinid
 * @access public
 */
class t2 {

	private static $_SiteLoaded = false;
	private static $_AdminLoaded = false;
	private static $Err_ConflictUsage = 'Conflict in usage of both of Site and Admin translation resources (\t2::Site & \t2::Admin)';

	/**
	 * improved Yii::t to support modular base
	 * @param type $strTranslationModule
	 * @param type $category
	 * @param type $message
	 * @param type $params
	 * @param type $source
	 * @param type $language
	 * @return type
	 */
	static function t($strTranslationModule = null, $category, $message, $params = array(), $source = null, $language = null) {
		if ($category) {
			return \Yii::t(
							$strTranslationModule ?
									"\\$strTranslationModule\\{$strTranslationModule}Module.$category" :
									$category
							, $message, $params, $source, $language);
		}
	}

//	 * The module name will come from ucwords(\Yii::app()->controller->module->id) and category by the name of called magic method<br/>
//	 * If there was no \Yii::app()->controller->module you should use \t2::ModuleName_CatName('msg')<br/>
//	 * For example in module's init , no module exists yet
	/**
	 * PHPDoc doesn't support magic static methods to have autocomplete so we have a fake Lng class too here : Lng_StaticMethodPHPDoc<br/>
	 * @param type $name
	 * @param type $arguments
	 * @return string 
	 * mytodo 1: make translator sweeter by \Yii::app()->controller->module->id
	 */
	public static function __callStatic($name, $arguments) {
//		static $objModule = null;
//		if (!$objModule) {
//			$ctrl = \Yii::app()->controller;
//			$objModule = $ctrl?$ctrl->module:null;
//		}
//		if ($objModule) {
//			$Module = ucwords($objModule->id);
//			$Category = $name;
//		} else {
		$Module = trim(strstr($name, '_', true), '_');
		$Category = trim(strstr($name, '_'), '_');
//		}
		$Category = strtolower('tr_' . $Category);
		array_unshift($arguments, $Category);
		return call_user_func_array(array(__CLASS__, $Module), $arguments);
	}

	/**
	 * ONLY RETURNS the appropriate composed title
	 * @param type $ModuleName
	 * @param type $category
	 * @param type $message
	 * @param type $params
	 * @param type $source
	 * @param type $language
	 * @return type
	 */
	static function PageTitle($ModuleName, $category, $message, $params = array(), $source = null, $language = null) {
		return \Yii::app()->name . ' | ' . self::t($ModuleName, $category, $message, $params, $source, $language);
	}

	/**
	 * ONLY RETURNS the appropriate composed title
	 * @param type $category
	 * @param type $message
	 * @param type $params
	 * @param type $source
	 * @param type $language
	 * @return type
	 */
	static function AdminPageTitle($message, $params = array(), $source = null, $language = null) {
		self::$_AdminLoaded = true;
		if (self::$_SiteLoaded)
			throw new \Err(__METHOD__, self::$Err_ConflictUsage, func_get_args());
		return self::PageTitle('Admin', 'tr_admin', $message, $params, $source, $language);
	}

	/**
	 * ONLY RETURNS the appropriate composed title
	 * @param type $category
	 * @param type $message
	 * @param type $params
	 * @param type $source
	 * @param type $language
	 * @return type
	 */
	static function SitePageTitle($message, $params = array(), $source = null, $language = null) {
		self::$_SiteLoaded = true;
		if (self::$_AdminLoaded)
			throw new \Err(__METHOD__, self::$Err_ConflictUsage, func_get_args());
		return self::PageTitle('Site', 'tr_site', $message, $params, $source, $language);
	}

//	 * @deprecated since version 1.1 due to easy to use functions : Site_Common, ...
	/**
	 * Translates a message to the specified language.
	 * This method supports choice format (see {@link CChoiceFormat}),
	 * i.e., the message returned will be chosen from a few candidates according to the given
	 * number value. This feature is mainly used to solve plural format issue in case
	 * a message has different plural forms in some languages.
	 * @param string $category message category. Please use only word letters. Note, category 'yii' is
	 * reserved for Yii framework core code use. See {@link CPhpMessageSource} for
	 * more interpretation about message category.
	 * @param string $message the original message
	 * @param array $params parameters to be applied to the message using <code>strtr</code>.
	 * The first parameter can be a number without key.
	 * And in this case, the method will call {@link CChoiceFormat::format} to choose
	 * an appropriate message translation.
	 * Starting from version 1.1.6 you can pass parameter for {@link CChoiceFormat::format}
	 * or plural forms format without wrapping it with array.
	 * This parameter is then available as <code>{n}</code> in the message translation string.
	 * @param string $source which message source application component to use.
	 * Defaults to null, meaning using 'coreMessages' for messages belonging to
	 * the 'yii' category and using 'messages' for the rest messages.
	 * @param string $language the target language. If null (default), the {@link CApplication::getLanguage application language} will be used.
	 * @return string the translated message
	 * @see CMessageSource
	 */
	static function Site($category, $message, $params = array(), $source = null, $language = null) {
		self::$_SiteLoaded = true;
		if (self::$_AdminLoaded)
			throw new \Err(__METHOD__, self::$Err_ConflictUsage, func_get_args());
		return self::t('Site', $category, $message, $params, $source, $language);
	}

//	 * @deprecated since version 1.1 due to easy to use functions : Admin_User, ...
	/**
	 * Translates a message to the specified language.
	 * This method supports choice format (see {@link CChoiceFormat}),
	 * i.e., the message returned will be chosen from a few candidates according to the given
	 * number value. This feature is mainly used to solve plural format issue in case
	 * a message has different plural forms in some languages.
	 * @param string $category message category. Please use only word letters. Note, category 'yii' is
	 * reserved for Yii framework core code use. See {@link CPhpMessageSource} for
	 * more interpretation about message category.
	 * @param string $message the original message
	 * @param array $params parameters to be applied to the message using <code>strtr</code>.
	 * The first parameter can be a number without key.
	 * And in this case, the method will call {@link CChoiceFormat::format} to choose
	 * an appropriate message translation.
	 * Starting from version 1.1.6 you can pass parameter for {@link CChoiceFormat::format}
	 * or plural forms format without wrapping it with array.
	 * This parameter is then available as <code>{n}</code> in the message translation string.
	 * @param string $source which message source application component to use.
	 * Defaults to null, meaning using 'coreMessages' for messages belonging to
	 * the 'yii' category and using 'messages' for the rest messages.
	 * @param string $language the target language. If null (default), the {@link CApplication::getLanguage application language} will be used.
	 * @return string the translated message
	 * @see CMessageSource
	 */
	static function Admin($category, $message, $params = array(), $source = null, $language = null) {
		self::$_AdminLoaded = true;
		if (self::$_SiteLoaded)
			throw new \Err(__METHOD__, self::$Err_ConflictUsage, func_get_args());
		return self::t('Admin', $category, $message, $params, $source, $language);
	}

	/**
	 * Translates a message to the specified language.
	 * This method supports choice format (see {@link CChoiceFormat}),
	 * i.e., the message returned will be chosen from a few candidates according to the given
	 * number value. This feature is mainly used to solve plural format issue in case
	 * a message has different plural forms in some languages.
	 * @param string $message the original message
	 * @param array $params parameters to be applied to the message using <code>strtr</code>.
	 * The first parameter can be a number without key.
	 * And in this case, the method will call {@link CChoiceFormat::format} to choose
	 * an appropriate message translation.
	 * Starting from version 1.1.6 you can pass parameter for {@link CChoiceFormat::format}
	 * or plural forms format without wrapping it with array.
	 * This parameter is then available as <code>{n}</code> in the message translation string.
	 * @param string $source which message source application component to use.
	 * Defaults to null, meaning using 'coreMessages' for messages belonging to
	 * the 'yii' category and using 'messages' for the rest messages.
	 * @param string $language the target language. If null (default), the {@link CApplication::getLanguage application language} will be used.
	 * @return string the translated message
	 * @see CMessageSource
	 */
	static function General($message, $params = array(), $source = null, $language = null) {
		return self::t(null, 'tr_general', $message, $params, $source, $language);
	}

	/**
	 * same as \t2::t which is improved Yii:t to support modular base
	 * tarray translates each element of the array and modifies the referenced array to the translated version
	 * @param type $array reference
	 * @param type $strTranslationModule
	 * @param type $category
	 * @param type $params
	 * @param type $source
	 * @param type $language
	 */
	static function tarray(&$array, $strTranslationModule = NULL, $category = NULL, $params = array(), $source = null, $language = null) {
		if ($category)
			foreach ($array as $key => $msg)
				$array[$key] = self::t($strTranslationModule, $category, $msg, $params, $source, $language);
	}

	static function InitializeTranslation($Langs) {
//		$lang = trim(\GPCS::GET('lang'), '/');	//trim was because of when i tried in the routes to handle the with and without lang modes only by one route regexp pattern
		$lang = \GPCS::GET('lang');
		if ($lang && in_array($lang, $Langs))
			\Yii::app()->language = $lang;
		else { //en -> en translate : to change a msg in one file and affect everywhere
//			\Yii::app()->sourceLanguage = '00';
			\Yii::app()->language = $Langs[0];
		}
	}

	/**
	 * returns the array of the common lang file located under /protected/messages_common
	 * @param string $LangAndCategory such as en/User which will be /protected/messages_common/en/User.php
	 */
	static function GetCommonLangResourceArray($LangAndCategory) {
		static $AppDir = NULL;
		if (!$AppDir)
			$AppDir = \Conf::AppDir();
		return require_once $AppDir . "/messages/$LangAndCategory.php";
	}

	/**
	 * gets default language from \Yii::app()->sourceLanguage orelse if it was empty get from \Conf::$SiteModuleLangs[0]
	 * @staticvar null $Lng
	 * @return \Lng_LangObj $Object to get the properties of the language easily
	 */
	static function GetDefaultLang() {
		static $Lng = null;
		if (!$Lng) {
			$Lng = new Lng_LangObj();
			$Lng->LocaleID = \Yii::app()->sourceLanguage;
//			if ($Lng->LocaleID === '00')
//				$Lng->LocaleID = \Yii::app()->language;
			if (!$Lng->LocaleID)
				$Lng->LocaleID = \Conf::$SiteModuleLangs[0];
			$Lng->LangCode = strstr($Lng->LocaleID, '_', true);
		}
		return $Lng;
	}

}

class Lng_LangObj {

	public $LocaleID;
	public $LangCode;

}
