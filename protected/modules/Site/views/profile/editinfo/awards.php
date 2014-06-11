<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Awards */
/* @var $dg \Base\DataGrid */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<?= $dg ?>
<?/*<table class="FullW OLst">
	<tr class="LstHdr">
		<td><?= $Model->getAttributeLabel('txtOrganizationTitle') ?></td>
		<td><?= $Model->getAttributeLabel('txtTitle') ?></td>
		<td><?= $Model->getAttributeLabel('ddlYear') ?></td>
		<td></td>
	</tr>
	<? foreach ($Model->dtFreshAwards as $dr): ?>

		<tr <?= html::AltRow() ?>>
			<td title="<?= $dr['OrganizationURL'] ?>"><?= $dr['OrganizationTitle'] ?></td>
			<td><?= $dr['Title'] ?></td>
			<td><?= $dr['Year'] ?></td>
			<td>
				<?
				if ($form = $this->beginWidget('Widgets\ActiveForm', array(
					'id' => 'formEditAwards',
					'method' => 'POST',
						))):
					/* @var $form Widgets\ActiveForm * /
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::site_site('Edit')
									, array(
								'name' => 'btnEdit',
								'rel' => \html::AjaxElement('#divEditAwards', NULL, "hdnAwardID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
									)
					))
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::site_site('Delete')
									, array(
								'name' => 'btnDelete',
								'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnAwardID={$dr['CombinedID']}") . ' ' . html::OnceClick,
								'onclick' => \html::PostbackConfirm_OnClick('Are you sure?'),
									)
					))
					?>
				<? endif; ?>
				<? $this->endWidget(); ?>
			</td>
		</tr>
	<? endforeach; ?>
</table>*/?>
<? require 'awards_addedit.php'; ?>
<? $this->endContent(); ?>
