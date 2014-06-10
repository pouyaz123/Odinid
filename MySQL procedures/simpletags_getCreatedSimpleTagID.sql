--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

DROP FUNCTION IF EXISTS simpletags_getCreatedSimpleTagID;
DROP FUNCTION IF EXISTS simpletags_getSimpleTagTagID;
DELIMITER //
CREATE FUNCTION simpletags_getCreatedSimpleTagID($simpletag VARCHAR(50))
RETURNS INT(10)	--check your _simpletags datatable to ensure about identical type
BEGIN
	DECLARE $simpletagID INT(10);
	DECLARE $tagID CHAR(15);
	SET $simpletagID = (SELECT `ID` FROM `_simpletags` WHERE `Tag` = $simpletag);
	IF ISNULL($simpletagID) THEN
		INSERT INTO `_simpletags`(`Tag`) VALUES($simpletag);

		SET $simpletagID = LAST_INSERT_ID();
		SET $tagID = simpletags_getSimpleTagTagID($simpletagID);

		UPDATE `_simpletags` SET `TagID` = $tagID WHERE ID = $simpletagID;
		INSERT INTO `_tags`(`TagID`, `Type`) VALUES($tagID, 'Tag');
	END IF;
	RETURN $simpletagID;
END//
DELIMITER ;

CREATE FUNCTION simpletags_getSimpleTagTagID($simpletagID INT(10))
RETURNS CHAR(15)	--check your _simpletags datatable to ensure about identical type
	RETURN CONCAT('tag', $simpletagID);	--check your php consts to be sure about identical prefix if you have
