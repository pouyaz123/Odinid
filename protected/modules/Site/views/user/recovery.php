<?php
/* @var $this \Site\controllers\UserController */
/* @var $Model \Site\models\User\Recovery */
?>
<div id="divSendRecoveryLinkForm" class="form">
	<? $this->beginContent('Site.views.user.layout') ?>
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'SendRecoveryLink',
		'method' => 'POST',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
		'focus' => array($Model, 'txtEmail'),
			))):
		/* @var $form Widgets\ActiveForm */
		?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtEmail', array('autocomplete' => 'off'))
							, $form->labelEx($Model, 'txtEmail')
							, $form->error($Model, 'txtEmail'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtEmailRepeat', array('autocomplete' => 'off'))
							, $form->labelEx($Model, 'txtEmailRepeat')
							, $form->error($Model, 'txtEmailRepeat'))
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
							CHtml::submitButton(\t2::site_site('Recover')
									, array(
								'name' => 'btnSendRecoveryLink',
								'rel' => \html::AjaxElement('#divSendRecoveryLinkForm')
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
