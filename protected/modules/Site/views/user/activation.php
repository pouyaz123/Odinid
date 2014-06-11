<?php
/* @var $this \Site\controllers\UserController */
/* @var $Model \Site\models\User\Activation */
?>
<div id="divActivationForm" class="form">
	<? $this->beginContent('Site.views.user.layout') ?>
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'formActivation',
		'method' => 'POST',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
		'focus' => array($Model, 'txtActivationCode'),
			))):
		/* @var $form Widgets\ActiveForm */
		?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtActivationCode', array('autocomplete' => 'off'))
							, $form->labelEx($Model, 'txtActivationCode')
							, $form->error($Model, 'txtActivationCode'))
					?>
					<?=
					html::ButtonContainer(
							CHtml::submitButton(\t2::site_site('Activate')
									, array(
								'name' => 'btnActivate',
								'rel' => \html::AjaxElement('#divActivationForm')
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