<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Educations */
/* @var $dg \Base\DataGrid */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<?= $dg ?>
<?/*<table class="FullW OLst">
	<tr class="LstHdr">
		<td><?= $Model->getAttributeLabel('txtSchoolTitle') ?></td>
		<td><?= $Model->getAttributeLabel('ddlCountry') ?></td>
		<td><?= $Model->getAttributeLabel('txtJobTitle') ?></td>
		<td><?= $Model->getAttributeLabel('txtFromDate') ?></td>
		<td><?= $Model->getAttributeLabel('txtToDate') ?></td>
		<td><?= $Model->getAttributeLabel('chkHealthInsurance') ?></td>
		<td><?= $Model->getAttributeLabel('chkRetirementAccount') ?></td>
		<td></td>
	</tr>
	<? foreach ($Model->dtFreshEducations as $dr): ?>

		<tr <?= html::AltRow() ?>>
			<td title="<?= $dr['SchoolURL'] ?>"><?= $dr['SchoolTitle'] ?></td>
			<td title="<?= $dr['City'] . ($dr['City'] && $dr['Division'] ? ' , ' : '') . $dr['Division'] ?>"><?= $dr['Country'] ?></td>
			<td title="<?=
			($dr['Level'] ? $Model->getAttributeLabel('ddlLevel') . ' : ' . $dr['Level'] . '<br/>' : '')
			. ($dr['EmploymentType'] ? $Model->getAttributeLabel('ddlEmploymentType') . ' : ' . $dr['EmploymentType'] . '<br/>' : '')
			. ($dr['SalaryType'] ? $Model->getAttributeLabel('ddlSalaryType') . ' : ' . $dr['SalaryType'] . '<br/>' : '')
			. ($dr['SalaryAmount'] ? $Model->getAttributeLabel('txtSalaryAmount') . ' : ' . $dr['SalaryAmount'] . '<br/>' : '')
			?>"><?= $dr['JobTitle'] ?></td>
			<td><?= $dr['FromDate'] ?></td>
			<td><?= $dr['ToPresent'] ? t2::site_site('Present') : $dr['ToDate'] ?></td>
			<td><input type="checkbox" <?= $dr['HealthInsurance'] ? "checked='checked'" : "" ?> disabled="disabled"/> </td>
			<td title="<?= $dr['RAPercent'] ? $Model->getAttributeLabel('txtRetirementPercent') . ' : ' . $dr['RAPercent'] . '%' : '' ?>"><input type="checkbox" <?= $dr['RetirementAccount'] ? "checked='checked'" : "" ?> disabled="disabled"/> </td>
			<td>
				<?
				if ($form = $this->beginWidget('Widgets\ActiveForm', array(
					'id' => 'formEditEducations',
					'method' => 'POST',
						))):
					/* @var $form Widgets\ActiveForm * /
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::site_site('Edit')
									, array(
								'name' => 'btnEdit',
								'rel' => \html::AjaxElement('#divEditEducations', NULL, "hdnEducationID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
									)
					))
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::site_site('Delete')
									, array(
								'name' => 'btnDelete',
								'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnEducationID={$dr['CombinedID']}") . ' ' . html::OnceClick,
								'onclick' => \html::PostbackConfirm_OnClick('Are you sure?'),
									)
					))
					?>
				<? endif; ?>
				<? $this->endWidget(); ?>
			</td>
		</tr>
	<? endforeach; ?>
</table>*/ ?>
<? require 'educations_addedit.php'; ?>
<? $this->endContent(); ?>
