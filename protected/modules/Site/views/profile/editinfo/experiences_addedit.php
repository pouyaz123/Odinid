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
		<?= $Model->scenario == 'Edit' ? \CHtml::link(t2::Site_User('Add new'), \Site\Consts\Routes::User_EditExperiences()) : '' ?>
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
							$form->checkBox($Model, 'chkHealthInsurance')
							, $form->labelEx($Model, 'chkHealthInsurance')
							, $form->error($Model, 'chkHealthInsurance'))
					?>
					<?=
					html::FieldContainer(
							$form->checkBox($Model, 'chkOvertimePay')
							, $form->labelEx($Model, 'chkOvertimePay')
							, $form->error($Model, 'chkOvertimePay'))
					?>
					<?=
					html::FieldContainer(
							$form->checkBox($Model, 'chkRetirementAccount')
							, $form->labelEx($Model, 'chkRetirementAccount')
							, $form->error($Model, 'chkRetirementAccount'))
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
							\html::activeComboBox($Model, $form, 'ddlWorkCondition', $Model->arrWorkConditions
									, array('prompt' => '', 'rel' => \html::Combobox_NoSearchRel))
							, $form->labelEx($Model, 'ddlWorkCondition')
							, $form->error($Model, 'ddlWorkCondition'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtSalaryAmount')
							, $form->labelEx($Model, 'txtSalaryAmount')
							, $form->error($Model, 'txtSalaryAmount'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtTBALayoff')
							, $form->labelEx($Model, 'txtTBALayoff')
							, $form->error($Model, 'txtTBALayoff'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtRetirementPercent')
							, $form->labelEx($Model, 'txtRetirementPercent')
							, $form->error($Model, 'txtRetirementPercent'))
					?>

					<?=
					html::ButtonContainer(
							CHtml::submitButton(\t2::Site_User($Model->scenario == 'Edit' ? 'Edit' : 'Add')
									, array(
								'name' => $Model->scenario == 'Edit' ? 'btnSaveEdit' : 'btnAdd',
								'rel' => \html::AjaxElement('#divEditInfo') . ' ' . \html::OnceClick
									)
					))
					?>
					<?=
					$Model->scenario == 'Edit' ?
							html::ButtonContainer(
									CHtml::button(\t2::Site_User('Delete')
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