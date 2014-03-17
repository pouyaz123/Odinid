<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<?
if ($form = $this->beginWidget('Widgets\ActiveForm', array(
	'id' => 'EditInfo',
	'method' => 'POST',
	'enableClientValidation' => true,
	'enableAjaxValidation' => true,
	'clientOptions' => array(
		'validateOnSubmit' => true,
	),
	'focus' => array($Model, 'txtFirstName'),
		))):
	/* @var $form Widgets\ActiveForm */
	?>
	<table class="FullW">
		<tr>
			<td style="width: 350px">
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtFirstName')
						, $form->labelEx($Model, 'txtFirstName')
						, $form->error($Model, 'txtFirstName'))
				?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtLastName')
						, $form->labelEx($Model, 'txtLastName')
						, $form->error($Model, 'txtLastName'))
				?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtMidName')
						, $form->labelEx($Model, 'txtMidName')
						, $form->error($Model, 'txtMidName'))
				?>
				<?=
				html::FieldContainer(
						\html::activeComboBox($Model, $form, 'ddlGender', $Model->arrGenders, array('prompt' => ''))
						, $form->labelEx($Model, 'ddlGender')
						, $form->error($Model, 'ddlGender'))
				?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtArtistTitle')
						, $form->labelEx($Model, 'txtArtistTitle')
						, $form->error($Model, 'txtArtistTitle'))
				?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtBirthday')
						, $form->labelEx($Model, 'txtBirthday')
						, $form->error($Model, 'txtBirthday'))
				?>
				<?=
				html::FieldContainer(
						\html::activeComboBox($Model, $form, 'ddlBirthdayFormat', $Model->arrBirthdayFormats
								, array('rel' => \html::Combobox_NoSearchRel))
						, $form->labelEx($Model, 'ddlBirthdayFormat')
						, $form->error($Model, 'ddlBirthdayFormat'))
				?>
				<?=
				html::FieldContainer(
						$form->textArea($Model, 'txtObjective')
						, $form->labelEx($Model, 'txtObjective')
						, $form->error($Model, 'txtObjective'))
				?>
				<?=
				html::FieldContainer(
						$form->textArea($Model, 'txtSmallDesc')
						, $form->labelEx($Model, 'txtSmallDesc')
						, $form->error($Model, 'txtSmallDesc'))
				?>
				<?=
				html::FieldContainer(
						$form->textArea($Model, 'txtDescription')
						, $form->labelEx($Model, 'txtDescription')
						, $form->error($Model, 'txtDescription'))
				?>
				<?=
				html::FieldContainer(
						$form->checkBox($Model, 'chkTalentSearchVisibility')
						, $form->labelEx($Model, 'chkTalentSearchVisibility')
						, $form->error($Model, 'chkTalentSearchVisibility'))
				?>
				<?=
				html::FieldContainer(
						$form->checkBox($Model, 'chkRelocateInternally')
						, $form->labelEx($Model, 'chkRelocateInternally')
						, $form->error($Model, 'chkRelocateInternally'))
				?>
				<?=
				html::FieldContainer(
						$form->checkBox($Model, 'chkRelocateExternally')
						, $form->labelEx($Model, 'chkRelocateExternally')
						, $form->error($Model, 'chkRelocateExternally'))
				?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtHireAvailabilityDate')
						, $form->labelEx($Model, 'txtHireAvailabilityDate')
						, $form->error($Model, 'txtHireAvailabilityDate'))
				?>
				<?=
				html::FieldContainer(
						\html::activeComboBox($Model, $form, 'ddlHireAvailabilityType', $Model->arrHireAvailabilityTypes, array('prompt' => ''))
						, $form->labelEx($Model, 'ddlHireAvailabilityType')
						, $form->error($Model, 'ddlHireAvailabilityType'))
				?>
				<?=
				html::ButtonContainer(
						CHtml::submitButton(\t2::Site_User('Update')
								, array(
							'name' => 'btnUpdate',
							'rel' => \html::AjaxElement('#divEditInfo')
								)
				))
				?>
			</td>
			<td class="BtmAlign">
				<?= $form->errorSummary($Model) ?>
			</td>
		</tr>
	</table>
<? endif; ?>
<? $this->endWidget(); ?>
<? $this->endContent(); ?>
