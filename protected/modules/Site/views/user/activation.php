<?php
/* @var $this \Site\controllers\UserController */
/* @var $Model \Site\models\User\Activation */
?>
<div id="divActivateForm" class="form">
	<?
	$form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'Activate',
		'method' => 'POST',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
		'focus' => array($Model, 'txtActivationCode'),
	));
	/* @var $form CActiveForm */
	?>
	<table class="FullW">
		<tr>
			<td style="width: 350px">
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtActivationCode')
						, $form->labelEx($Model, 'txtActivationCode')
						, $form->error($Model, 'txtActivationCode', array(), true))
				?>
				<?=
				html::ButtonContainer(
						CHtml::submitButton(\Lng::Site('tr_user', 'Activate')
								, array(
							'name' => 'btnActivate',
							'rel' => \html::AjaxElement('#divActivateForm')
								)
				))
				?>
			</td>
			<td class="BtmAlign">
				<?= $form->errorSummary($Model) ?>
			</td>
		</tr>
	</table>
	<? $this->endWidget(); ?>
</div>
