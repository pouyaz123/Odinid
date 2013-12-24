--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

DROP PROCEDURE IF EXISTS geo_getGeoLocationIDs;
DELIMITER //
CREATE PROCEDURE geo_getGeoLocationIDs(
--gets VARCHAR or NULL by the first 3 params
	IN $country VARCHAR(100)
	, IN $division VARCHAR(100)
	, IN $city VARCHAR(200)
--sets the last 4 params if the 3 first params were valid
	, OUT $countryISO2 CHAR(2)
	, OUT $divisionCombined VARCHAR(20)
	, OUT $divisionCode VARCHAR(20)
	, OUT $cityGeonameID INT(10) UNSIGNED
)
BEGIN
	--default values
	SET $countryISO2=NULL;
	SET $divisionCombined=NULL;
	SET $divisionCode=NULL;
	SET $cityGeonameID=NULL;

	IF NOT ISNULL($country) AND $country!='' THEN
		SELECT `ISO2` INTO $countryISO2
		FROM `_geo_countries`
		WHERE `ISO2`=$country OR `AsciiName`=$country
		LIMIT 1;
	END IF;
	
	IF NOT ISNULL($countryISO2) AND NOT ISNULL($division) AND $division!='' THEN
		SELECT `CombinedCode`, `DivisionCode`
		INTO $divisionCombined, $divisionCode
		FROM `_geo_divisions`
		WHERE `CountryISO2`=$countryISO2 AND (`CombinedCode`=$division OR `AsciiName`=$division)
		LIMIT 1;
	END IF;

	IF NOT ISNULL($divisionCombined) AND NOT ISNULL($city) AND $city!='' THEN
		SELECT `GeonameID` INTO $cityGeonameID
		FROM `_geo_cities`
		WHERE `CountryISO2`=$countryISO2
			AND (`DivisionCode`=$divisionCode OR ISNULL(`DivisionCode`) OR `DivisionCode`='')
			AND (`GeonameID`=$city OR `AsciiName`=$city)
		LIMIT 1;
	END IF;

END//
DELIMITER ;