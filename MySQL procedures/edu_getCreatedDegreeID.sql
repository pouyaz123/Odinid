--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

DROP FUNCTION IF EXISTS edu_getCreatedDegreeID;
DELIMITER //
CREATE FUNCTION edu_getCreatedDegreeID($degree VARCHAR(50))
RETURNS INT(10)	--check your _education_degrees datatable to ensure about identical type
BEGIN
	DECLARE $degreeID INT(10);
	SET $degreeID = (SELECT `ID` FROM `_education_degrees` WHERE `Degree` = $degree);
	IF ISNULL($degreeID) THEN
		INSERT INTO `_education_degrees`(`Degree`) VALUES($degree);
		SET $degreeID = LAST_INSERT_ID();
	END IF;
	RETURN $degreeID;
END//
DELIMITER ;
