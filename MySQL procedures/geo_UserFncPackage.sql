--@Copyright: Odinid
--@author: Abbas Ali Hashemian tondarweb@gmail.com - webdesignir.com

--To get user country ID
DROP FUNCTION IF EXISTS geo_getUserCountryID;
CREATE FUNCTION geo_getUserCountryID($country VARCHAR(100))
RETURNS INT(10)
	RETURN (SELECT ID FROM _geo_user_countries WHERE Country=$country LIMIT 1);

--To get user division ID
DROP FUNCTION IF EXISTS geo_getUserDivisionID;
CREATE FUNCTION geo_getUserDivisionID($division VARCHAR(100))
RETURNS INT(10)
	RETURN (SELECT ID FROM _geo_user_divisions WHERE Division=$division LIMIT 1);

--To get user city ID
DROP FUNCTION IF EXISTS geo_getUserCityID;
CREATE FUNCTION geo_getUserCityID($city VARCHAR(100))
RETURNS INT(10)
	RETURN (SELECT ID FROM _geo_user_cities WHERE City=$city LIMIT 1);