<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
?>
<div id="divEditEmail">
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'ProfileInfo',
		'method' => 'POST',
		'enableClientValidation' => true,
//		'enableAjaxValidation' => true,	//disabled because of the Edit mode which the ID of the current email must be passed
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
		'focus' => array($Model, 'txtEmail'),
			))):
		/* @var $form Widgets\ActiveForm */
		?>
		<?= $Model->scenario == 'Edit' ? \CHtml::link(t2::Site_User('Add new'), \Site\Consts\Routes::User_EditEmails()) : '' ?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?= $form->hiddenField($Model, 'hdnEmailID') ?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtEmail')
							, $form->labelEx($Model, 'txtEmail')
							, $form->error($Model, 'txtEmail', NULL, true))
					?>
					<div><?= $Model->PendingEmail ? t2::Site_User('Pending email') . ' : ' . $Model->PendingEmail : '' ?></div>
					<?=
					html::FieldContainer(
							$form->checkBox($Model, 'chkIsPrivate')
							, $form->labelEx($Model, 'chkIsPrivate')
							, $form->error($Model, 'chkIsPrivate', NULL, true))
					?>

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
										'onclick' => \html::PostbackConfirm_OnClick('Are you sure?'),
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