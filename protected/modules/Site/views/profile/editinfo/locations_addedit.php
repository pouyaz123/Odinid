<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
/* @var $wdgGeoLocation \Widgets\GeoLocationFields\GeoLocationFields */
?>
<div id="divEditLocation">
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'ProfileInfo',
		'method' => 'POST',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
			))):
		/* @var $form Widgets\ActiveForm */
		?>
		<?= $Model->scenario == 'Edit' ? \CHtml::link(t2::site_site('Add new'), \Site\Consts\Routes::User_EditLocations()) : '' ?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?= $form->hiddenField($Model, 'hdnLocationID') ?>
					<?
					$wdgGeoLocation->ActiveForm = $form;
					echo $wdgGeoLocation;
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtAddress1')
							, $form->labelEx($Model, 'txtAddress1')
							, $form->error($Model, 'txtAddress1'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtAddress2')
							, $form->labelEx($Model, 'txtAddress2')
							, $form->error($Model, 'txtAddress2'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtPostalCode')
							, $form->labelEx($Model, 'txtPostalCode')
							, $form->error($Model, 'txtPostalCode'))
					?>
					<?=
					html::FieldContainer(
							$form->checkBox($Model, 'chkIsCurrentLocation')
							, $form->labelEx($Model, 'chkIsCurrentLocation')
							, $form->error($Model, 'chkIsCurrentLocation'))
					?>
					<?=
					html::FieldContainer(
							$form->checkBox($Model, 'chkIsBillingLocation')
							, $form->labelEx($Model, 'chkIsBillingLocation')
							, $form->error($Model, 'chkIsBillingLocation'))
					?>

					<?=
					html::ButtonContainer(
							CHtml::submitButton(\t2::site_site($Model->scenario == 'Edit' ? 'Edit' : 'Add')
									, array(
								'name' => $Model->scenario == 'Edit' ? 'btnSaveEdit' : 'btnAdd',
								'rel' => \html::AjaxElement('#divEditInfo') . ' ' . \html::OnceClick
									)
					))
					?>
					<?=
					$Model->scenario == 'Edit' ?
							html::ButtonContainer(
									CHtml::button(\t2::site_site('Delete')
											, array(
										'name' => 'btnDelete',
										'rel' => \html::AjaxElement('#divEditInfo') . ' ' . html::OnceClick,
										'onclick' => \html::PostbackConfirm_OnClick('Are you sure?'),
											)
							)) : ''
					?>
				</td>
				<td class="BtmAlign">
					<?= $form->errorSummary($Model) ?>
				</td>
			</tr>
		</table>
	<? endif; ?>
	<? $this->endWidget(); ?>
</div>