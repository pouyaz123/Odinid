--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

DROP FUNCTION IF EXISTS companies_getCreatedCompanyID;
-- DROP FUNCTION IF EXISTS companies_getCreatedCompanyTagID;
DELIMITER //
CREATE FUNCTION companies_getCreatedCompanyID(
	$companyID INT(10)
	, $title VARCHAR(30)
	, $domain VARCHAR(75)
	, $domainLikeEscaped VARCHAR(75)
	, $url VARCHAR(250)
)
RETURNS INT(10)	--check your _company_info datatable to ensure about identical type
BEGIN
-- 	DECLARE $companyID INT(10);
-- 	DECLARE $tagID CHAR(17);
	SET $companyID = (SELECT `ID` FROM `_company_info` WHERE `ID` <=> $companyID
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
	IF ISNULL($companyID) THEN
		INSERT INTO `_company_info`(`Title`, `Domain`, `URL`) VALUES($title, $domain, $url);

		SET $companyID = LAST_INSERT_ID();
-- 		SET $tagID = companies_getCreatedCompanyTagID($companyID);
-- 
-- 		UPDATE `_company_info` SET `TagID` = $tagID WHERE ID = $companyID;
-- 		INSERT INTO `_tags`(`TagID`, `Type`) VALUES($tagID, 'Company');
	END IF;
	RETURN $companyID;
END//
DELIMITER ;

-- CREATE FUNCTION companies_getCreatedCompanyTagID($companyID INT(10))
-- RETURNS CHAR(17)	--check your _company_info datatable to ensure about identical type
-- 	RETURN CONCAT('company', $companyID);	--check your php consts to be sure about identical prefix if you have
