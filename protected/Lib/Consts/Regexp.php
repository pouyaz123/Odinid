<?php

namespace Consts;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 1
 * @access public
 */
final class Regexp {

	const URL = '/^http(s)?\:\/\/\w+(\.\w+)*(\.\w{2,})+[^\s]$/'; #	http://asd.c is wrong but http://asd.co is ok
	const URL_Local = '/^http(s)\?:\/\/\w+(\.\w+)*[^\s]?$/';
#	
	/** Force the first username character to be alphabet. Someone may choose .htaccess or other things */
	const Username = '/^[A-Za-z][\w\.]*$/';
	static function Username_InvalidCases() {
		return '/^('
				//htdocs directories
				. '_css|_js|_img|assets|me'
				//htdocs files
				. '|.*\.php|.*\.htaccess'
				//module names has been used in urlManager or may be used there
				. '|admin|site'
				//site module controllers have been set in \Conf::SiteModuleControllers
				. (\Conf::SiteModuleControllers ? '|' . \Conf::SiteModuleControllers : '')
				. ')$/i';
	}
	#
	const Email = '/^\w+([\.\-]\w+)*@\w+([\.\-]\w+)*\.\w{2,}$/';
	static function CompanyURLDomain($Domain_PregEscaped) {
		return "/^([\w\-]+\.)?$Domain_PregEscaped$/";
	}
	#
	const LogicName = '/^\w*$/';
	/**
	 * SimpleWords has been used for :
	 * <ol>
	 *	<li>geo items in register, locations, residencies</li>
	 *	<li>...</li>
	 * </ol>
	 */
	const SimpleWords = '/^[\w\s\-\,\.\'\`\(\)]+$/';
	const ASCIIChars_Simple = '/^[\w\s\-\,\.\:\"\'\`\~\?\/\\\(\)\[\]\{\}\=\+\*\!\@\#\$\%\^\&\<\>]+$/';
	const CropDims='/^(\d+\,){3}\d+$/';
	#
//	const Number = '/^(\-?\d+(\.\d+)?)?$/';
//	const Integer = '/^(\-\d)?\d*$/';
	const Phone = '/^[\d\+]?[\d\s\-]*$/';
	#
//	const DateTime = '/^\d{4}\-\d\d\-\d\d\s\d\d:\d\d:\d\d$/';
	const DateFormat_FullDigit = '/^\d{4}\-\d\d\-\d\d$/';
	const YearFormat_FullDigit = '/^\d{4}$/';
	const Yii_DateFormat_FullDigit = 'yyyy-MM-dd';
	#
//	const HttpRequestHeader_ModifiedSince = '/^[\w\,\s:\+\-]+$/';
//	const Language = '/^[\w\-\s]*$/';
//	const Languages = '/^[\w\-\s\,]*$/';
//	const Language_Label = '<span dir="ltr">Characters : A-z 0-9 _ - [space]</span>';
//	const Location = '/^[\w\s\-\/\.\)\(\'\"]*$/';
//	const Locations = '/^[\w\s\-\,\/\.\)\(\'\"]*$/';
//	const Location_Label = "<span dir=\"ltr\">Characters : A-z 0-9 _ - / . ) ( &apos; &quot; [space]</span>";
//	const CurrecyAbbr = '/^[\w\s\,]+$/';

	#
	const SecureFileExt = 'txt|csv|htm|html|xml|css|doc|docx|xls|rtf|ppt|pdf|swf|flv|avi|wmv|mov|mpg|mpeg|webm|mp4|mov|f4v|mp3|ogg|wav|jpg|jpeg|gif|png|tiff|tif|psd|zip|rar|tar|gzip';
//			'txt|csv|htm|html|xml|css|doc|docx|xls|rtf|ppt|pdf
//			|swf|flv|avi|wmv|mov|mpg|mpeg|webm|mp4|mov|f4v
//			|mp3|ogg|wav
//			|jpg|jpeg|gif|png|tiff|tif|psd
//			|zip|rar|tar|gzip';
//-------------
//	txt|csv|htm|html|xml|css|doc|xls|rtf|ppt|pdf|swf|flv|avi|wmv|mov|jpg|jpeg|gif|png
//	jpg|gif|tiff|png|psd|php|htm|html|css|zip|rar|tar|gzip|txt|doc|docx|xls|xlsx|ppt|pptx|pps|odt|ods|odp|sxw|sxc|sxi|wpd|pdf|rtf|csv|tsv|mp3|ogg|wav|mov|mp4|f4v|flv
//	pdf|doc|docx|xls|csv|txt|rtf|html|zip|mp3|wma|mpg|flv|avi|jpg|jpeg|png|gif|mp4|mp3|ppt|pub|m4a|mov|vts|vob|m4v|m2ts|dvr|264|mp4|flv|bup|mts|psd|png|MOV|mpeg-4
//	txt|csv|css|doc|xls|rtf|ppt|pdf|swf|flv|avi|wmv|mov|jpg|jpeg|gif|png|dwg
	const PictureExt = 'jpg|jpeg|png|gif|ico'; //used for html::PictureUpload
	const VideoExt = 'swf|flv|avi|wmv|mov|mpg|mpeg|webm|mp4|mov|f4v';
	static function FileNameRegexp($Ext = self::SecureFileExt) {
		return '/^.*\.(' . $Ext . ')$/';
	}

}
