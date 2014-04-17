--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

DROP FUNCTION IF EXISTS langs_getCreatedLangID;
-- DROP FUNCTION IF EXISTS langs_getLangTagID;
DELIMITER //
CREATE FUNCTION langs_getCreatedLangID($lang VARCHAR(50))
RETURNS INT(10)	--check your _languages datatable to ensure about identical type
BEGIN
	DECLARE $langID INT(10);
-- 	DECLARE $tagID CHAR(15);
	SET $langID = (SELECT `ID` FROM `_languages` WHERE `Language` = $lang);
	IF ISNULL($langID) THEN
		INSERT INTO `_languages`(`Language`) VALUES($lang);

		SET $langID = LAST_INSERT_ID();
-- 		SET $tagID = langs_getLangTagID($langID);

-- 		UPDATE `_languages` SET `TagID` = $tagID WHERE ID = $langID;
-- 		INSERT INTO `_tags`(`TagID`, `Type`) VALUES($tagID, 'Language');	--Language type has not been added to db yet(requires Pouya decision)
	END IF;
	RETURN $langID;
END//
DELIMITER ;

-- CREATE FUNCTION langs_getLangTagID($langID INT(10))
-- RETURNS CHAR(15)	--check your _languages datatable to ensure about identical type
-- 	RETURN CONCAT('language', $langID);	--check your php consts to be sure about identical prefix if you have
