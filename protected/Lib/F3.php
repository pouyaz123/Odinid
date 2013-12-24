<?php

/**
 * Tondarweb framework(namedin.com, tondarweb.com)(R) is a high level framework implemented on top of F3(Fat Free Framework(R) by Bong Cosca on github and source forge)<br/>
 * Some tools of Tondarweb framework are migrated to Odinid by me(Abbas Ali Hashemian) legitimately but F3 is not migrated<br/>
 * Here F3 is absent and this is only my simulator of F3 basis to provide required base for my framework<br/>
 * -------------F3 original description and copyright:<br/>
 * PHP Fat-Free Framework<br/>
 * The contents of this file are subject to the terms of the GNU General<br/>
 * Public License Version 3.0. You may not use this file except in<br/>
 * compliance with the license. Any of the license terms and conditions<br/>
 * can be waived if you get permission from the copyright holder.<br/>
 * Copyright (c) 2009-2011 F3::Factory<br/>
 * Bong Cosca <bong.cosca@yahoo.com><br/>
 * 	package Base<br/>
 * 	version 2.0.9<br/>
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 1
 * @access public
 */
final class F3 {

	static $vars = array();

	static function set($name, $value) {
		self::$vars[$name] = $value;
	}

	static function get($name) {
		return self::exists($name) ? self::$vars[$name] : null;
	}

	static function exists($name) {
		return isset(self::$vars[$name]);
	}

	static function concat($name, $value) {
		if (!self::exists($name))
			self::set($name, $value);
		else
			self::set($name, self::get($name) . $value);
	}

	/**
	  Retrieve HTTP headers
	 * @author Bong Cosca <bong.cosca@yahoo.com>
	  @return array
	  @public
	 * */
	static function headers() {
		if (PHP_SAPI != 'cli') {
			if (function_exists('getallheaders'))
			// Apache server
				return getallheaders();
			// Workaround
			$req = array();
			foreach ($_SERVER as $key => $val)
				if (substr($key, 0, 5) == 'HTTP_')
					$req[strtr(ucwords(strtolower(
													strtr(substr($key, 5), '_', ' '))), ' ', '-')] = $val;
			return $req;
		}
		return array();
	}

}

