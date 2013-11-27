<?php

/**
 * Odinid language center
 * translations and ...
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class Lng {

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
		return \Yii::t("\Site\SiteModule.$category", $message, $params = array(), $source = null, $language = null);
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
		return \Yii::t("\Admin\AdminModule.$category", $message, $params = array(), $source = null, $language = null);
	}

	static function PageTitle($ModuleName, $category, $message, $params = array(), $source = null, $language = null) {
		return \Yii::app()->name . ' | ' . \Yii::t("\\$ModuleName\\{$ModuleName}Module.$category", $message, $params = array(), $source = null, $language = null);
	}

	static function AdminPageTitle($category, $message, $params = array(), $source = null, $language = null) {
		return self::PageTitle('Admin', $category, $message, $params, $source, $language);
	}

	static function SitePageTitle($category, $message, $params = array(), $source = null, $language = null) {
		return self::PageTitle('Site', $category, $message, $params, $source, $language);
	}

	static function tarray(&$array, $strTranslationModule = NULL, $strTranslationCat = NULL) {
		if ($strTranslationCat) {
			foreach ($array as $key => $val) {
				$array[$key] == \Yii::t(
								$strTranslationModule ?
										"\\$strTranslationModule\\{$strTranslationModule}Module.$strTranslationCat" :
										$strTranslationCat
								, $val);
			}
		}
	}

}

?>
