<?php

namespace Consts;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 1
 * @copyright (c) Abbas Ali Hashemian
 * @access public
 */
final class Regexp {

	const URL = '/^http(s)?\:\/\/\w+(\.\w+)*(\.\w{2,})+[^\s]$/' #	http://asd.c is wrong but http://asd.co is ok
			, URL_Local = '/^http(s)\?:\/\/\w+(\.\w+)*[^\s]?$/';
#	
	const Username = '/^[A-Za-z][\w\.]*$/';
	const Email = '/^\w+([\.\-]\w+)*@\w+([\.\-]\w+)*\.\w{2,}$/';
	const LogicName = '/^\w*$/';
//	const Password = '/^.{4,}$/';
	const Password_MinLength = 4;
	const Number = '/^(\-?\d+(\.\d+)?)?$/';
	const Integer = '/^(\-\d)?\d*$/';
	const Phone = '/^[\d\+]?[\d\s\-]*$/';
	const DateTime = '/^\d\d\d\d\-\d\d\-\d\d\s\d\d:\d\d:\d\d$/';
	const HttpRequestHeader_ModifiedSince = '/^[\w\,\s:\+\-]+$/';
//	const Language = '/^[\w\-\s]*$/';
//	const Languages = '/^[\w\-\s\,]*$/';
//	const Language_Label = '<span dir="ltr">Characters : A-z 0-9 _ - [space]</span>';
//	const Location = '/^[\w\s\-\/\.\)\(\'\"]*$/';
//	const Locations = '/^[\w\s\-\,\/\.\)\(\'\"]*$/';
//	const Location_Label = "<span dir=\"ltr\">Characters : A-z 0-9 _ - / . ) ( &apos; &quot; [space]</span>";
	const CurrecyAbbr='/^[\w\s\,]+$/';
	const ASCIIChars_Simple='/^[\w\s\-\,\.\:\"\'\`\~\?\/\\\(\)\[\]\{\}\=\+\*\!\@\#\$\%\^\&\<\>]+$/';
//KASP
	const NoComma = '/^[^,]*$/';
	const NoComma_Req = '/^[^,]+$/';
//	const AuctionItemSpecifications='/^[\w\s\;\:\(\)\.\-]*$/';
//	const AuctionItemSpecifications_Label = '<span dir="ltr">Characters : A-z 0-9 _ - : ; ) ( . [space]</span>';
#
	const Username_MinLen = 4;
	const Username_MaxLen = 32;
	#
	const Location_EachMaxChars = 100;
	const Location_MaxChars = 300;
	#
	const Currency_MaxChars = 30;
	#
	const Language_EachMaxChars = 25;
	const Language_MaxChars = 125;
	#
//KASP
	const Branch_EachMaxChars = 100;
	const Branch_MaxChars = 300;
	const Skill_EachMaxChars = 30;
	const Skill_MaxChars = 150;
	const AuctionItemSpecifications_MaxChars = 50;
	#msging
	const MsgingDepr_MaxChance = 30;
	const MsgingDepr_LockHours = 168;
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

?>
