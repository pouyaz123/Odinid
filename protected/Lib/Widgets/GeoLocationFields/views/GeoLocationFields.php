<?php
/* @var $this Widgets\GeoLocationFields\GeoLocationFields */
/* @var $Model Base\FormModel */

use \Widgets\GeoLocationFields\GeoLocationFields;
?>
<?=
\html::FieldContainer(
		\html::activeComboBox($Model, $this->ActiveForm, $this->ddlCountryAttr, $this->ddlarrCountries
				, array(
			'rel' => $this->DivisionDropDown_ContainerID ?
					\html::AjaxElement("#$this->DivisionDropDown_ContainerID:insert", $this->AjaxKW) : '',
			'prompt' => ($this->PromptDDLOption ? : NULL),
				)
				, null
				, null
				//ddlCombo with user input ability
				, $this->txtCountryAttr ? array(
					'attribute' => $this->txtCountryAttr
					, 'htmlOptions' => array(
						'rel' => $this->DivisionDropDown_ContainerID ?
								\html::AjaxElement("#$this->DivisionDropDown_ContainerID", $this->AjaxKW) : '',
					)) : null
		)
		, \html::activeLabelEx($Model, $this->ActiveForm, $this->ddlCountryAttr)
		, $this->txtCountryAttr ? \html::error($Model, $this->ActiveForm, $this->txtCountryAttr) : null
)
?> 
<div id='<?= $this->DivisionDropDown_ContainerID ?>'>
	<?= GeoLocationFields::GenerateDivisionDDL($Model, $this, $this->ddlarrDivisions) ?>
	<?= GeoLocationFields::GenerateCityDDL($Model, $this, $this->ddlarrCities) ?>
</div>
<?=
($this->txtAddress1Attr ? \html::FieldContainer(\html::activeTextField($Model, $this->ActiveForm, $this->txtAddress1Attr)
				, \html::activeLabelEx($Model, $this->ActiveForm, $this->txtAddress1Attr)
				, \html::error($Model, $this->ActiveForm, $this->txtAddress1Attr)) : '')
?>
<?=
($this->txtAddress2Attr ? \html::FieldContainer(\html::activeTextField($Model, $this->ActiveForm, $this->txtAddress2Attr)
				, \html::activeLabelEx($Model, $this->ActiveForm, $this->txtAddress2Attr)
				, \html::error($Model, $this->ActiveForm, $this->txtAddress2Attr)) : '')
?>
