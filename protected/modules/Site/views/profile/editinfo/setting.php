<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Setting */
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
	'focus' => array($Model, 'txtCurrentPassword'),
		))):
	/* @var $form Widgets\ActiveForm */
	?>
	<table class="FullW">
		<tr>
			<td style="width: 350px">
				<?=
				html::FieldContainer(
						$form->passwordField($Model, 'txtCurrentPassword')
						, $form->labelEx($Model, 'txtCurrentPassword')
						, $form->error($Model, 'txtCurrentPassword'))
				?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtUsername', array('autocomplete' => 'off'))
						, $form->labelEx($Model, 'txtUsername')
						, $form->error($Model, 'txtUsername'))
				?>
				<div><?= t2::Site_User('Current username') . ' : ' . $Model->drUser['Username'] ?></div>
				<div><?= t2::Site_User('Username change') . ' : ' . $Model->drUser['UNChangeCount'] ?></div>
				<?=
				html::FieldContainer(
						$form->passwordField($Model, 'txtNewPassword')
						, $form->labelEx($Model, 'txtNewPassword')
						, $form->error($Model, 'txtNewPassword'))
				?>
				<?=
				html::FieldContainer(
						$form->passwordField($Model, 'txtNewPasswordRepeat')
						, $form->labelEx($Model, 'txtNewPasswordRepeat')
						, $form->error($Model, 'txtNewPasswordRepeat'))
				?>
				<?=
				html::FieldContainer(
						$form->checkBox($Model, 'chkBlockMatureContent')
						, $form->labelEx($Model, 'chkBlockMatureContent')
						, $form->error($Model, 'chkBlockMatureContent'))
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
				<? /* = $form->errorSummary($Model) */ ?>
			</td>
		</tr>
	</table>
<? endif; ?>
<? $this->endWidget(); ?>
<? $this->endContent(); ?>
