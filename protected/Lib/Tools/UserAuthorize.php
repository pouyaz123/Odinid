<?php

/**
 * We are currently using our own initial ACL mode : 
 * <ul>
 * <li>User type(each user type may inherit permissions from his parent)</li>
 * <li>Actions(the different activities)</li>
 * <li>Permissions(connects each user type to multiple actions)</li>
 * </ul>
 * Later as a cg network , we may extend to serve OAuth2 or OASIS XACML too but every thing will happen here.
 * So we keep methods flexible to have the ability of changing our base way or even our database.<br/>
 * i haven't used Yii auth directly in codes because it makes inside codes more complex and depended and less flexible. Later we may use Yii auth here in this class. (alix)
 * @author Abbas Ali Hashemian(alix) <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class UserAuthorize {
	
}
