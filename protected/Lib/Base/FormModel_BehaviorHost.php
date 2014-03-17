<?php

namespace Base;

use \Tools as T;
use \Consts as C;

/**
 * Extended \Base\FormModel to host multiple attached CModelBehaviors<br/>
 * Host means this class has required events to accept additional validation rules and attribute labels and ...
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid Portal
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
// * @property-read array $Behaviors the key-value pair list of behaviors(name=>obj)
class FormModel_BehaviorHost extends \Base\FormModel {

	//----------- behaviors and model behaviors -----------//
//	private $_Behaviors = array();
//
//	public function attachBehavior($name, $behavior) {
//		return $this->_Behaviors[$name] = parent::attachBehavior($name, $behavior);
//	}
//
//	public function getBehaviors() {
//		return $this->_Behaviors;
//	}

	//------- events
	public function onBeforeGetXSSPurification(\CEvent $e) {
		$NeedsXSSPurify = false;
		$e->params['NeedsXSSPurify'] = &$NeedsXSSPurify;
		$this->raiseEvent('onBeforeGetXSSPurification', $e);
		return $NeedsXSSPurify;
	}

	/**
	 * Recall this in your overrides to get the result from attached behaviors
	 * @return boolean
	 */
	public function getXSSPurification() {
		return $this->onBeforeGetXSSPurification(new \CEvent($this));
	}

	public function onBeforeXSSPurify_Exceptions(\CEvent $e) {
		$arrXSSExceptions = array();
		$e->params['arrXSSExceptions'] = &$arrXSSExceptions;
		$this->raiseEvent('onBeforeXSSPurify_Exceptions', $e);
		return $arrXSSExceptions;
	}

	/**
	 * Recall this in your overrides to get the result from attached behaviors
	 * @return boolean
	 */
	protected function XSSPurify_Exceptions() {
		return $this->onBeforeXSSPurify_Exceptions(new \CEvent($this));
	}

	public function onBeforeXSSPurify_Attrs(\CEvent $e) {
		$arrXSSAttrs = array();
		$e->params['arrXSSAttrs'] = &$arrXSSAttrs;
		$this->raiseEvent('onBeforeXSSPurify_Attrs', $e);
		return $arrXSSAttrs;
	}

	/**
	 * Recall this in your overrides to get the result from attached behaviors
	 * @return boolean
	 */
	protected function XSSPurify_Attrs() {
		return $this->onBeforeXSSPurify_Attrs(new \CEvent($this));
	}

	public function onBeforeRules(\CEvent $e) {
		$arrRules = array();
		$e->params['arrRules'] = &$arrRules;
		$this->raiseEvent('onBeforeRules', $e);
		return $arrRules;
	}

	/**
	 * Recall this in your overrides to get the result from attached behaviors
	 * @return boolean
	 */
	public function rules() {
		return $this->onBeforeRules(new \CEvent($this));
	}

	public function onBeforeAttributeLabels(\CEvent $e) {
		$arrAttrLabels = array();
		$e->params['arrAttrLabels'] = &$arrAttrLabels;
		$this->raiseEvent('onBeforeAttributeLabels', $e);
		return $arrAttrLabels;
	}

	/**
	 * Recall this in your overrides to get the result from attached behaviors
	 * @return boolean
	 */
	public function attributeLabels() {
		return $this->onBeforeAttributeLabels(new \CEvent($this));
	}

}
