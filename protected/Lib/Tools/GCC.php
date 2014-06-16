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
	const GCCPeriod = 3600;

	//172800 seconds = 48hours
//	const MediumGCCPeriod = 172800;

	/**
	 * cleans expired user recoveries/activations periodically
	 * mytodo 1: multithread execution for gcc to have parallel processes
	 */
	static function UserRecoveries() {
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
////				':time' => time() - (T\Settings::GetInstance()->ActivationLink_LifeTime * 60 * 60),
////				':activation' => C\User::Recovery_Activation,
////					)
////			);
//			T\Cache::rabbitCache()->set('GCC_UserRecoveries', 1, self::GCCPeriod);
//			ignore_user_abort(false);
//			if (connection_status() != CONNECTION_NORMAL)
//				exit;
//		}
	}

	private static function RogueCacheHandler($fncJob, $GCCCacheName, $GCCCachePeriod = self::GCCPeriod) {
		if (!T\Cache::rabbitCache()->get($GCCCacheName)) {
			ignore_user_abort(true);
			$fncJob();
			T\Cache::rabbitCache()->set($GCCCacheName, 1, $GCCCachePeriod);
			ignore_user_abort(false);
			if (connection_status() != CONNECTION_NORMAL)
				exit;
		}
	}

	static function RogueSkills() {
		self::RogueCacheHandler(function() {
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
					$arrTrans[] = "DELETE FROM `_tags` WHERE TagID=\"" . $drWhereClause['TagIDs'] . "\" AND `Type`='Skill'";
				T\DB::Transaction($arrTrans);
			}
		}, 'GCC_Skills');
	}

	static function RogueLanguages() {
		self::RogueCacheHandler(function() {
			$drWhereClause = T\DB::GetRow("SELECT "
							. " GROUP_CONCAT(DISTINCT lng.ID SEPARATOR ' OR ID=') AS IDs"
//							. ", GROUP_CONCAT(DISTINCT lng.TagID SEPARATOR '\" OR TagID=\"') AS TagIDs"
							. " FROM `_languages` lng"
							. " INNER JOIN (SELECT 1) tmp ON NOT lng.`IsOfficial`"
							. " LEFT JOIN (SELECT DISTINCT `LangID` FROM `_user_langs`) ulng"
							. " ON ulng.LangID=lng.`ID`"
							. " WHERE ISNULL(ulng.LangID)");
			if ($drWhereClause && $drWhereClause['IDs']) {
				$arrTrans = array();
				$arrTrans[] = "DELETE FROM `_languages` WHERE ID=" . $drWhereClause['IDs'];
//				if ($drWhereClause['TagIDs'])
//					$arrTrans[] = "DELETE FROM `_tags` WHERE TagID=\"" . $drWhereClause['TagIDs'] . "\" AND `Type`='Language'";	//Language type has not been added to db yet(requires Pouya decision)
				T\DB::Transaction($arrTrans);
			}
		}, 'GCC_Languages');
	}

	static function RogueWorkFields() {
		self::RogueCacheHandler(function() {
			$drWhereClause = T\DB::GetRow("SELECT "
							. " GROUP_CONCAT(DISTINCT wfld.ID SEPARATOR ' OR ID=') AS IDs"
							. ", GROUP_CONCAT(DISTINCT wfld.TagID SEPARATOR '\" OR TagID=\"') AS TagIDs"
							. " FROM `_workfields` wfld"
							. " INNER JOIN (SELECT 1) tmp ON NOT wfld.`IsOfficial`"
							. " LEFT JOIN (SELECT DISTINCT `WorkFieldID` FROM `_user_workfields`) uwfld"
							. " ON uwfld.WorkFieldID=wfld.`ID`"
							. " WHERE ISNULL(uwfld.WorkFieldID)");
			if ($drWhereClause && $drWhereClause['IDs']) {
				$arrTrans = array();
				$arrTrans[] = "DELETE FROM `_workfields` WHERE ID=" . $drWhereClause['IDs'];
				if ($drWhereClause['TagIDs'])
					$arrTrans[] = "DELETE FROM `_tags` WHERE TagID=\"" . $drWhereClause['TagIDs'] . "\" AND `Type`='WorkField'";
				T\DB::Transaction($arrTrans);
			}
		}, 'GCC_WorkFields');
	}

	static function RogueTools() {
		self::RogueCacheHandler(function() {
			$drWhereClause = T\DB::GetRow("SELECT "
							. " GROUP_CONCAT(DISTINCT sft.ID SEPARATOR ' OR ID=') AS IDs"
							. ", GROUP_CONCAT(DISTINCT sft.TagID SEPARATOR '\" OR TagID=\"') AS TagIDs"
							. " FROM `_tools` sft"
							. " INNER JOIN (SELECT 1) tmp ON NOT sft.`IsOfficial`"
							. " LEFT JOIN (SELECT DISTINCT `ToolID` FROM `_user_tools`) usft"
							. " ON usft.ToolID=sft.`ID`"
							. " WHERE ISNULL(usft.ToolID)");
			if ($drWhereClause && $drWhereClause['IDs']) {
				$arrTrans = array();
				$arrTrans[] = "DELETE FROM `_tools` WHERE ID=" . $drWhereClause['IDs'];
				if ($drWhereClause['TagIDs'])
					$arrTrans[] = "DELETE FROM `_tags` WHERE TagID=\"" . $drWhereClause['TagIDs'] . "\" AND `Type`='Tool'";
				T\DB::Transaction($arrTrans);
			}
		}, 'GCC_Tools');
	}

	static function RogueInstitutions() {
		self::RogueCacheHandler(function() {
			$drWhereClause = T\DB::GetRow("SELECT "
							. " GROUP_CONCAT(DISTINCT inst.ID SEPARATOR ' OR ID=') AS IDs"
							. " FROM `_institutions` inst"
							. " INNER JOIN (SELECT 1) tmp ON NOT inst.`Verified`"
							. " LEFT JOIN (SELECT DISTINCT `InstitutionID` FROM `_user_certificates`) cert"
							. " ON cert.InstitutionID=inst.`ID`"
							. " WHERE ISNULL(cert.InstitutionID)");
			if ($drWhereClause && $drWhereClause['IDs']) {
				$arrTrans = array();
				$arrTrans[] = "DELETE FROM `_institutions` WHERE ID=" . $drWhereClause['IDs'];
				T\DB::Transaction($arrTrans);
			}
		}, 'GCC_Institutions');
	}

	static function RogueOrganizations() {
		self::RogueCacheHandler(function() {
			$drWhereClause = T\DB::GetRow("SELECT "
							. " GROUP_CONCAT(DISTINCT org.ID SEPARATOR ' OR ID=') AS IDs"
							. " FROM `_organizations` org"
							. " INNER JOIN (SELECT 1) tmp ON NOT org.`Verified`"
							. " LEFT JOIN (SELECT DISTINCT `OrganizationID` FROM `_user_awards`) uawrd"
							. " ON uawrd.OrganizationID=org.`ID`"
							. " WHERE ISNULL(uawrd.OrganizationID)");
			if ($drWhereClause && $drWhereClause['IDs']) {
				$arrTrans = array();
				$arrTrans[] = "DELETE FROM `_organizations` WHERE ID=" . $drWhereClause['IDs'];
				T\DB::Transaction($arrTrans);
			}
		}, 'GCC_Organizations');
	}

	static function RogueCompanies() {
		self::RogueCacheHandler(function() {
			$drWhereClause = T\DB::GetRow("SELECT "
							. " GROUP_CONCAT(DISTINCT comp.ID SEPARATOR ' OR ID=') AS IDs"
							. " FROM `_company_info` comp"
							. " INNER JOIN (SELECT 1) tmp ON NOT comp.`Verified` AND ISNULL(`OwnerUID`)"
							. " LEFT JOIN (SELECT DISTINCT `CompanyID` FROM `_user_experiences`) uexp"
							. " ON uexp.CompanyID=comp.`ID`"
							. " WHERE ISNULL(uexp.CompanyID)");
			if ($drWhereClause && $drWhereClause['IDs']) {
				$arrTrans = array();
				$arrTrans[] = "DELETE FROM `_company_info` WHERE `ID`=" . $drWhereClause['IDs'];
				T\DB::Transaction($arrTrans);
			}
		}, 'GCC_Companies');
	}

//mytodo 1: GCC clean unused unoccupied institutions, companies, schools and organizations
//mytodo 1: GCC clean unused unoccupied edu_degrees, edu_studyfields
//mytodo 1: GCC clean unused unofficial countries, divisions and cities
}

?>
