<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Experiences */
/* @var $wdgGeoLocation \Widgets\GeoLocationFields\GeoLocationFields */
?>
<div id="divEditExperiences">
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'formProfileInfo',
		'method' => 'POST',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
			))):
		/* @var $form Widgets\ActiveForm */
		?>
		<?= $Model->scenario == 'Edit' ? \CHtml::link(t2::site_site('Add new'), \Site\Consts\Routes::User_EditExperiences()) : '' ?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?= $form->hiddenField($Model, 'hdnExperienceID') ?>
					<?= $form->hiddenField($Model, 'hdnCompanyID') ?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtCompanyTitle')
							, $form->labelEx($Model, 'txtCompanyTitle')
							, $form->error($Model, 'txtCompanyTitle'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtCompanyURL')
							, $form->labelEx($Model, 'txtCompanyURL')
							, $form->error($Model, 'txtCompanyURL'))
					?>
<script>
_t.RunScriptAfterLoad(['tagit/tag-it.min', 'MyJuiAutoComplete/MyAutoComplete', 'tagit/ac_companies'], function() {
	tagit_ac_companies(
		'#UserExperiences_txtCompanyTitle', '#UserExperiences_hdnCompanyID', '#UserExperiences_txtCompanyURL',
		'<?= Tools\HTTP::URL_InsertAjaxKW("AutoComplete_UserExperiences_txtCompanyTitle") ?>')
})
</script>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtJobTitle')
							, $form->labelEx($Model, 'txtJobTitle')
							, $form->error($Model, 'txtJobTitle'))
					?>
					<?
					$wdgGeoLocation->ActiveForm = $form;
					echo $wdgGeoLocation;
					?>
					<?=
					html::FieldContainer(
							\html::activeComboBox($Model, $form, 'ddlEmploymentType', $Model->arrEmployTypes
									, array('prompt' => '', 'rel' => \html::Combobox_NoSearchRel))
							, $form->labelEx($Model, 'ddlEmploymentType')
							, $form->error($Model, 'ddlEmploymentType'))
					?>
					<?=
					html::FieldContainer(
							\html::activeComboBox($Model, $form, 'ddlSalaryType', $Model->arrSalaryTypes
									, array('prompt' => '', 'rel' => \html::Combobox_NoSearchRel))
							, $form->labelEx($Model, 'ddlSalaryType')
							, $form->error($Model, 'ddlSalaryType'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtSalaryAmount')
							, $form->labelEx($Model, 'txtSalaryAmount')
							, $form->error($Model, 'txtSalaryAmount'))
					?>
					<?=
					html::FieldContainer(
							$form->checkBox($Model, 'chkOvertimePay')
							, $form->labelEx($Model, 'chkOvertimePay')
							, $form->error($Model, 'chkOvertimePay'))
					?>
					<?=
					html::FieldContainer(
							\html::activeComboBox($Model, $form, 'ddlWorkCondition', $Model->arrWorkConditions
									, array('prompt' => '', 'rel' => \html::Combobox_NoSearchRel))
							, $form->labelEx($Model, 'ddlWorkCondition')
							, $form->error($Model, 'ddlWorkCondition'))
					?>
					<?=
					html::FieldContainer(
							\html::activeComboBox($Model, $form, 'ddlLevel', $Model->arrLevels
									, array('prompt' => '', 'rel' => \html::Combobox_NoSearchRel))
							, $form->labelEx($Model, 'ddlLevel')
							, $form->error($Model, 'ddlLevel'))
					?>
					<?=
					html::FieldContainer(
							$form->checkBox($Model, 'chkHealthInsurance')
							, $form->labelEx($Model, 'chkHealthInsurance')
							, $form->error($Model, 'chkHealthInsurance'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtTBALayoff')
							, $form->labelEx($Model, 'txtTBALayoff')
							, $form->error($Model, 'txtTBALayoff'))
					?>
					<?=
					html::FieldContainer(
							$form->checkBox($Model, 'chkRetirementAccount')
							, $form->labelEx($Model, 'chkRetirementAccount')
							, $form->error($Model, 'chkRetirementAccount'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtRetirementPercent')
							, $form->labelEx($Model, 'txtRetirementPercent')
							, $form->error($Model, 'txtRetirementPercent'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtFromDate')
							, $form->labelEx($Model, 'txtFromDate')
							, $form->error($Model, 'txtFromDate'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtToDate', $Model->chkToPresent ? array('disabled' => 'disabled', 'class' => 'disabled') : array())
							, $form->labelEx($Model, 'txtToDate')
							, $form->error($Model, 'txtToDate'))
					?>
					<?=
					html::FieldContainer(
							$form->checkBox($Model, 'chkToPresent', array(
								'onclick' => '$("#' . $Model->PostName . '_txtToDate").attr("disabled", $(this).is(":checked")?"disabled":null)[$(this).is(":checked")?"addClass":"removeClass"]("disabled")'
							))
							, $form->labelEx($Model, 'chkToPresent')
							, $form->error($Model, 'chkToPresent'))
					?>
<script>
_t.RunScriptAfterLoad(['jqUI/jquery.ui.widget.min', 'jqUI/jquery.ui.datepicker.min'], function() {
	var frmJQSlct = "#<?= $Model->PostName ?>_txtFromDate"
		, toJQSlct = "#<?= $Model->PostName ?>_txtToDate"
		, opts = {
			showOn: "both",
			dateFormat: 'yy-mm-dd',
			buttonText: '<span class="ui-icon ui-icon-calendar"></span>',
			maxDate: "+0D",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				var isTo = $(this).is(toJQSlct)
				$(isTo ? frmJQSlct : toJQSlct).datepicker("option", isTo ? "maxDate" : "minDate", selectedDate);
			},
			yearRange: '<?= date('Y') - Site\models\Profile\Experiences::OldestYearLimitation ?>:<?= date('Y') ?>'
		};
	$(frmJQSlct).datepicker(opts);
	$(toJQSlct).datepicker(opts);
})
</script>
					<?=
					html::FieldContainer(
							$form->textArea($Model, 'txtDescription')
							, $form->labelEx($Model, 'txtDescription')
							, $form->error($Model, 'txtDescription'))
					?>

					<?=
					html::ButtonContainer(
							CHtml::submitButton(\t2::site_site($Model->scenario == 'Edit' ? 'Edit' : 'Add')
									, array(
								'name' => $Model->scenario == 'Edit' ? 'btnSaveEdit' : 'btnAdd',
								'rel' => \html::AjaxElement('#divEditInfo') . ' ' . \html::OnceClick
									)
					))
					?>
					<?=
					$Model->scenario == 'Edit' ?
							html::ButtonContainer(
									CHtml::button(\t2::site_site('Delete')
											, array(
										'name' => 'btnDelete',
										'rel' => \html::AjaxElement('#divEditInfo') . ' ' . html::OnceClick,
										'onclick' => \html::PostbackConfirm_OnClick('Are you sure?'),
											)
							)) : ''
					?>
				</td>
				<td class="BtmAlign">
					<?= $form->errorSummary($Model) ?>
				</td>
			</tr>
		</table>
	<? endif; ?>
	<? $this->endWidget(); ?>
</div>