<?php
/* @var $this \Site\controllers\UserController */
/* @var $Model \Site\models\User */

//$this->breadcrumbs=array(
//	$this->module->id,
//);
?>
<div class="form">
	<?
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'Register',
		'method' => 'POST',
		'enableClientValidation' => true,
		'enableAjaxValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
//			'validateOnChange' => true,
//			'validationDelay' => 500,
		),
		'focus' => array($Model, 'txtEmail'),
			));
	/* @var $form CActiveForm */
	?>
		<?= $form->errorSummary($Model); ?>
		<div class="row">
			<?= $form->label($Model, 'txtEmail'); ?>
			<?= $form->textField($Model, 'txtEmail') ?>
			<?= $form->error($Model, 'txtEmail', array(), true) ?>
		</div>
		<div class="row">
			<?= $form->label($Model, 'txtEmailRepeat'); ?>
			<?= $form->textField($Model, 'txtEmailRepeat') ?>
			<?= $form->error($Model, 'txtEmailRepeat') ?>
		</div>
		<div class="row">
			<?= $form->label($Model, 'txtUsername'); ?>
			<?= $form->textField($Model, 'txtUsername') ?>
			<?= $form->error($Model, 'txtUsername', array(), true) ?>
		</div>
		<div class="row">
			<?= $form->label($Model, 'txtPassword'); ?>
			<?= $form->passwordField($Model, 'txtPassword') ?>
			<?= $form->error($Model, 'txtPassword') ?>
		</div>
		<div class="row">
			<?= $form->label($Model, 'txtInvitationCode'); ?>
			<?= $form->textField($Model, 'txtInvitationCode') ?>
			<?= $form->error($Model, 'txtInvitationCode') ?>
		</div>
		<div class="row">
			<div><? $form->widget('CCaptcha') ?></div>
			<?= $form->label($Model, 'txtCaptcha'); ?>
			<?= $form->textField($Model, 'txtCaptcha', array('autocomplete'=>'off')) ?>
			<?= $form->error($Model, 'txtCaptcha', array(), false, false) ?>
		</div>
		<div class="row submit">
			<?= CHtml::submitButton(\Lng::Site('User', 'Register')); ?>
		</div>
	<? $this->endWidget(); ?>
</div>
