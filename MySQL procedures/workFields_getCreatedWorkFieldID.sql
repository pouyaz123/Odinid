--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

DROP FUNCTION IF EXISTS workFields_getCreatedWorkFieldID;
DROP FUNCTION IF EXISTS workFields_getWorkFieldTagID;
DELIMITER //
CREATE FUNCTION workFields_getCreatedWorkFieldID($workField VARCHAR(50))
RETURNS INT(10)	--check your _workfields datatable to ensure about identical type
BEGIN
	DECLARE $workFieldID INT(10);
	DECLARE $tagID CHAR(15);
	SET $workFieldID = (SELECT `ID` FROM `_workfields` WHERE `WorkField` = $workField);
	IF ISNULL($workFieldID) THEN
		INSERT INTO `_workfields`(`WorkField`) VALUES($workField);

		SET $workFieldID = LAST_INSERT_ID();
		SET $tagID = workFields_getWorkFieldTagID($workFieldID);

		UPDATE `_workfields` SET `TagID` = $tagID WHERE ID = $workFieldID;
		INSERT INTO `_tags`(`TagID`, `Type`) VALUES($tagID, 'WorkField');
	END IF;
	RETURN $workFieldID;
END//
DELIMITER ;

CREATE FUNCTION workFields_getWorkFieldTagID($workFieldID INT(10))
RETURNS CHAR(15)	--check your _workfields datatable to ensure about identical type
	RETURN CONCAT('wrkfld', $workFieldID);	--check your php consts to be sure about identical prefix if you have
