<?php

namespace Tools;

/**
 * Provides the right supported cache components of Yii
 * Also provides return types of the installed cache components to have a right intelli sense(code completion)
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid cg network
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class Cache {

	/**
	 * Be careful : CApcCache & CFileCache may have different capabilities(memebers,props,methods)
	 * @return CApcCache|CFileCache|\CCache
	 */
	static function rabbitCache() {
		if (\Yii::app()->hasComponent('rabbitCache'))
			return \Yii::app()->rabbitCache;
		if (\Yii::app()->hasComponent('fileCache'))
			return \Yii::app()->fileCache;
	}

	/**
	 * @return CFileCache
	 */
	static function fileCache() {
		return \Yii::app()->fileCache;
	}

}

?>
