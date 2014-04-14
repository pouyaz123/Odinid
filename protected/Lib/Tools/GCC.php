<?php

namespace Tools;

use \Tools as T;
use \Consts as C;

/**
 * Garbage Collection Cleaner (GCC)
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid cg network
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class GCC {

//	private static $WindowsPHPPath = 'd:/xampp/php/php';
//	private static $LinuxPHPPath = 'php';
//
//	private static function getPHP() {
//		return isset($_SERVER['WINDIR']) && stripos($_SERVER['WINDIR'], 'windows') !== false ?
//				self::$WindowsPHPPath :
//				self::$LinuxPHPPath;
//	}
	//86400 seconds = 24hours
	const GCCPeriod = 86400;

	//172800 seconds = 48hours
//	const MediumGCCPeriod = 172800;

	/**
	 * cleans expired user recoveries/activations periodically
	 * mytodo 1: multithread execution for gcc to have parallel processes
	 */
//	static function UserRecoveries() {
////		ignore_user_abort(true);
////		set_time_limit(0);
////		$pipe = popen(self::getPHP() . ' "' . __DIR__ . '/GCCThreads/UserRecoveries.php" asd 2>&1', 'r');
//////		sleep(1500);
////		stream_set_blocking($pipe, 0);
////		\Err::DebugBreakPoint(fgets($pipe, 1024), 0);
////		pclose($pipe);
////		ignore_user_abort(false);
////		if (connection_status() != CONNECTION_NORMAL)
////			exit;
////		exit;
////		set_time_limit(0);
////			ignore_user_abort(true);
////echo file_get_contents('http://www.google.com');
////flush();ob_flush();
//////sleep(20000);
////			ignore_user_abort(false);
////			if (connection_status() != CONNECTION_NORMAL)
////				exit;
////			exit();
//		if (!T\Cache::rabbitCache()->get('GCC_UserRecoveries')) {
//			ignore_user_abort(true);
////			T\DB::Execute("DELETE FROM `_user_recoveries`"
////					. " WHERE `TimeStamp`<:time AND `Type`!=:activation" //to avoid wild user accounts(like the c++ wild pointers)
////					, array(
////				':time' => time() - (T\Settings::GetValue('ActivationLink_LifeTime') * 60 * 60),
////				':activation' => C\User::Recovery_Activation,
////					)
////			);
//			T\Cache::rabbitCache()->set('GCC_UserRecoveries', 1, self::GCCPeriod);
//			ignore_user_abort(false);
//			if (connection_status() != CONNECTION_NORMAL)
//				exit;
//		}
//	}

	static function RogueSkills() {
		if (!T\Cache::rabbitCache()->get('GCC_Skills')) {
			ignore_user_abort(true);
			$drWhereClause = T\DB::GetRow("SELECT "
							. " GROUP_CONCAT(DISTINCT skl.ID SEPARATOR ' OR ID=') AS IDs"
							. ", GROUP_CONCAT(DISTINCT skl.TagID SEPARATOR '\" OR TagID=\"') AS TagIDs"
							. " FROM `_skills` skl"
							. " INNER JOIN (SELECT 1) tmp ON NOT skl.`IsOfficial`"
							. " LEFT JOIN (SELECT DISTINCT `SkillID` FROM `_user_skills`) uskl"
							. " ON uskl.SkillID=skl.`ID`"
							. " WHERE ISNULL(uskl.SkillID)");
			if ($drWhereClause && $drWhereClause['IDs']) {
				$arrTrans = array();
				$arrTrans[] = "DELETE FROM `_skills` WHERE ID=" . $drWhereClause['IDs'];
				if ($drWhereClause['TagIDs'])
					$arrTrans[] = "DELETE FROM `_tags` WHERE TagID=\"" . $drWhereClause['TagIDs'] . "\"";
				T\DB::Transaction($arrTrans);
			}
			T\Cache::rabbitCache()->set('GCC_Skills', 1, self::GCCPeriod);
			ignore_user_abort(false);
			if (connection_status() != CONNECTION_NORMAL)
				exit;
		}
	}

}

?>
