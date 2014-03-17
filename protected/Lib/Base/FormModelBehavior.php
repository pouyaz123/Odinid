<?php

namespace Base;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-read FormModel_BehaviorHost $owner The owner model that this behavior is attached to.
 */
class FormModelBehavior extends \CModelBehavior {

	/**
	 * Declares events and the corresponding event handler methods.
	 * The default implementation returns 'onAfterConstruct', 'onBeforeValidate' and 'onAfterValidate' events and handlers.
	 * If you override this method, make sure you merge the parent result to the return value.
	 * @return array events (array keys) and the corresponding event handler methods (array values).
	 * @see CBehavior::events
	 */
	public function events() {
		return array_merge(parent::events(), array(
			'onBeforeGetXSSPurification' => 'onBeforeGetXSSPurification',
			'onBeforeXSSPurify_Exceptions' => 'onBeforeXSSPurify_Exceptions',
			'onBeforeXSSPurify_Attrs' => 'onBeforeXSSPurify_Attrs',
			'onBeforeRules' => 'onBeforeRules',
			'onBeforeAttributeLabels' => 'onBeforeAttributeLabels',
		));
	}

	/**
	 * Be careful to only turn it on not off because if you set it to false it will cause other behaviors to be in XSS injection risk<br/>
	 * $e->params['NeedsXSSPurify'] is a reference to turn on xss purification in the Model<br/>
	 * $e->params['NeedsXSSPurify'] = true;<br/>
	 * @param \CEvent $e
	 */
	public function onBeforeGetXSSPurification(\CEvent $e) {
		
	}

	/**
	 * $e->params['arrXSSExceptions'] is a reference to merge new XSSPurify_Exceptions to the Model<br/>
	 * $e->params['arrXSSExceptions'] = array_merge($e->params['arrXSSExceptions'], array());
	 * @param \CEvent $e
	 */
	public function onBeforeXSSPurify_Exceptions(\CEvent $e) {
		
	}

	/**
	 * $e->params['arrXSSAttrs'] is a reference to merge new XSSPurify_Attrs to the Model<br/>
	 * $e->params['arrXSSAttrs'] = array_merge($e->params['arrXSSAttrs'], array());
	 * @param \CEvent $e
	 */
	public function onBeforeXSSPurify_Attrs(\CEvent $e) {
		
	}

	/**
	 * $e->params['arrRules'] is a reference to merge new rules to the Model<br/>
	 * $e->params['arrRules'] = array_merge($e->params['arrRules'], array());
	 * @param \CEvent $e
	 */
	public function onBeforeRules(\CEvent $e) {
		
	}

	/**
	 * $e->params['arrAttrLabels'] is a reference to merge new rules to the Model<br/>
	 * $e->params['arrAttrLabels'] = array_merge($e->params['arrAttrLabels'], array());
	 * @param \CEvent $e
	 */
	public function onBeforeAttributeLabels(\CEvent $e) {
		
	}

}
