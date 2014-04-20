<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Avatar */
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
		))):
	/* @var $form Widgets\ActiveForm */
	?>
	<table class="FullW">
		<tr>
			<td style="width: 350px">
				<? if ($UserAvatarID = $Model->FreshAvatarID): ?>
					<?= cl_image_tag($UserAvatarID) ?>
				<? endif; ?>
				<?=
				html::FieldContainer(
						$form->fileField($Model, 'fileAvatar')
						, $form->labelEx($Model, 'fileAvatar')
						, $form->error($Model, 'fileAvatar'))
				?>
				<?=
				html::ButtonContainer(
						CHtml::submitButton(\t2::site_site('Upload')
								, array(
							'name' => 'btnUpload',
							'rel' => \html::AjaxElement('#divEditInfo')
								)
				))
				?>
				<? if ($UserAvatarID): ?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::site_site('Delete')
									, array(
								'name' => 'btnDelete',
								'rel' => \html::AjaxElement('#divEditInfo') . ' ' . html::OnceClick,
								'onclick' => \html::PostbackConfirm_OnClick('Are you sure?'),
									)
					))
					?>
				<? endif; ?>
			</td>
			<td class="BtmAlign">
				<?= $form->errorSummary($Model) ?>
			</td>
		</tr>
	</table>
<? endif; ?>
<? $this->endWidget(); ?>
<? $this->endContent(); ?>
