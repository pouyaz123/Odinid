<?php

namespace Widgets\TagIt;

use \Tools as T;
use \Consts as C;
//mytodo 1 : tagit widget
/**
 * Description of TagIt
 *
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 * @property-write \Base\FormModel $Model set only
 * @property-read array|null $ModelPostValue get only
 * @property-read string $AjaxKW get only
 */
class TagIt extends \Base\Widget {

	private static $Counter = 0;
	public $txtTextAttr;
	public $SQL;
	#
	/** @var \CActiveForm the form widget object to patch the fields to it */
	public $ActiveForm = null;

	/** @var \Base\FormModel */
	private $_Model;

	function setModel($model) {
		$this->_Model = $model;
	}

//tag it autocomplete
	const _AjaxKW = 'TagItAC';

	function getAjaxKW() {
		return self::_AjaxKW . '_' . $this->id;
	}

//-------- DATA --------//
	/**
	 * posted value or the value of the model
	 * @staticvar null $Post
	 * @return array|null
	 */
	function getModelPostValue() {
		static $Post = NULL;
		if (!$Post) {
			$Post = \GPCS::POST(\CHtml::modelName($this->_Model));
			if (!$Post) {
				$txtTextAttr = $this->txtTextAttr;
				$Post = array();
				if ($txtTextAttr)
					$Post[$txtTextAttr] = $this->_Model->$txtTextAttr;
			}
		}
		return $Post;
	}

//-------- Tasks --------//
	public function init() {
		self::$Counter++;
		if (!$this->id)
			$this->id = 'TagItAC' . self::$Counter;

		$Model = &$this->_Model;
		if (!$Model || !is_object($Model) || !is_a($Model, '\CModel'))
			throw new \Err(__METHOD__, 'No valid Model has been passed to ' . __CLASS__ . ' widget');
		if (!$this->txtTextAttr)
			throw new \Err(__METHOD__, 'No text attribute has been set for ' . __CLASS__);

		$_this = &$this;
		\Output::AddIn_AjaxOutput(function()use($Model, $_this) {
			$term = \GPCS::GET('term')? : \GPCS::POST('term');
			if ($term) {
				$dt = T\DB::GetTable($_this->SQL
								, array(':term' => T\DB::EscapeLikeWildCards(T\String::Encode2_DB($term))));
				if ($dt) {
					foreach ($dt as $idx => $dr) {
						$item = array(
							'label' => "<div rel='" . json_encode(array('ID' => $dr['ID'], 'URL' => $dr['URL'])) . "'>{$dr['Title']}" . ($dr['URL'] ? " ({$dr['URL']})" : '') . "</div>"
							, 'value' => $dr['Title']);
						$dt[$idx] = $item;
					}
					echo json_encode($dt);
				}
			}
		}, 'AutoComplete_UserExperiences_txtCompanyTitle');
		\Output::AddIn_AjaxOutput(function()use($Model, $_this) {
			/* @var $_this TagIt */
			$Post = $_this->ModelPostValue;
			if (!$Post)
				return;
		}, $this->AjaxKW);
	}

	public function run() {
		if (!\Output::IsThisAsyncPostBack($this->AjaxKW))
			echo $this->render('TagIt', array(
				'Model' => $this->_Model,
					)
			);
	}

}
