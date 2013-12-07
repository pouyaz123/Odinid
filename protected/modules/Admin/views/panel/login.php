<?php
/* @var $this \Admin\controllers\PanelController */
/* @var $Model \Admin\models\AdminLogin */
?>
<div id="divLoginForm" class="form">
	<?
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'formLogin',
		'method' => 'POST',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
		'focus' => array($Model, 'txtUsername'),
//		'htmlOptions'=>array('rel'=>\html::AjaxPanel())
			));
	/* @var $form CActiveForm */
	?>
		<div class="row">
			<?= $form->label($Model, 'txtUsername'); ?>
			<?= $form->textField($Model, 'txtUsername', array('autocomplete'=>'off')) ?>
			<?= $form->error($Model, 'txtUsername') ?>
		</div>
		<div class="row">
			<?= $form->label($Model, 'txtPassword'); ?>
			<?= $form->passwordField($Model, 'txtPassword') ?>
			<?= $form->error($Model, 'txtPassword') ?>
		</div>
		<div class="row">
			<?= $form->label($Model, 'chkRemember'); ?>
			<?= $form->checkBox($Model, 'chkRemember') ?>
			<?= $form->error($Model, 'chkRemember') ?>
		</div>
		<div class="row">
			<div><? $form->widget('CCaptcha') ?></div>
			<?= $form->label($Model, 'txtCaptcha'); ?>
			<?= $form->textField($Model, 'txtCaptcha', array('autocomplete'=>'off')) ?>
			<?= $form->error($Model, 'txtCaptcha') ?>
		</div>
		<div class="row submit">
			<? /*CHtml::ajaxSubmitButton(\Lng::Admin('tr_Common', 'Login'), ''
					, array(
						'replace'=>'#divLoginForm'
					)
				);*/ ?>
			<?= CHtml::submitButton(\Lng::Admin('tr_Common', 'Login')
					,array('rel'=>\html::AjaxElement('#divLoginForm'))) ?>
		</div>
		<?= $form->errorSummary($Model); ?>
	<? $this->endWidget(); ?>
</div>
