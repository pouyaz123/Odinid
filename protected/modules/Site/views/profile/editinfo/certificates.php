<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Certificates */
/* @var $dg \Base\DataGrid */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<?= $dg ?>
<table class="FullW OLst">
	<tr class="LstHdr">
		<td><?= $Model->getAttributeLabel('txtInstitutionTitle') ?></td>
		<td><?= $Model->getAttributeLabel('txtTitle') ?></td>
		<td><?= $Model->getAttributeLabel('txtDate') ?></td>
		<td><?= $Model->getAttributeLabel('ddlCountry') ?></td>
		<td></td>
	</tr>
	<? foreach ($Model->dtFreshCertificates as $dr): ?>

		<tr <?= html::AltRow() ?>>
			<td title="<?= $dr['InstitutionURL'] ?>"><?= $dr['InstitutionTitle'] ?></td>
			<td><?= $dr['Title'] ?></td>
			<td><?= $dr['Date'] ?></td>
			<td title="<?= $dr['City'] . ($dr['City'] && $dr['Division'] ? ' , ' : '') . $dr['Division'] ?>"><?= $dr['Country'] ?></td>
			<td>
				<?
				if ($form = $this->beginWidget('Widgets\ActiveForm', array(
					'id' => 'EditCertificates',
					'method' => 'POST',
						))):
					/* @var $form Widgets\ActiveForm */
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::site_site('Edit')
									, array(
								'name' => 'btnEdit',
								'rel' => \html::AjaxElement('#divEditCertificates', NULL, "hdnCertificateID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
									)
					))
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::site_site('Delete')
									, array(
								'name' => 'btnDelete',
								'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnCertificateID={$dr['CombinedID']}") . ' ' . html::OnceClick,
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
<? require 'certificates_addedit.php'; ?>
<? $this->endContent(); ?>
