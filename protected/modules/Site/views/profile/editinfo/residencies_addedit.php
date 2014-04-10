<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
/* @var $wdgGeoLocation \Widgets\GeoLocationFields\GeoLocationFields */
?>
<div id="divEditResidency">
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
		<?= $Model->scenario == 'Edit' ? \CHtml::link(t2::Site_User('Add new'), \Site\Consts\Routes::User_EditResidencies()) : '' ?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?= $form->hiddenField($Model, 'hdnResidencyID') ?>
					<?
					$wdgGeoLocation->ActiveForm = $form;
					echo $wdgGeoLocation;
					?>
					<?=
					html::FieldContainer(
							$form->radioButtonList($Model, 'rdoResidencyStatus', $Model->arrResidencyStatuses)
//							, $form->labelEx($Model, 'rdoResidencyStatus')
//							, $form->error($Model, 'rdoResidencyStatus')
					)
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtVisaType')
							, $form->labelEx($Model, 'txtVisaType')
							, $form->error($Model, 'txtVisaType'))
					?>

					<?=
					html::ButtonContainer(
							CHtml::submitButton(\t2::Site_User($Model->scenario == 'Edit' ? 'Edit' : 'Add')
									, array(
								'name' => $Model->scenario == 'Edit' ? 'btnSaveEdit' : 'btnAdd',
								'rel' => \html::AjaxElement('#divEditInfo') . ' ' . \html::OnceClick
									)
					))
					?>
					<?=
					$Model->scenario == 'Edit' ?
							html::ButtonContainer(
									CHtml::button(\t2::Site_User('Delete')
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