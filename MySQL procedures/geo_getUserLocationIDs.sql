--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com
--requires geo_UserFncPackage

--To get valid user location IDs
DROP PROCEDURE IF EXISTS geo_getUserLocationIDs;
DELIMITER //
CREATE PROCEDURE geo_getUserLocationIDs(
--gets the user inputs by the first 3 params
	IN $country VARCHAR(100)
	, IN $division VARCHAR(100)
	, IN $city VARCHAR(200)
--gets geoname valid location vars(of CALL geo_getValidLocations) by the middle 3 params
	, IN $countryISO2 CHAR(2)
	, IN $divisionCombined VARCHAR(20)
-- 	, IN $divisionCode VARCHAR(20)
	, IN $cityGeonameID INT(10) UNSIGNED
--saves(inserts) if the user inputs was not valid geoname locations
--sets the last 3 params with the IDs of saved user inputs
	, OUT $userCountryID INT(10) UNSIGNED
	, OUT $userDivisionID INT(10) UNSIGNED
	, OUT $userCityID INT(10) UNSIGNED
)
BEGIN
	SET $userCountryID = NULL;
	SET $userDivisionID = NULL;
	SET $userCityID = NULL;

	IF ISNULL($countryISO2) AND NOT ISNULL($country) AND $country!='' THEN
		SET $userCountryID:=geo_getUserCountryID($country);
		IF ISNULL($userCountryID) THEN
			INSERT INTO _geo_user_countries(Country) VALUES($country);
		END IF;
		SET $userCountryID:=geo_getUserCountryID($country);
	END IF;

	IF ISNULL($divisionCombined) AND NOT ISNULL($division) AND $division!='' THEN
		SET $userDivisionID:=geo_getUserDivisionID($division);
		IF ISNULL($userDivisionID) THEN
			INSERT INTO _geo_user_divisions(Division, UserCountryID, CountryISO2) VALUES($division, $userCountryID, $countryISO2);
		END IF;
		SET $userDivisionID:=geo_getUserDivisionID($division);
	END IF;

	IF ISNULL($cityGeonameID) AND NOT ISNULL($city) AND $city!='' THEN
		SET $userCityID:=geo_getUserCityID($city);
		IF ISNULL($userCityID) THEN
			INSERT INTO _geo_user_cities(City, UserDivisionID, DivisionCombinedCode) VALUES($city, $userDivisionID, $divisionCombined);
		END IF;
		SET $userCityID:=geo_getUserCityID($city);
	END IF;

END//
DELIMITER ;