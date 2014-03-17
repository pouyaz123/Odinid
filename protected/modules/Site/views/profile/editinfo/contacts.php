<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<? require 'contacts_addedit.php'; ?>
<table class="FullW OLst">
	<tr class="LstHdr">
		<td><?= $Model->getAttributeLabel('txtPhone') ?></td>
		<td><?= $Model->getAttributeLabel('ddlPhoneType') ?></td>
		<td><?= $Model->getAttributeLabel('txtEmail') ?></td>
		<td><?= t2::Site_User('Pending email') ?></td>
		<? if ($Model->asa('Info_Company')): ?>
			<td><?= $Model->getAttributeLabel('txtContactFirstName') ?></td>
			<td><?= $Model->getAttributeLabel('txtContactLastName') ?></td>
			<td><?= $Model->getAttributeLabel('txtContactMidName') ?></td>
		<? endif; ?>
		<td></td>
	</tr>
	<? foreach ($Model->dtContacts as $drContact): ?>
		<tr>
			<td><?= $drContact['Phone'] ?></td>
			<td><?= $drContact['PhoneType'] ?></td>
			<td><?= $drContact['Email'] ?></td>
			<td><?= $drContact['PendingEmail'] ?></td>
			<? if ($Model->asa('Info_Company')): ?>
				<td><?= $drContact['FirstName'] ?></td>
				<td><?= $drContact['LastName'] ?></td>
				<td><?= $drContact['JobTitle'] ?></td>
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
					$drContact['PendingEmail'] ?
							html::ButtonContainer(
									CHtml::button(\t2::Site_User('Resend Activation Link')
											, array(
										'name' => 'btnResendActivationLink',
										'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnContactID={$drContact['CombinedID']}") . ' ' . html::OnceClick,
											)
							)) : ''
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::Site_User('Edit')
									, array(
								'name' => 'btnEdit',
								'rel' => \html::AjaxElement('#divEditContact', NULL, "hdnContactID={$drContact['CombinedID']}") . \html::SimpleAjaxPanel,
									)
					))
					?>
					<?=
					!$drContact['IsPrimary'] ?
							html::ButtonContainer(
									CHtml::button(\t2::Site_User('Delete')
											, array(
										'name' => 'btnDelete',
										'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnContactID={$drContact['CombinedID']}") . ' ' . html::OnceClick,
										'onclick' => \html::PostbackConfirm_OnClick('Are you sure to delete it?'),
											)
							)) : ''
					?>
				<? endif; ?>
				<? $this->endWidget(); ?>
			</td>
		</tr>
	<? endforeach; ?>
</table>
<? $this->endContent(); ?>
