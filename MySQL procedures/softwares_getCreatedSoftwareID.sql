--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

DROP FUNCTION IF EXISTS softwares_getCreatedSoftwareID;
DROP FUNCTION IF EXISTS softwares_getSoftwareTagID;
DELIMITER //
CREATE FUNCTION softwares_getCreatedSoftwareID($software VARCHAR(50))
RETURNS INT(10)	--check your _softwares datatable to ensure about identical type
BEGIN
	DECLARE $softwareID INT(10);
	DECLARE $tagID CHAR(15);
	SET $softwareID = (SELECT `ID` FROM `_softwares` WHERE `Software` = $software);
	IF ISNULL($softwareID) THEN
		INSERT INTO `_softwares`(`Software`) VALUES($software);

		SET $softwareID = LAST_INSERT_ID();
		SET $tagID = softwares_getSoftwareTagID($softwareID);

		UPDATE `_softwares` SET `TagID` = $tagID WHERE ID = $softwareID;
		INSERT INTO `_tags`(`TagID`, `Type`) VALUES($tagID, 'Software');
	END IF;
	RETURN $softwareID;
END//
DELIMITER ;

CREATE FUNCTION softwares_getSoftwareTagID($softwareID INT(10))
RETURNS CHAR(15)	--check your _softwares datatable to ensure about identical type
	RETURN CONCAT('sftwr', $softwareID);	--check your php consts to be sure about identical prefix if you have
