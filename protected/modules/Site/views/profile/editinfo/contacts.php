<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<table class="FullW OLst">
	<tr class="LstHdr">
		<td><?= $Model->getAttributeLabel('txtPhone') ?></td>
		<td><?= $Model->getAttributeLabel('ddlPhoneType') ?></td>
		<td><?= $Model->getAttributeLabel('chkIsPrivate') ?></td>
		<? if ($Model->asa('Info_Company')): ?>
			<td><?= $Model->getAttributeLabel('txtContactFirstName') ?></td>
			<td><?= $Model->getAttributeLabel('txtContactLastName') ?></td>
			<td><?= $Model->getAttributeLabel('txtContactMidName') ?></td>
		<? endif; ?>
		<td></td>
	</tr>
	<? foreach ($Model->dtFreshContacts as $dr): ?>
		<tr <?= html::AltRow()?>>
			<td><?= $dr['Phone'] ?></td>
			<td><?= $dr['PhoneType'] ?></td>
			<td><input type="checkbox" <?= $dr['IsPrivate'] ? "checked='checked'" : "" ?> disabled="disabled"/> </td>
			<? if ($Model->asa('Info_Company')): ?>
				<td><?= $dr['FirstName'] ?></td>
				<td><?= $dr['LastName'] ?></td>
				<td><?= $dr['JobTitle'] ?></td>
			<? endif; ?>
			<td>
				<?
				if ($form = $this->beginWidget('Widgets\ActiveForm', array(
					'id' => 'EditContact',
					'method' => 'POST',
						))):
					/* @var $form Widgets\ActiveForm */
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::Site_User('Edit')
									, array(
								'name' => 'btnEdit',
								'rel' => \html::AjaxElement('#divEditContact', NULL, "hdnContactID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
									)
					))
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::Site_User('Delete')
									, array(
								'name' => 'btnDelete',
								'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnContactID={$dr['CombinedID']}") . ' ' . html::OnceClick,
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
<? require 'contacts_addedit.php'; ?>
<? $this->endContent(); ?>
