<?php

/**
 * Odinid language center
 * translations and ...
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class Lng {

	private static $_SiteLoaded = false;
	private static $_AdminLoaded = false;
	private static $Err_ConflictUsage = 'Conflict in usage of both of Site and Admin translation resources';

	static function t($strTranslationModule = null, $category, $message, $params = array(), $source = null, $language = null) {
		if ($category) {
			return \Yii::t(
							$strTranslationModule ?
									"\\$strTranslationModule\\{$strTranslationModule}Module.$category" :
									$category
							, $message, $params, $source, $language);
		}
	}

	static function PageTitle($ModuleName, $category, $message, $params = array(), $source = null, $language = null) {
		return \Yii::app()->name . ' | ' . self::t($ModuleName, $category, $message, $params, $source, $language);
	}

	static function AdminPageTitle($category, $message, $params = array(), $source = null, $language = null) {
		self::$_AdminLoaded = true;
		if (self::$_SiteLoaded)
			\Err::ErrMsg_Method(__METHOD__, self::$Err_ConflictUsage, func_get_args());
		return self::PageTitle('Admin', $category, $message, $params, $source, $language);
	}

	static function SitePageTitle($category, $message, $params = array(), $source = null, $language = null) {
		self::$_SiteLoaded = true;
		if (self::$_AdminLoaded)
			\Err::ErrMsg_Method(__METHOD__, self::$Err_ConflictUsage, func_get_args());
		return self::PageTitle('Site', $category, $message, $params, $source, $language);
	}

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
			\Err::ErrMsg_Method(__METHOD__, self::$Err_ConflictUsage, func_get_args());
		return self::t('Site', $category, $message, $params, $source, $language);
	}

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
			\Err::ErrMsg_Method(__METHOD__, self::$Err_ConflictUsage, func_get_args());
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
		else //en -> en translate : to change a msg in one file and affect everywhere
			\Yii::app()->language = $Langs[0];
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
