<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<table class="FullW OLst">
	<tr class="LstHdr">
		<td><?= $Model->getAttributeLabel('txtEmail') ?></td>
		<td><?= t2::site_site('Pending email') ?></td>
		<td><?= $Model->getAttributeLabel('chkIsPrivate') ?></td>
		<td></td>
	</tr>
	<? foreach ($Model->dtFreshEmails as $dr): ?>
		<tr <?= html::AltRow()?>>
			<td><?= $dr['Email'] ?> <?= $dr['IsPrimary'] ? ' [' . t2::site_site('Primary') . ']' : '' ?> </td>
			<td><?= $dr['PendingEmail'] ?></td>
			<td><input type="checkbox" <?= $dr['IsPrivate'] ? "checked='checked'" : "" ?> disabled="disabled"/> </td>
			<td>
				<?
				if ($form = $this->beginWidget('Widgets\ActiveForm', array(
					'id' => 'formEditEmail',
					'method' => 'POST',
						))):
					/* @var $form Widgets\ActiveForm */
					?>
					<?=
					$dr['PendingEmail'] ?
							html::ButtonContainer(
									CHtml::button(\t2::site_site('Resend Activation Link')
											, array(
										'name' => 'btnResendActivationLink',
										'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnEmailID={$dr['CombinedID']}") . ' ' . html::OnceClick,
											)
							)) : ''
					?>
					<?=
					html::ButtonContainer(
							CHtml::button(\t2::site_site('Edit')
									, array(
								'name' => 'btnEdit',
								'rel' => \html::AjaxElement('#divEditEmail', NULL, "hdnEmailID={$dr['CombinedID']}") . \html::SimpleAjaxPanel,
									)
					))
					?>
					<?=
					!$dr['IsPrimary'] ?
							html::ButtonContainer(
									CHtml::button(\t2::site_site('Delete')
											, array(
										'name' => 'btnDelete',
										'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnEmailID={$dr['CombinedID']}") . ' ' . html::OnceClick,
										'onclick' => \html::PostbackConfirm_OnClick('Are you sure?'),
											)
							)) : ''
					?>
					<?=
					!$dr['IsPrimary'] && !$dr['PendingEmail'] ?
							html::ButtonContainer(
									CHtml::button(\t2::site_site('Set as primary')
											, array(
										'name' => 'btnPrimary',
										'rel' => \html::AjaxElement('#divEditInfo', NULL, "hdnEmailID={$dr['CombinedID']}") . ' ' . html::OnceClick,
										'onclick' => \html::PostbackConfirm_OnClick('Are you sure?'),
											)
							)) : ''
					?>
				<? endif; ?>
				<? $this->endWidget(); ?>
			</td>
		</tr>
	<? endforeach; ?>
</table>
<? require 'emails_addedit.php'; ?>
<? $this->endContent(); ?>
