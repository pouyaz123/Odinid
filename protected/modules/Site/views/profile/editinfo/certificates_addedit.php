<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Certificates */
/* @var $wdgGeoLocation \Widgets\GeoLocationFields\GeoLocationFields */
?>
<div id="divEditCertificates">
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
		<?= $Model->scenario == 'Edit' ? \CHtml::link(t2::site_site('Add new'), \Site\Consts\Routes::User_EditCertificates()) : '' ?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?= $form->hiddenField($Model, 'hdnCertificateID') ?>
					<?= $form->hiddenField($Model, 'hdnInstitutionID') ?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtInstitutionTitle')
							, $form->labelEx($Model, 'txtInstitutionTitle')
							, $form->error($Model, 'txtInstitutionTitle'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtInstitutionURL')
							, $form->labelEx($Model, 'txtInstitutionURL')
							, $form->error($Model, 'txtInstitutionURL'))
					?>
<script>
_t.RunScriptAfterLoad(['tagit/tag-it.min', 'MyJuiAutoComplete/MyAutoComplete', 'tagit_ac_urlFactor'], function() {
	tagit_ac_urlFactor(
		'#UserCertificates_txtInstitutionTitle', '#UserCertificates_hdnInstitutionID', '#UserCertificates_txtInstitutionURL',
		'<?= Tools\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], "__AjaxPostKW=AutoComplete_UserCertificates_txtInstitutionTitle") ?>')
})
</script>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtTitle')
							, $form->labelEx($Model, 'txtTitle')
							, $form->error($Model, 'txtTitle'))
					?>
					<?
					$wdgGeoLocation->ActiveForm = $form;
					echo $wdgGeoLocation;
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtDate')
							, $form->labelEx($Model, 'txtDate')
							, $form->error($Model, 'txtDate'))
					?>
<script>
_t.RunScriptAfterLoad('jqUI/jquery.ui.datepicker.min', function() {
	$("#<?= $Model->PostName ?>_txtDate").datepicker({
		showOn: "both",
		dateFormat: 'yy-mm-dd',
		buttonText: '<span class="ui-icon ui-icon-calendar"></span>',
		maxDate: "+0D",
		changeMonth: true,
		changeYear: true,
		yearRange: '<?= date('Y') - Site\models\Profile\Certificates::OldestYearLimitation ?>:<?= date('Y') ?>'
	})
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