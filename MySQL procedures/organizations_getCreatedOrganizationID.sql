--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

DROP FUNCTION IF EXISTS organizations_getCreatedOrganizationID;

DELIMITER //
CREATE FUNCTION organizations_getCreatedOrganizationID(
	$organizationID INT(10)
	, $title VARCHAR(30)
	, $domain VARCHAR(75)
	, $domainLikeEscaped VARCHAR(75)
	, $url VARCHAR(250)
)
RETURNS INT(10)	--check your _organizations datatable to ensure about identical type
BEGIN
	SET $organizationID = (SELECT `ID` FROM `_organizations` WHERE `ID` = $organizationID
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
	IF ISNULL($organizationID) THEN
		INSERT INTO `_organizations`(`Title`, `Domain`, `URL`) VALUES($title, $domain, $url);

		SET $organizationID = LAST_INSERT_ID();
	END IF;
	RETURN $organizationID;
END//
DELIMITER ;
