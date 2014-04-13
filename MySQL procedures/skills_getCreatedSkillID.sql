--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

DROP FUNCTION IF EXISTS skills_getCreatedSkillID;
DROP FUNCTION IF EXISTS skills_getSkillTagID;
DELIMITER //
CREATE FUNCTION skills_getCreatedSkillID($skill VARCHAR(50))
RETURNS INT(10)	--check your _skills datatable to ensure about identical type
BEGIN
	DECLARE $skillID INT(10);
	DECLARE $tagID CHAR(15);
	SET $skillID = (SELECT `ID` FROM `_skills` WHERE `Skill` = $skill);
	IF ISNULL($skillID) THEN
		INSERT INTO `_skills`(`Skill`) VALUES($skill);

		SET $skillID = LAST_INSERT_ID();
		SET $tagID = skills_getSkillTagID($skillID);

		UPDATE `_skills` SET `TagID` = $tagID WHERE ID = $skillID;
		INSERT INTO `_tags`(`TagID`, `Type`) VALUES($tagID, 'Skill');
	END IF;
	RETURN $skillID;
END//
DELIMITER ;

CREATE FUNCTION skills_getSkillTagID($skillID INT(10))
RETURNS CHAR(15)	--check your _skills datatable to ensure about identical type
	RETURN CONCAT('skill', $skillID);	--check your php consts to be sure about identical prefix if you have
