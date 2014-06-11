<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Avatar */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<?
if ($form = $this->beginWidget('Widgets\ActiveForm', array(
	'id' => 'formProfileInfo',
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
				<? if ($Model->FreshAvatarID): ?>
					<div class="UserAvatar">
						<?=
						Tools\Cloudinary\Cloudinary::cl_image_tag($Model->AvatarID, array('id' => 'imgAvatar',
							'width' => 256))
						?>
					</div>
					<?= $form->hiddenField($Model, 'hdnCropDims') ?>
					<script>
						_t.RunScriptAfterLoad('jcrop/jquery.Jcrop.min', function() {
							$('#imgAvatar').Jcrop({
							aspectRatio: 0
									, onSelect: function(c) {
										$('#<?= $Model->PostName ?>_hdnCropDims').attr('value', c.x + ',' + c.y + ',' + c.x2 + ',' + c.y2)
									}
							, onRelease: function(c) {
								$('#<?= $Model->PostName ?>_hdnCropDims').attr('value', '')
							}
							}
		<? if ($Model->drAvatar['PictureCrop']): ?>
								, function() {
								this.animateTo([<?= $Model->drAvatar['PictureCrop'] ?>])
								}
		<? endif; ?>
							)
						})
					</script>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::site_site('Crop')
									, array(
								'name' => 'btnCrop',
								'rel' => \html::AjaxElement('#divEditInfo') . ' ' . html::OnceClick,
									)
					))
					?>
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
			</td>
			<td class="BtmAlign">
				<?= $form->errorSummary($Model) ?>
			</td>
		</tr>
	</table>
<? endif; ?>
<? $this->endWidget(); ?>
<? $this->endContent(); ?>
