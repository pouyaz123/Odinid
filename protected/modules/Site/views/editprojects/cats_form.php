<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Projects\Categories */

use Site\models\Projects\Categories;
use Site\Consts\Routes;
?>
<div id="divEditCategories">
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'formPrjCat',
		'method' => 'POST',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
			))):
		/* @var $form Widgets\ActiveForm */
		?>
		<?=
		$Model->scenario == 'Edit' ? \CHtml::link(t2::site_site('Add new')
						, $Model->Type == Categories::Type_Blog ?
								Routes::User_EditBlogCat() :
								($Model->Type == Categories::Type_Project ?
										Routes::User_EditPrjCat() :
										Routes::User_EditTutCat()
								)
				) : ''
		?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?= $form->hiddenField($Model, 'hdnID') ?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtTitle')
							, $form->labelEx($Model, 'txtTitle')
							, $form->error($Model, 'txtTitle'))
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