<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<?
if ($form = $this->beginWidget('Widgets\ActiveForm', array(
	'id' => 'ProfileInfo',
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
						\html::activeComboBox($Model, $form, 'ddlHireAvailabilityType', $Model->arrHireAvailabilityTypes, array('prompt' => ''))
						, $form->labelEx($Model, 'ddlHireAvailabilityType')
						, $form->error($Model, 'ddlHireAvailabilityType'))
				?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtHireAvailabilityDate')
						, $form->labelEx($Model, 'txtHireAvailabilityDate')
						, $form->error($Model, 'txtHireAvailabilityDate'))
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
				html::ButtonContainer(
						CHtml::submitButton(\t2::site_site('Update')
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
