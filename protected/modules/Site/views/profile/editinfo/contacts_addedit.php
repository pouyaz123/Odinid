<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
?>
<div id="divEditContact">
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'ProfileInfo',
		'method' => 'POST',
		'enableClientValidation' => true,
//		'enableAjaxValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
		'focus' => array($Model, 'txtPhone'),
			))):
		/* @var $form Widgets\ActiveForm */
		?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?= $form->hiddenField($Model, 'hdnContactID') ?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtPhone')
							, $form->labelEx($Model, 'txtPhone')
							, $form->error($Model, 'txtPhone'))
					?>
					<?=
					html::FieldContainer(
							\html::activeComboBox($Model, $form, 'ddlPhoneType', $Model->arrPhoneTypes
									, array('prompt' => '', 'rel' => \html::Combobox_NoSearchRel))
							, $form->labelEx($Model, 'ddlPhoneType')
							, $form->error($Model, 'ddlPhoneType'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtEmail')
							, $form->labelEx($Model, 'txtEmail')
//							, $form->error($Model, 'txtEmail'))
							, $form->error($Model, 'txtEmail', NULL, true))
					?>
					<? if ($Model->asa('Info_Company')): ?>
						<?=
						html::FieldContainer(
								$form->textField($Model, 'txtContactFirstName')
								, $form->labelEx($Model, 'txtContactFirstName')
								, $form->error($Model, 'txtContactFirstName'))
						?>
						<?=
						html::FieldContainer(
								$form->textField($Model, 'txtContactLastName')
								, $form->labelEx($Model, 'txtContactLastName')
								, $form->error($Model, 'txtContactLastName'))
						?>
						<?=
						html::FieldContainer(
								$form->textField($Model, 'txtContactJobTitle')
								, $form->labelEx($Model, 'txtContactJobTitle')
								, $form->error($Model, 'txtContactJobTitle'))
						?>
					<? endif; ?>

					<?=
					html::ButtonContainer(
							CHtml::submitButton(\t2::Site_User($Model->scenario == 'Edit' ? 'Edit' : 'Add')
									, array(
								'name' => $Model->scenario == 'Edit' ? 'btnSaveEdit' : 'btnAdd',
								'rel' => \html::AjaxElement('#divEditInfo') . ' ' . html::OnceClick
									)
					))
					?>
					<?=
					$Model->scenario == 'Edit' && !$Model->IsPrimaryEmailEdit ?
							html::ButtonContainer(
									CHtml::button(\t2::Site_User('Delete')
											, array(
										'name' => 'btnDelete',
										'rel' => \html::AjaxElement('#divEditInfo') . ' ' . html::OnceClick,
										'onclick' => \html::PostbackConfirm_OnClick('Are you sure to delete it?'),
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