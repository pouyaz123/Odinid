<?php

namespace ValidationLimits;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-read array $UserPicture in bytes max:1MB
 */
final class User extends Base {

	//---- user
	public $Username = array('min' => 4, 'max' => 32);
	public $Email = array('max' => 100);
	public $Password = array('min' => 4, 'max' => 250);
//	public $ActivationCode = array('max' => 40);
//	public $RecoveryCode = array('max' => 40);
	//---- user info
	public $UserObjective = array('max' => 1000);
	public $UserSmallDesc = array('max' => 255);
	public $UserDescription = array('max' => 2000);

	function getUserPicture() {
		$Ext = \Consts\Regexp::PictureExt;
		return array(
			'minSize' => 5120, 'maxSize' => 1048576, 'maxFiles' => 1,
			'types' => $Ext ? str_replace('|', ',', $Ext) : null,
			'mimeTypes' => $Ext ? 'image/' . str_replace('|', ',image/', $Ext) : null
		);
	}

	//---- totally common
	public $Title = array('max' => 30); //company and artist title and ...
	public $LongTitle = array('max' => 50);
	//---- common for users
	public $WebAddress = array('max' => 250); //company url and user contact web address
	public $FirstName = array('max' => 30); //company contact first name and artist first name
	public $LastName = array('max' => 30); //company contact last name and artist last name
	//---- artist
	public $InvitationCode = array('max' => 50);
	public $MidName = array('max' => 30);
	//---- location
	public $Country = array('max' => 100);
	public $Division = array('max' => 100);
	public $City = array('max' => 200);
	public $Address = array('max' => 255);
	public $PostalCode = array('max' => 30);
	//---- contact
	public $Phone = array('max' => 25);
	public $CustomType = array('max' => 20);//has been used for web address type
	//---- residency
	public $ResidencyVisa = array('max' => 50);
	//---- resume
	public $ExperienceSalaryAmount = array('max' => 99999);
	public $ExperienceTBALayoff = array('max' => 999);
	public $ExperienceRetirementAccountPercent = array('max' => 999);

}
