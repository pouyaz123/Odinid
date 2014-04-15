<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<table class="FullW OLst">
	<tr class="LstHdr">
		<td><?= $Model->getAttributeLabel('txtWebAddress') ?></td>
		<td><?= $Model->getAttributeLabel('ddlWebAddrType') ?></td>
		<td></td>
	</tr>
	<? foreach ($Model->dtFreshWebAddr as $dr): ?>
		<tr <?= html::AltRow() ?>>
			<td><a href="<?= $dr['WebAddress'] ?>"><?= $dr['WebAddress'] ?></a></td>
			<td><?= $dr['Type'] != 'Other' ? $dr['Type'] : $dr['CustomType'] ?></td>
			<td>
				<?
				if ($form = $this->beginWidget('Widgets\ActiveForm', array(
					'id' => 'EditWebAddr',
					'method' => 'POST',
						))):
					/* @var $form Widgets\ActiveForm */
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::Site_User('Edit')
									, array(
								'name' => 'btnEdit',
								'rel' => \html::AjaxElement('#divEditWebAddr', NULL, "hdnWebAddrID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
									)
					))
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::Site_User('Delete')
									, array(
								'name' => 'btnDelete',
								'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnWebAddrID={$dr['CombinedID']}") . ' ' . html::OnceClick,
								'onclick' => \html::PostbackConfirm_OnClick('Are you sure?'),
									)
					))
					?>
				<? endif; ?>
				<? $this->endWidget(); ?>
			</td>
		</tr>
	<? endforeach; ?>
</table>
<? require 'webaddresses_addedit.php'; ?>
<? $this->endContent(); ?>
