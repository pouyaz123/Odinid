--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

DROP FUNCTION IF EXISTS edu_getCreatedStudyFieldID;
DELIMITER //
CREATE FUNCTION edu_getCreatedStudyFieldID($studyField VARCHAR(50))
RETURNS INT(10)	--check your _education_studyfields datatable to ensure about identical type
BEGIN
	DECLARE $studyFieldID INT(10);
	SET $studyFieldID = (SELECT `ID` FROM `_education_studyfields` WHERE `StudyField` = $studyField);
	IF ISNULL($studyFieldID) THEN
		INSERT INTO `_education_studyfields`(`StudyField`) VALUES($studyField);
		SET $studyFieldID = LAST_INSERT_ID();
	END IF;
	RETURN $studyFieldID;
END//
DELIMITER ;
