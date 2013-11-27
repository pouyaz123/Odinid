<?php

namespace Consts;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 2
 * @copyright (c) Abbas Ali Hashemian
 * @access public
 */
final class Calendars {
	#----------------- CALENDAR TYPES -----------------#

	static $Types = array(
		1 => array('TITLE' => '{{@res_GregorianCalendar}}', 'NAME' => 'GREGORIAN', 'ID' => 1)
		, 2 => array('TITLE' => '{{@res_SolarHejiraCalendar}}', 'NAME' => 'SOLARHEJIRA', 'ID' => 2)
//		, 3 => array('TITLE' => '{{@res_AchaemenianPersianCalendar}}', 'NAME' => 'ACHAEMENIANPERSIAN', 'ID' => 2)
//		, 4 => array('TITLE' => '{{@res_ZoroastrianPersianCalendar}}', 'NAME' => 'ZOROASTRIANPERSIAN', 'ID' => 3)
	);

	static function GetCalendarType_ByName($Name, $Field = NULL) {
		$result = array_filter(self::$Types, create_function('$dr', "return \$dr['NAME']=='$Name';"));
		$result = array_shift($result);
		if ($Field)
			return $result[$Field];
		return $result;
	}

}

?>
