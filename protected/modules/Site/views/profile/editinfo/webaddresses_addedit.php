<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Info */
/* @var $wdgGeoLocation \Widgets\GeoLocationFields\GeoLocationFields */
?>
<div id="divEditWebAddr">
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'formProfileInfo',
		'method' => 'POST',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
			))):
		/* @var $form Widgets\ActiveForm */
		?>
		<?= $Model->scenario == 'Edit' ? \CHtml::link(t2::site_site('Add new'), \Site\Consts\Routes::User_EditWebAddresses()) : '' ?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?= $form->hiddenField($Model, 'hdnWebAddrID') ?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtWebAddress')
							, $form->labelEx($Model, 'txtWebAddress')
							, $form->error($Model, 'txtWebAddress'))
					?>
					<?=
					\html::FieldContainer(
							\html::activeComboBox($Model, $form, 'ddlWebAddrType', $Model->arrWebAddrTypes
									, array('prompt' => 'select')
									, null
									, null
									, array('attribute' => 'txtCustomType')
							)
							, $form->labelEx($Model, 'ddlWebAddrType')
							, $form->error($Model, 'ddlWebAddrType')
					)
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