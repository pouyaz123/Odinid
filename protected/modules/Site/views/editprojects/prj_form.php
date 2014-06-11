<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Projects\Projects */

use Site\models\Projects\Projects;
use Site\Consts\Routes;
?>
<div id="divEditProjects">
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'formPrj',
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
						, $Model->Type == Projects::Type_Blog ?
								Routes::User_EditBlog() :
								($Model->Type == Projects::Type_Project ?
										Routes::User_EditPrj() :
										Routes::User_EditTut()
								)
				) : ''
		?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?= $form->hiddenField($Model, 'hdnID') ?>
<?/*	hdnID;
	#
	txtTitle = 'Untitled project';
	txtSmallDesc;
	chkIsReel = 0;
	chkPaidTutorial = 0;
	ddlStatus;
	fileThumb;
	hdnThumbCrop;
	chkVisibility = 1;
	hdnCatIDs;
	chkShowInHome = 1;
	chkAdult = 0;
	txtPassword;
	ddlDividerLineType;
	txtContentSpacing;
	#
	txtWorkFields;
	txtTools;
	txtTags;
	txtSkills;
	#
	hdnSchoolIDs;
	txtSchools;
	#
	hdnCompanyIDs;
	txtCompanies;*/?>
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