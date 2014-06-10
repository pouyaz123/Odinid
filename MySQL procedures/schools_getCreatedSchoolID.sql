--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

DROP FUNCTION IF EXISTS schools_getCreatedSchoolID;
-- DROP FUNCTION IF EXISTS schools_getCreatedSchoolTagID;
DELIMITER //
CREATE FUNCTION schools_getCreatedSchoolID(
	$schoolID INT(10)
	, $title VARCHAR(30)
	, $domain VARCHAR(75)
	, $domainLikeEscaped VARCHAR(75)
	, $url VARCHAR(250)
)
RETURNS INT(10)	--check your _school_info datatable to ensure about identical type
BEGIN
-- 	DECLARE $schoolID INT(10);
-- 	DECLARE $tagID CHAR(17);
	SET $schoolID = (SELECT `ID` FROM `_school_info` WHERE `ID` <=> $schoolID
		OR (
			`Title` = $title
			AND (
				`Domain` <=> $domain
				OR `Domain` LIKE CONCAT('%', $domainLikeEscaped) ESCAPE '='
				OR $domain LIKE CONCAT('%', `Domain`) ESCAPE '='
			)
		) LIMIT 1
	);
-- ESCAPE char is same as what we have in T\DB::LikeEscapeChar has been used in T\DB::EscapeLikeWildCards
	IF ISNULL($schoolID) THEN
		INSERT INTO `_school_info`(`Title`, `Domain`, `URL`) VALUES($title, $domain, $url);

		SET $schoolID = LAST_INSERT_ID();
-- 		SET $tagID = schools_getCreatedSchoolTagID($schoolID);
-- 
-- 		UPDATE `_school_info` SET `TagID` = $tagID WHERE ID = $schoolID;
-- 		INSERT INTO `_tags`(`TagID`, `Type`) VALUES($tagID, 'School');
	END IF;
	RETURN $schoolID;
END//
DELIMITER ;

-- CREATE FUNCTION schools_getCreatedSchoolTagID($schoolID INT(10))
-- RETURNS CHAR(17)	--check your _school_info datatable to ensure about identical type
-- 	RETURN CONCAT('school', $schoolID);	--check your php consts to be sure about identical prefix if you have
