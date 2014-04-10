<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<? require 'residencies_addedit.php'; ?>
<table class="FullW OLst">
	<tr class="LstHdr">
		<td><?= $Model->getAttributeLabel('ddlCountry') ?></td>
		<td><?= $Model->getAttributeLabel('rdoResidencyStatus') ?></td>
		<td></td>
	</tr>
	<? foreach ($Model->dtFreshResidencies as $dr): ?>
		<tr <?= html::AltRow() ?>>
			<td><?= $dr['Country'] ?></td>
			<td><?= $dr['ResidencyStatus'] . ($dr['VisaType'] ? "({$dr['VisaType']})" : "") ?></td>
			<td>
				<?
				if ($form = $this->beginWidget('Widgets\ActiveForm', array(
					'id' => 'EditResidency',
					'method' => 'POST',
						))):
					/* @var $form Widgets\ActiveForm */
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::Site_User('Edit')
									, array(
								'name' => 'btnEdit',
								'rel' => \html::AjaxElement('#divEditResidency', NULL, "hdnResidencyID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
									)
					))
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::Site_User('Delete')
									, array(
								'name' => 'btnDelete',
								'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnResidencyID={$dr['CombinedID']}") . ' ' . html::OnceClick,
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
<? $this->endContent(); ?>
