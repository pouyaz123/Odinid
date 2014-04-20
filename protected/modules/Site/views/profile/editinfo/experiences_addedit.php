<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Experiences */
/* @var $wdgGeoLocation \Widgets\GeoLocationFields\GeoLocationFields */
?>
<div id="divEditExperiences">
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'ProfileInfo',
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
						_t.RunScriptAfterLoad('tagit/tag-it.min', function() {
							_t.RunScriptAfterLoad('MyJuiAutoComplete/MyAutoComplete', function() {
								function TagStartup($obj) {
									$($obj).next('ul').find('a').attr('rel', 'AjaxExcept')
								}
								var $obj = $('#UserExperiences_txtCompanyTitle')
										, ACOpts = MyAutoComplete(
												$obj, {
													source: '<?= Tools\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], "__AjaxPostKW=AutoComplete_UserExperiences_txtCompanyTitle") ?>'
													, select: function(e, ui) {
														if (ui.item.label) {
															var dr = $.parseJSON($(ui.item.label).attr('rel'))
															if (dr['ID'])
																$('#UserExperiences_hdnCompanyID').attr('value', dr['ID'])
															if (dr['URL'])
																$('#UserExperiences_txtCompanyURL').tagit('createTag', dr['URL'])
														}
													}
												}, 0, 1, 1, 1)

								$obj.tagit({
									allowSpaces: true
									, autocomplete: ACOpts
									, tagLimit: 1
									, afterTagAdded: function(e, ui) {
										TagStartup($(this))
									}
									, afterTagRemoved: function() {
										$('#UserExperiences_txtCompanyURL').tagit('removeAll')
										$('#UserExperiences_hdnCompanyID').attr('value', '')
									}
								})
								TagStartup($obj)
								TagStartup($('#UserExperiences_txtCompanyURL').tagit({
									allowSpaces: true
									, tagLimit: 1
									, afterTagAdded: function(e, ui) {
										TagStartup($(this))
									}
									, afterTagRemoved: function() {
										$('#UserExperiences_hdnCompanyID').attr('value', '')
									}
								}))
							})
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
						$("#<?= $Model->PostName ?>_txtFromDate, #<?= $Model->PostName ?>_txtToDate").datepicker({
							showOn: "both",
							dateFormat: 'yy-mm-dd',
							buttonText: '<span class="ui-icon ui-icon-calendar"></span>',
							maxDate: "+0D",
							changeMonth: true,
							changeYear: true,
							yearRange: '<?= date('Y') - Site\models\Profile\Certificates::OldestYearLimitation ?>:<?= date('Y') ?>'
								});
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