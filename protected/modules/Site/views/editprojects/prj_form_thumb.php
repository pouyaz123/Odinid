<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Projects\Projects */

?>
<div id="divThumbPanel">
	<?=
	html::FieldContainer(
			CHtml::activeFileField($Model, 'fileThumb')
			, CHtml::activeLabelEx($Model, 'fileThumb')
			, CHtml::error($Model, 'fileThumb'))
	?>
	<? if ($Model->scenario=='Edit'): ?>
			<?=
			html::ButtonContainer(
					CHtml::submitButton(\t2::site_site('Upload')
							, array(
						'name' => 'btnUpload',
						'rel' => \html::AjaxElement('#divThumbPanel', NULL, 'ID='.$Model->hdnID) . ' ' . html::OnceClick
							)
			))
			?>
			<? if ($Model->FreshThumbID): ?>
				<div class="ThmbGnrl">
					<?= Tools\Cloudinary\Cloudinary::ThumbnailImageTag($Model->ThumbID, array('id' => 'imgPrjThumb')) ?>
				</div>
				<?= \CHtml::activeHiddenField($Model, 'hdnThumbCrop') ?>
	<script>
	_t.RunScriptAfterLoad('jcrop/jquery.Jcrop.min', function() {
		$('#imgPrjThumb').Jcrop({
				aspectRatio: 0
				, onSelect: function(c) {
					$('#<?= $Model->PostName ?>_hdnThumbCrop').attr('value', c.x + ',' + c.y + ',' + c.x2 + ',' + c.y2)
				}
				, onRelease: function(c) {
					$('#<?= $Model->PostName ?>_hdnThumbCrop').attr('value', '')
				}
			}
		<? if ($Model->drProject_Edit['ThumbnailCrop']): ?>
			, function() {
				this.animateTo([<?= $Model->drProject_Edit['ThumbnailCrop'] ?>])
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
							'rel' => \html::AjaxElement('#divThumbPanel', NULL, 'ID='.$Model->hdnID) . ' ' . html::OnceClick,
								)
				))
				?>
				<?=
				html::ButtonContainer(
						CHtml::button(\t2::site_site('Delete')
								, array(
							'name' => 'btnDeleteThumb',
							'rel' => \html::AjaxElement('#divThumbPanel', NULL, 'ID='.$Model->hdnID) . ' ' . html::OnceClick,
							'onclick' => \html::PostbackConfirm_OnClick('Are you sure?'),
								)
				))
				?>
			<? endif; ?>
	<? endif; ?>
</div>
