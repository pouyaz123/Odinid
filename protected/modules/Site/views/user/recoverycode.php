<?php
/* @var $this \Site\controllers\UserController */
/* @var $Model \Site\models\User\Recovery */
?>
<div id="divRecoverForm" class="form">
	<? $this->beginContent('Site.views.user.layout') ?>
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'Recovery',
		'method' => 'POST',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
		'focus' => array($Model, 'txtRecoveryCode'),
			))):
		/* @var $form Widgets\ActiveForm */
		?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtRecoveryCode', array('autocomplete' => 'off'))
							, $form->labelEx($Model, 'txtRecoveryCode')
							, $form->error($Model, 'txtRecoveryCode'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtNewPassword', array('autocomplete' => 'off'))
							, $form->labelEx($Model, 'txtNewPassword')
							, $form->error($Model, 'txtNewPassword'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtNewPasswordRepeat', array('autocomplete' => 'off'))
							, $form->labelEx($Model, 'txtNewPasswordRepeat')
							, $form->error($Model, 'txtNewPasswordRepeat'))
					?>
					<?=
					html::CaptchaFieldContainer(
							html::CaptchaImage($form)
							, $form->textField($Model, 'txtCaptcha', array('autocomplete' => 'off'))
							, $form->labelEx($Model, 'txtCaptcha')
							, $form->error($Model, 'txtCaptcha'))
					?>
					<?=
					html::ButtonContainer(
							CHtml::submitButton(\t2::site_site('Reset password')
									, array(
								'name' => 'btnRecover',
								'rel' => \html::AjaxElement('#divRecoverForm')
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
</div>
