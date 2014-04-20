--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

DROP FUNCTION IF EXISTS institutions_getCreatedInstitutionID;

DELIMITER //
CREATE FUNCTION institutions_getCreatedInstitutionID(
	$institutionID INT(10)
	, $title VARCHAR(30)
	, $domain VARCHAR(75)
	, $domainLikeEscaped VARCHAR(75)
	, $url VARCHAR(250)
)
RETURNS INT(10)	--check your _institutions datatable to ensure about identical type
BEGIN
	SET $institutionID = (SELECT `ID` FROM `_institutions` WHERE `ID` = $institutionID
		OR (
			`Title` = $title
			AND (
				`Domain` = $domain
				OR `Domain` LIKE CONCAT('%', $domainLikeEscaped) ESCAPE '='
				OR $domain LIKE CONCAT('%', `Domain`) ESCAPE '='
			)
		) LIMIT 1
	);
-- ESCAPE char is same as what we have in T\DB::LikeEscapeChar has been used in T\DB::EscapeLikeWildCards
	IF ISNULL($institutionID) THEN
		INSERT INTO `_institutions`(`Title`, `Domain`, `URL`) VALUES($title, $domain, $url);

		SET $institutionID = LAST_INSERT_ID();
	END IF;
	RETURN $institutionID;
END//
DELIMITER ;
