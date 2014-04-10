<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<? require 'locations_addedit.php'; ?>
<table class="FullW OLst">
	<tr class="LstHdr">
		<td><?= $Model->getAttributeLabel('ddlCountry') ?></td>
		<td><?= $Model->getAttributeLabel('ddlDivision') ?></td>
		<td><?= $Model->getAttributeLabel('ddlCity') ?></td>
		<td><?= $Model->getAttributeLabel('chkIsCurrentLocation') ?></td>
		<td><?= $Model->getAttributeLabel('chkIsBillingLocation') ?></td>
		<td></td>
	</tr>
	<? foreach ($Model->dtFreshLocations as $dr): ?>
		<tr <?= html::AltRow() ?> title="<?=
		($dr['Address1'] ? $Model->getAttributeLabel('txtAddress1') . ' : ' . $dr['Address1'] . '<br/>' : '')
		. ($dr['Address2'] ? $Model->getAttributeLabel('txtAddress2') . ' : ' . $dr['Address2'] . '<br/>' : '')
		. ($dr['PostalCode'] ? $Model->getAttributeLabel('txtPostalCode') . ' : ' . $dr['PostalCode'] . '<br/>' : '')
		?>">
			<td><?= $dr['Country'] ?></td>
			<td><?= $dr['Division'] ?></td>
			<td><?= $dr['City'] ?></td>
			<td><input type="checkbox" <?= $dr['IsCurrentLocation'] ? "checked='checked'" : "" ?> disabled="disabled"/> </td>
			<td><input type="checkbox" <?= $dr['IsBillingLocation'] ? "checked='checked'" : "" ?> disabled="disabled"/> </td>
			<td>
				<?
				if ($form = $this->beginWidget('Widgets\ActiveForm', array(
					'id' => 'EditLocation',
					'method' => 'POST',
						))):
					/* @var $form Widgets\ActiveForm */
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::Site_User('Edit')
									, array(
								'name' => 'btnEdit',
								'rel' => \html::AjaxElement('#divEditLocation', NULL, "hdnLocationID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
									)
					))
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::Site_User('Delete')
									, array(
								'name' => 'btnDelete',
								'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnLocationID={$dr['CombinedID']}") . ' ' . html::OnceClick,
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
