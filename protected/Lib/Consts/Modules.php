<?php

namespace Consts;

use \Tools as T;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 2
 * @copyright (c) Abbas Ali Hashemian
 * @access public
 */
final class Modules {

//	static $LinkTypes = array(1 => 'URL', 2 => 'ACatOrItem', 3 => 'ContentsOfACat');
//	static $LocationTypes = array(1 => "CAT", 2 => "ITEM", 3 => "EmailTemplates");
//	static $Genders = array(1 => '{{@res_Mr}}', 2 => '{{@res_Madam}}');
//	static $SiteMapPriorities = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
//
//	static function UploadPath_BySubPath($SubPath, $Secure = false) {
//		if (\Conf::$IsDemoAdmin)
//			return T\DemoAdmin::UploadPath_BySubPath($SubPath);
//
//		$SubPath = trim($SubPath, '\\/');
//		if ($Secure)
//			return \Conf::ProjectPath . "/_UPLOADS/$SubPath";
//		else
//			return $_SERVER['DOCUMENT_ROOT'] . "/_UPLOADS/$SubPath";
//	}
//
//	static function FileURL_BySubURI($SubURI, $Secure = false) {
//		if (\Conf::$IsDemoAdmin)
//			return T\DemoAdmin::FileURL_BySubURI($SubURI);
//
//		$SubURI = str_replace('\\', '/', trim($SubURI, '\\/'));
//		if ($Secure)
//			return "/SecureUPLOADS/$SubURI";
//		else
//			return "/_UPLOADS/$SubURI";
//	}
//
//	static function ThumbnailURL_BySubURI($SubURI, $Secure = false, $Dims = '175x100') {
//		if (\Conf::$IsDemoAdmin)
//			return T\DemoAdmin::ThumbnailURL_BySubURI($SubURI, $Dims);
//
//		$SubURI = str_replace('\\', '/', trim($SubURI, '\\/'));
//		if ($Secure)
//			return "/SecureUPLOAD_THUMBS/$Dims/$SubURI";
//		else
//			return "/UPLOAD_THUMBS/$Dims/$SubURI";
//	}
//
//	static function UploadPath($DBTableName, $UploadFieldName, $dr, $Secure = false) {
//		return self::UploadPath_BySubPath("$DBTableName/$UploadFieldName/" . self::GetFileName($dr, $UploadFieldName), $Secure);
//	}
//
//	static function FileURL($DBTableName, $UploadFieldName, $dr, $Secure = false) {
//		return self::FileURL_BySubURI("$DBTableName/$UploadFieldName/" . self::GetFileName($dr, $UploadFieldName), $Secure);
//	}
//
//	static function ThumbnailURL($DBTableName, $UploadFieldName, $dr, $Secure = false) {
//		return self::ThumbnailURL_BySubURI("$DBTableName/$UploadFieldName/" . self::GetFileName($dr, $UploadFieldName), $Secure);
//	}
//
//	static function ThumbnailURL_ByDims($DBTableName, $UploadFieldName, $dr, $Dims = '175x100', $Secure = false) {
//		return self::ThumbnailURL_BySubURI("$DBTableName/$UploadFieldName/" . self::GetFileName($dr, $UploadFieldName), $Secure, $Dims);
//	}
//
//	static function GetFileName($dr, $DBFieldName, $WithoutExt = false, $WithLangID = true) {
//		if ($WithLangID)
//			$LangID = @$dr['LangID'];
//		return $dr['ID'] . ($WithLangID && $LangID ? '_' . $LangID : '') . ($WithoutExt ? '' : '.' . $dr[$DBFieldName]);
//	}

//	static function GetActiveCountries($Fields = "ID, CONCAT(Name, ' (+', PhoneCode, ')') AS Name") {
//		$KW = 'Countries_' . sprintf('%u', crc32($Fields));
//		if (!($dtResult = T\Site::GetCache($KW)))
//			T\Site::SetInCache($KW, $dtResult = T\DB::GetTable("SELECT $Fields FROM countries WHERE `IsActive` ORDER BY Name"));
//		return $dtResult;
//	}
//
//	static function GetActiveCurrencies($Fields = "ID, CONCAT(Code, ' (', Title, ')') AS Title") {
//		$KW = 'Currencies_' . sprintf('%u', crc32($Fields));
//		if (!($dtResult = T\Site::GetCache($KW)))
//			T\Site::SetInCache($KW, $dtResult = T\DB::GetTable("SELECT $Fields FROM currencies WHERE `IsActive` ORDER BY `Code`"));
//		return $dtResult;
//	}

}

?>
