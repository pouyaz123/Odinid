<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<? require 'emails_addedit.php'; ?>
<table class="FullW OLst">
	<tr class="LstHdr">
		<td><?= $Model->getAttributeLabel('txtEmail') ?></td>
		<td><?= t2::Site_User('Pending email') ?></td>
		<td></td>
	</tr>
	<? foreach ($Model->dtFreshEmails as $drEmail): ?>
		<tr>
			<td><?= $drEmail['Email'] ?></td>
			<td><?= $drEmail['PendingEmail'] ?></td>
			<td>
				<?
				if ($form = $this->beginWidget('Widgets\ActiveForm', array(
					'id' => 'EditEmail',
					'method' => 'POST',
						))):
					/* @var $form Widgets\ActiveForm */
					?>
					<?=
					$drEmail['PendingEmail'] ?
							html::ButtonContainer(
									CHtml::button(\t2::Site_User('Resend Activation Link')
											, array(
										'name' => 'btnResendActivationLink',
										'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnEmailID={$drEmail['CombinedID']}") . ' ' . html::OnceClick,
											)
							)) : ''
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::Site_User('Edit')
									, array(
								'name' => 'btnEdit',
								'rel' => \html::AjaxElement('#divEditEmail', NULL, "hdnEmailID={$drEmail['CombinedID']}") . \html::SimpleAjaxPanel,
									)
					))
					?>
					<?=
					!$drEmail['IsPrimary'] ?
							html::ButtonContainer(
									CHtml::button(\t2::Site_User('Delete')
											, array(
										'name' => 'btnDelete',
										'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnEmailID={$drEmail['CombinedID']}") . ' ' . html::OnceClick,
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
