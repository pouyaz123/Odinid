<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Educations */
/* @var $wdgGeoLocation \Widgets\GeoLocationFields\GeoLocationFields */
?>
<div id="divEditEducations">
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
		<?= $Model->scenario == 'Edit' ? \CHtml::link(t2::site_site('Add new'), \Site\Consts\Routes::User_EditEducations()) : '' ?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?= $form->hiddenField($Model, 'hdnEducationID') ?>
					<?= $form->hiddenField($Model, 'hdnSchoolID') ?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtSchoolTitle')
							, $form->labelEx($Model, 'txtSchoolTitle')
							, $form->error($Model, 'txtSchoolTitle'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtSchoolURL')
							, $form->labelEx($Model, 'txtSchoolURL')
							, $form->error($Model, 'txtSchoolURL'))
					?>
<script>
_t.RunScriptAfterLoad(['tagit/tag-it.min', 'MyJuiAutoComplete/MyAutoComplete', 'tagit_ac_urlFactor'], function() {
	tagit_ac_urlFactor(
		'#UserEducations_txtSchoolTitle', '#UserEducations_hdnSchoolID', '#UserEducations_txtSchoolURL',
		'<?= Tools\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], "__AjaxPostKW=AutoComplete_UserEducations_txtSchoolTitle") ?>')
})
</script>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtStudyField')
							, $form->labelEx($Model, 'txtStudyField')
							, $form->error($Model, 'txtStudyField'))
					?>
<script>
_t.RunScriptAfterLoad('MyJuiAutoComplete/MyAutoComplete', function() {
	MyAutoComplete($("#UserEducations_txtStudyField")
		, {source: '<?= Tools\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], "__AjaxPostKW=AutoComplete_UserEducations_txtStudyField") ?>'}
		, 0, 1, 1, 0)
})
</script>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtDegree')
							, $form->labelEx($Model, 'txtDegree')
							, $form->error($Model, 'txtDegree'))
					?>
<script>
_t.RunScriptAfterLoad('MyJuiAutoComplete/MyAutoComplete', function() {
	MyAutoComplete($("#UserEducations_txtDegree")
		, {source: '<?= Tools\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], "__AjaxPostKW=AutoComplete_UserEducations_txtDegree") ?>'}
		, 0, 1, 1, 0)
})
</script>
					<?
					$wdgGeoLocation->ActiveForm = $form;
					echo $wdgGeoLocation;
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
_t.RunScriptAfterLoad('jqUI/jquery.ui.datepicker.min', function() {
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
			yearRange: '<?= date('Y') - Site\models\Profile\Educations::OldestYearLimitation ?>:<?= date('Y') ?>'
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