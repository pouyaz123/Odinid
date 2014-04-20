<?php
/* @var $this Site\controllers\UserController */
/* @var $Model \Site\models\User\Login */
?>
<div id="divLoginForm" class="form">
	<? $this->beginContent('Site.views.user.layout') ?>
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'formLogin',
		'method' => 'POST',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
		'focus' => array($Model, 'txtUsername'),
			))):
		/* @var $form Widgets\ActiveForm */
		?>
		<?=
		html::FieldContainer(
				$form->textField($Model, 'txtUsername', array('autocomplete' => 'off'))
				, $form->labelEx($Model, 'txtUsername')
				, $form->error($Model, 'txtUsername'))
		?>
		<?=
		html::FieldContainer(
				$form->passwordField($Model, 'txtPassword')
				, $form->labelEx($Model, 'txtPassword')
				, $form->error($Model, 'txtPassword'))
		?>
		<?=
		html::FieldContainer(
				$form->checkBox($Model, 'chkRemember')
				, $form->labelEx($Model, 'chkRemember')
				, $form->error($Model, 'chkRemember'))
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
				CHtml::submitButton(\t2::site_site('Login')
						, array(
					'name' => 'btnLogin',
					'rel' => \html::AjaxElement('#divLoginForm')
						)
		))
		?>
		<?= $form->errorSummary($Model); ?>
	<? endif; ?>
	<? $this->endWidget(); ?>
	<? $this->endContent(); ?>
</div>
