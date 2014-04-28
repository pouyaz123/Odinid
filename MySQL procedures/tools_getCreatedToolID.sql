--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

DROP FUNCTION IF EXISTS tools_getCreatedToolID;
DROP FUNCTION IF EXISTS tools_getToolTagID;
DELIMITER //
CREATE FUNCTION tools_getCreatedToolID($tool VARCHAR(50))
RETURNS INT(10)	--check your _tools datatable to ensure about identical type
BEGIN
	DECLARE $toolID INT(10);
	DECLARE $tagID CHAR(15);
	SET $toolID = (SELECT `ID` FROM `_tools` WHERE `Tool` = $tool);
	IF ISNULL($toolID) THEN
		INSERT INTO `_tools`(`Tool`) VALUES($tool);

		SET $toolID = LAST_INSERT_ID();
		SET $tagID = tools_getToolTagID($toolID);

		UPDATE `_tools` SET `TagID` = $tagID WHERE ID = $toolID;
		INSERT INTO `_tags`(`TagID`, `Type`) VALUES($tagID, 'Tool');
	END IF;
	RETURN $toolID;
END//
DELIMITER ;

CREATE FUNCTION tools_getToolTagID($toolID INT(10))
RETURNS CHAR(15)	--check your _tools datatable to ensure about identical type
	RETURN CONCAT('sftwr', $toolID);	--check your php consts to be sure about identical prefix if you have
